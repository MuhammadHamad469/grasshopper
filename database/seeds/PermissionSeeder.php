<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
	public function run()
	{
		$permissions = [
			//sidebar
			['name' => 'can_user_management', 'label' => 'Can Manage Users'],
			['name' => 'can_access_projects', 'label' => 'Can Access Projects Tab'],
			['name' => 'can_access_finance', 'label' => 'Can Access Finance Tab'],
			['name' => 'can_access_assets', 'label' => 'Can Access Assets Tab'],
			['name' => 'can_access_smme', 'label' => 'Can Access Smme Tab'],
			['name' => 'can_access_data_config', 'label' => 'Can Access Data Config Tab'],

			// Projects types Permissions
			['name' => 'can_access_veg', 'label' => 'Can Access Vegetation Management Projects'],
			['name' => 'can_access_training', 'label' => 'Can Access Training Projects'],
			['name' => 'can_access_innovation', 'label' => 'Can Access Innovation Projects'],
			['name' => 'can_access_planning', 'label' => 'Can Access Planning Projects'],

			// Projects Permissions
			['name' => 'can_view_projects', 'label' => 'Can View  Projects'],
			['name' => 'can_edit_projects', 'label' => 'Can Edit  Projects'],
			['name' => 'can_delete_projects', 'label' => 'Can Delete  Projects'],
			['name' => 'can_create_projects', 'label' => 'Can Create  Projects'],

			// Invoices Permissions
			['name' => 'can_view_invoices', 'label' => 'Can View Invoices'],
			['name' => 'can_edit_invoices', 'label' => 'Can Edit Invoices'],
			['name' => 'can_delete_invoices', 'label' => 'Can Delete Invoices'],
			['name' => 'can_create_invoices', 'label' => 'Can Create Invoices'],

			// Quotes Permissions
			['name' => 'can_view_quotes', 'label' => 'Can View Quotes'],
			['name' => 'can_edit_quotes', 'label' => 'Can Edit Quotes'],
			['name' => 'can_delete_quotes', 'label' => 'Can Delete Quotes'],
			['name' => 'can_create_quotes', 'label' => 'Can Create Quotes'],

			// Assets Permissions
			['name' => 'can_view_assets', 'label' => 'Can View Assets'],
			['name' => 'can_edit_assets', 'label' => 'Can Edit Assets'],
			['name' => 'can_delete_assets', 'label' => 'Can Delete Assets'],
			['name' => 'can_create_assets', 'label' => 'Can Create Assets'],
		];

		foreach ($permissions as $permission) {
			$exists = DB::table('permissions')->where('name', $permission['name'])->exists();

			if (! $exists) {
				DB::table('permissions')->insert([
					'name' => $permission['name'],
					'label' => $permission['label'],
					'created_at' => now(),
					'updated_at' => now(),
				]);
			}
		}
	}
}