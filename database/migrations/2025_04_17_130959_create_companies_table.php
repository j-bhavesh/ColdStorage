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
        Schema::create('companies', function (Blueprint $table) {
            $table->id(); // id: bigint, auto increment, primary key
            $table->string('name', 255); // Company Name
            $table->string('contact_person', 255); // Contact Person Name
            $table->string('contact_number', 20); // Contact Number
            $table->text('address')->nullable(); // Company Address
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
