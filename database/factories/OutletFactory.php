<?php

use App\Models\Outlet;
use Faker\Generator as Faker;

$factory->define(Outlet::class, function (
    Faker $faker, $params = null
) {
    $name = $params['name'] ?? null;
    $slug = $params['slug'] ?? null;
    $manager_id = $params['manager_id'] ?? null;
    $location_id = $params['location_id'] ?? null;

    return [
        'name' => $name,
        'slug' => $slug,
        'manager_id' => $manager_id,
        'location_id' => $location_id
    ];
});
