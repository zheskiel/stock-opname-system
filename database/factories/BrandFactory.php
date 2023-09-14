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

// $factory->afterMaking(Brand::class, function ($brand) {
//     factory(Province::class)->create([
//         // 'name' => 'DKI Jakarta',
//         // 'slug' => 'dki-jakarta',
//         'brand_id' => $brand->id
//     ]);
// });