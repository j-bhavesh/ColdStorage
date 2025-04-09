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
        Schema::create('storage_unloadings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('unloading_companies')->onDelete('cascade');
            $table->foreignId('cold_storage_id')->constrained('cold_storages')->onDelete('cascade');
            $table->foreignId('transporter_id')->constrained('transporters')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('seed_variety_id')->constrained('seed_varieties')->onDelete('cascade');
            $table->string('rst_no', 50);
            $table->string('chamber_no', 50);
            $table->string('location', 50);
            $table->integer('bag_quantity');
            $table->decimal('weight', 10, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_unloadings');
    }
}; 