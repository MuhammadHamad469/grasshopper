<?php

$factory->define(\App\Models\Product::class, function (Faker\Generator $faker) {
    return [
        "name" => $faker->name,
        "description" => $faker->name,
        "created_by_id" => factory(\App\Models\User::class)->create(),
        "created_by_team_id" => factory(\App\Models\Team::class)->create(),
    ];
});