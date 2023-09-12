<?php

use App\Models\Brand;
use App\Models\Province;

use Faker\Generator as Faker;

$factory->define(Province::class, function (Faker $faker, $params) {
    $provinceName = $params['name'];
    $provinceSlug = $params['slug'];

    return [
        'name' => $provinceName,
        'slug' => $provinceSlug,
        'brand_id'  => function () use ($faker) {
            $brandName = $faker->name;
            $brandSlug = strtolower(preg_replace('~[^\p{L}\p{N}\n]+~u', '-', $brandName));

            $brand = factory(Brand::class)->create([
                'name' => $brandName,
                'slug' => $brandSlug
            ]);
            
            return $brand->id;
        },
    ];
});

