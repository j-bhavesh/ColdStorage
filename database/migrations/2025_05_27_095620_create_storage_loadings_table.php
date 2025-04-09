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
        Schema::create('storage_loadings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agreement_id')->constrained('agreements')->onDelete('cascade');
            $table->foreignId('transporter_id')->constrained('transporters')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('cold_storage_id')->constrained('cold_storages')->onDelete('cascade');
            $table->string('rst_number', 50)->nullable();
            $table->string('chamber_no', 50)->nullable();
            $table->integer('bag_quantity');
            $table->decimal('net_weight', 10, 2);
            $table->integer('extra_bags')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_loadings');
    }
};
