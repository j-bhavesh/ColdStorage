<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('seed_distributions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('seeds_booking_id')
                ->constrained('seeds_booking')
                ->onDelete('cascade');

            $table->foreignId('farmer_id')
                ->constrained('farmers')
                ->onDelete('restrict');

            $table->foreignId('seed_variety_id')
                ->constrained('seed_varieties')
                ->onDelete('restrict');

            $table->foreignId('company_id')
                ->constrained('companies')
                ->onDelete('restrict');

            $table->integer('bag_quantity')->unsigned();

            $table->date('distribution_date');

            $table->string('vehicle_number', 50)->nullable();

            $table->string('received_by', 50)->nullable();

            $table->timestamps();
            $table->softDeletes(); // For recovery if accidentally deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seed_distributions');
    }
};
