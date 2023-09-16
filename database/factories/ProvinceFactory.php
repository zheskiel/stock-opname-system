<?php

use App\Models\Province;
use Faker\Generator as Faker;

$factory->define(Province::class, function (
    Faker $faker, $params = null
) {
    $name = $params['name'] ?? null;
    $slug = $params['slug'] ?? null;
    $brand_id = $params['brand_id'] ?? null;

    return [
        'name' => $name,
        'slug' => $slug,
        'brand_id' => $brand_id
    ];
});