<?php

namespace App\Console\Commands;

use App\Jobs\ProcessPostcodeBatch;
use App\Services\PostcodeImportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;

class ImportPostcodes extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'import:postcodes 
                            {file : Path to the CSV file} 
                            {--postcode-col : Optional name of the postcode column} 
                            {--lat-col= : Optional name of the latitude column} 
                            {--lon-col= : Optional name of the longitude column}';

    protected $description = 'Import postcodes from a CSV file';

    /**
     * Execute the console command.
     */
    public function handle(PostcodeImportService $service)
    {
        $filePath = $this->argument('file');

        if (! file_exists($filePath)) {
            $this->error("File not found: {$filePath}");

            return self::FAILURE;
        }

        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            $this->error("Failed to open file: {$filePath}. Check permissions.");

            return self::FAILURE;
        }

        try {
            $headers = fgetcsv($handle);

            $mapping = $service->determineColumnHeaders($headers, [
                'p' => $this->option('postcode-col') ?: 'pcd',
                'lat' => $this->option('lat-col') ?: 'lat',
                'lon' => $this->option('lon-col') ?: 'long',
            ]);

            $batch = $this->createBatch($filePath);

            $this->processFile($handle, $batch, $mapping, $service);

            $this->info('Jobs queued successfully');

            return self::SUCCESS;

        } catch (\Throwable $e) {
            $this->error('Import failed: '.$e->getMessage());

            return self::FAILURE;
        } finally {
            if (is_resource($handle)) {
                fclose($handle);
            }
        }
    }

    protected function createBatch(string $filePath)
    {
        return Bus::batch([])
            ->name('Postcode Import: '.$filePath)
            ->catch(fn ($batch, $e) => Log::error('Batch failed: '.$e->getMessage()))
            ->dispatch();
    }

    protected function processFile($handle, $batch, array $mapping, $service): void
    {
        LazyCollection::make(function () use ($handle) {
            while (($line = fgetcsv($handle)) !== false) {
                yield $line;
            }
        })
            ->chunk(2000)
            ->each(function ($rows) use ($batch, $mapping, $service) {
                $payload = $rows->map(fn ($row) => $service->formatRow($row, $mapping))->toArray();
                $batch->add(new ProcessPostcodeBatch($payload));
            });
    }
}
