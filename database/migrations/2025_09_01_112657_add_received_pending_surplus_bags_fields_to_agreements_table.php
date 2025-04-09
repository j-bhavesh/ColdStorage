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
        Schema::table('agreements', function (Blueprint $table) {
            //
            $table->integer('received_bags')->nullable()->after('bag_quantity');
            $table->integer('pending_bags')->nullable()->after('received_bags');
            $table->integer('surplus_bags')->nullable()->after('pending_bags');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agreements', function (Blueprint $table) {
            //
            $table->dropColumn(['received_bags', 'pending_bags', 'surplus_bags']);
        });
    }
};