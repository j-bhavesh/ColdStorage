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
        // Modify 'farmers' table
        Schema::table('farmers', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('village_name');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // Modify 'companies' table
        Schema::table('companies', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('address');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // Modify 'seed_varieties' table
        Schema::table('seed_varieties', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('description');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // Modify 'agreements' table
        Schema::table('agreements', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('status');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // Modify 'seeds_bookings' table
        Schema::table('seeds_bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('status');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // Modify 'seed_distributions' table
        Schema::table('seed_distributions', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('received_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // Modify 'packaging_distributions' table
        Schema::table('packaging_distributions', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('received_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // Modify 'advance_payments' table
        Schema::table('advance_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('taken_by_name');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // Modify 'transporters' table
        Schema::table('transporters', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('contact_number');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // Modify 'cold_storages' table
        Schema::table('cold_storages', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('remarks');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // Modify 'storage_loadings' table
        Schema::table('storage_loadings', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('remarks');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // Modify 'challans' table
        Schema::table('challans', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('challan_number');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // Modify 'unloading_companies' table
        Schema::table('unloading_companies', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('status');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // Modify 'storage_unloadings' table
        Schema::table('storage_unloadings', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('weight');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert 'farmers' table
        Schema::table('farmers', function (Blueprint $table) {
            $table->dropColumn(['created_by']);
        });

        // Revert 'farmers' table
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['created_by']);
        });

        // Revert 'farmers' table
        Schema::table('seed_varieties', function (Blueprint $table) {
            $table->dropColumn(['created_by']);
        });

        // Revert 'agreements' table
        Schema::table('agreements', function (Blueprint $table) {
            $table->dropColumn(['created_by']);
        });

        // Revert 'seeds_bookings' table
        Schema::table('seeds_bookings', function (Blueprint $table) {
            $table->dropColumn(['created_by']);
        });

        // Revert 'seeds_bookings' table
        Schema::table('seed_distributions', function (Blueprint $table) {
            $table->dropColumn(['created_by']);
        });

        // Revert 'packaging_distributions' table
        Schema::table('packaging_distributions', function (Blueprint $table) {
            $table->dropColumn(['created_by']);
        });

        // Revert 'advance_payments' table
        Schema::table('advance_payments', function (Blueprint $table) {
            $table->dropColumn(['created_by']);
        });

        // Revert 'transporters' table
        Schema::table('transporters', function (Blueprint $table) {
            $table->dropColumn(['created_by']);
        });

        // Revert 'cold_storages' table
        Schema::table('cold_storages', function (Blueprint $table) {
            $table->dropColumn(['created_by']);
        });

        // Revert 'storage_loadings' table
        Schema::table('storage_loadings', function (Blueprint $table) {
            $table->dropColumn(['created_by']);
        });

        // Revert 'challans' table
        Schema::table('challans', function (Blueprint $table) {
            $table->dropColumn(['created_by']);
        });

        // Revert 'unloading_companies' table
        Schema::table('unloading_companies', function (Blueprint $table) {
            $table->dropColumn(['created_by']);
        });

        // Revert 'storage_unloadings' table
        Schema::table('storage_unloadings', function (Blueprint $table) {
            $table->dropColumn(['created_by']);
        });
    }
};
