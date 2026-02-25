<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('postcodes', function (Blueprint $table) {
            $table->id();

            $table->string('postcode', 10)->unique();

            // https://github.com/tarfin-labs/laravel-spatial
            $table->geography('location', subtype: 'point');

            $table->timestamps();

            $table->spatialIndex('location');
        });
    }
};
