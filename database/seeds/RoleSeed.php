<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->delete();

    // Reset the AUTO_INCREMENT value
    DB::statement('ALTER TABLE roles AUTO_INCREMENT = 1;');
        $items = [
            ['id'=> 1, 'title' => 'Super Administrator'],
            ['id'=> 2, 'title' => 'Administrator'],
            ['id'=> 3, 'title' => 'Manager'],
            ['id'=> 4, 'title' => 'Simple user'],
            ['id'=> 5, 'title' => 'Basic user']
];

        foreach ($items as $item) {
            \App\Models\Role::create($item);
        }
    }
}