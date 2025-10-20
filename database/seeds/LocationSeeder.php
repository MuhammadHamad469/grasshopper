<?php

use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run()
    {
        $items = [
            ['name' => 'Somerset', 'slug' => 'somerset'],
            ['name' => 'Cape Town', 'slug' => 'cape-town'],
            ['name' => 'London', 'slug' => 'london'],
        ];

        foreach ($items as $item) {
            \App\Models\Location::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }
    }
}