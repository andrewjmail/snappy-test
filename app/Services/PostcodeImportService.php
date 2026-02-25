<?php

namespace App\Services;

class PostcodeImportService
{
    /**
     * Map headers to their respective column indices.
     */
    public function determineColumnHeaders(array $headers, array $colNames): array
    {
        $headers = array_map(fn ($h) => strtolower(trim($h)), $headers);
        $map = [];

        foreach ($colNames as $key => $colName) {
            $index = array_search(strtolower($colName), $headers);

            if ($index === false) {
                throw new \InvalidArgumentException("Column '{$colName}' not found in CSV.");
            }

            $map[$key] = $index;
        }

        return $map;
    }

    public function formatRow(array $row, array $map): ?array
    {
        $data = [
            'p' => strtoupper(str_replace(' ', '', $row[$map['p']])),
            'lat' => (float) $row[$map['lat']],
            'lon' => (float) $row[$map['lon']],
        ];

        if (! $this->isValidRow($data)) {
            return null;
        }

        return $data;
    }

    protected function isValidRow(array $data): bool
    {
        // Validate length
        $len = strlen($data['p']);
        if ($len < 5 || $len > 8) {
            return false;
        }

        if ($data['lat'] < -90 || $data['lat'] > 90 || $data['lon'] < -180 || $data['lon'] > 180) {
            return false;
        }

        // Discard as probably invalid if both lat and lon are exactly zero
        if ((float) $data['lat'] === 0.0 && (float) $data['lon'] === 0.0) {
            return false;
        }

        return true;
    }
}
