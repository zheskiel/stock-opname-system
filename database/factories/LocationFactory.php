<?php

use App\Models\Location;
use Faker\Generator as Faker;

$factory->define(Location::class, function (
    Faker $faker, $params = null
) {
    $name = $params['name'] ?? null;
    $slug = $params['slug'] ?? null;
    $alias = $params['alias'] ?? null;
    $district_id = $params['district_id'] ?? null;

    return [
        'name' => $name,
        'slug' => $slug,
        'alias' =>  $alias,
        'district_id' => $district_id
    ];
});
