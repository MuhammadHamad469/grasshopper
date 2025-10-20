<?php

use Illuminate\Database\Seeder;
use App\Models\ProjectType;

class ProjectTypeSeeder extends Seeder
{
	public function run()
	{
		$items = [
			['name' => 'Vegetation Management', 'slug' => 'vegetation-management'],
			['name' => 'Training', 'slug' => 'training'],
			['name' => 'Innovation', 'slug' => 'innovation'],
			['name' => 'Consulting', 'slug' => 'consulting'],
			['name' => 'SMME Development', 'slug' => 'smme-development'],
			['name' => 'Other', 'slug' => 'other'],
		];

		foreach ($items as $item) {
			ProjectType::firstOrCreate(
				['slug' => $item['slug']], 
				['name' => $item['name']]  
			);
		}
	}
}