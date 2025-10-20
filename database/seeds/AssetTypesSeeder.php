<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssetTypesSeeder extends Seeder
{
    public function run()
    {
        $items = [
            ['name' => 'Vehicle'],
            ['name' => 'Equipment'],
            ['name' => 'Computer'],
            ['name' => 'Furniture'],
            ['name' => 'Building'],
        ];

        foreach ($items as $item) {
            DB::table('asset_types')->updateOrInsert(
                ['name' => $item['name']],
                ['name' => $item['name'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}