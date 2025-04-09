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
        Schema::table('packaging_distributions', function (Blueprint $table) {
            //
            $table->integer('pending_bags')->nullable()->after('bag_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packaging_distributions', function (Blueprint $table) {
            //
            $table->dropColumn(['pending_bags']);
        });
    }
};
