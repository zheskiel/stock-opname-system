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

// $factory->define(Province::class, function (Faker $faker, $params) {
//     $provinceName = $params['name'];
//     $provinceSlug = $params['slug'];

//     return [
//         'name' => $provinceName,
//         'slug' => $provinceSlug,
//         'brand_id'  => function () use ($faker) {
//             $brandName = $faker->name;
//             $brandSlug = strtolower(preg_replace('~[^\p{L}\p{N}\n]+~u', '-', $brandName));

//             return factory(Brand::class)->create([
//                 'name' => $brandName,
//                 'slug' => $brandSlug
//             ])->id;
//         },
//     ];
// });