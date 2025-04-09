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
        Schema::create('farmers', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('user_id')->nullable();
            
            $table->string('farmer_id', 20)->unique(); // Unique ID (e.g., San201)
            $table->string('name'); // Full name of the farmer
            $table->string('village_name'); // Village name
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farmers');
    }
};
