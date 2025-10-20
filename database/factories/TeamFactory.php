<?php

$factory->define(\App\Models\Team::class, function (Faker\Generator $faker) {
    return [
        "name" => $faker->name,
    ];
});