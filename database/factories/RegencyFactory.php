<?php

use App\Models\Regency;
use Faker\Generator as Faker;

$factory->define(Regency::class, function (
    Faker $faker, $params = null
) {
    $name = $params['name'] ?? null;
    $slug = $params['slug'] ?? null;
    $province_id = $params['province_id'] ?? null;

    return [
        'name' => $name,
        'slug' => $slug,
        'province_id' => $province_id
    ];
});
