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
        Schema::create('seeds_booking', function (Blueprint $table) {
            
            $table->id();

            $table->foreignId('farmer_id')
                ->constrained('farmers')
                ->onDelete('restrict');

            $table->foreignId('company_id')
                ->constrained('companies')
                ->onDelete('restrict');

            $table->foreignId('seed_variety_id')
                ->constrained('seed_varieties')
                ->onDelete('restrict');

            // $table->decimal('rate', 10, 2);
            $table->enum('booking_type',['cash','debit'])->default('debit');
            $table->decimal('booking_amount', 10, 2)->default(0.00);

            $table->integer('bag_quantity');

            $table->enum('status', ['active', 'completed', 'rejected', 'hold'])->default('active');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seeds_booking');
    }
};
