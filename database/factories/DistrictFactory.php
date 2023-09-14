<?php

use App\Models\District;
use Faker\Generator as Faker;

$factory->define(District::class, function (
    Faker $faker, $params = null
) {
    $name = $params['name'] ?? null;
    $slug = $params['slug'] ?? null;
    $regency_id = $params['regency_id'] ?? null;

    return [
        'name' => $name,
        'slug' => $slug,
        'regency_id' => $regency_id
    ];
});
