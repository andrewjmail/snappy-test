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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();

            $table->string('uuid')->unique();
            $table->string('name', 50);
            $table->string('address')->nullable();

            $table->string('brand', 50)->nullable()->index();
            $table->decimal('delivery_radius_km', 5, 2)->default(5.00);

            $table->geography('location', subtype: 'point', srid: 4326);

            $table->datetime('active_at')->nullable();

            $table->timestamps();

            $table->spatialIndex('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
