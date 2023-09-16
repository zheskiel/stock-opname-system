<?php

use App\Models\Brand;
use Faker\Generator as Faker;

$factory->define(Brand::class, function (
    Faker $faker, $params = null
) {
    $name = $params['name'] ?? null;
    $slug = $params['slug'] ?? null;

    return [
        'name' => $name,
        'slug' => $slug,
        'admin_id' => rand(1, 5)
    ];
});