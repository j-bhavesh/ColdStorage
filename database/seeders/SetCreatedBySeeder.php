<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SetCreatedBySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Set the user id
        $defaultUserId = 2;

        // All tables with 'created_by' field
        $tables = [
            'farmers',
            'companies',
            'seed_varieties',
            'agreements',
            'seeds_bookings',
            'seed_distributions',
            'packaging_distributions',
            'advance_payments',
            'transporters',
            'cold_storages',
            'storage_loadings',
            'challans',
            'unloading_companies',
            'storage_unloadings',
        ];

        foreach ($tables as $table) {
            if (DB::table($table)->count() > 0) {
                DB::table($table)
                    ->whereNull('created_by')
                    ->update(['created_by' => $defaultUserId]);
            }
        }

        $this->command->info('✅ All created_by fields updated with user_id = ' . $defaultUserId);
    }
}
