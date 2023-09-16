<?php

use App\Models\Supervisor;
use Faker\Generator as Faker;

$factory->define(Supervisor::class, function (
    Faker $faker, $params = null
) {
    $title = $params['title'] ?? null;
    $slug = $params['slug'] ?? null;
    $duty = $params['duty'] ?? null;
    $outlet_id = $params['outlet_id'] ?? null;
    $manager_id = $params['manager_id'] ?? null;
    $supervisor_type_id = $params['supervisor_type_id'] ?? null;

    return [
        'title' => $title,
        'slug' => $slug,
        'duty' => $duty,
        'outlet_id' => $outlet_id,
        'manager_id' => $manager_id,
        'supervisor_type_id' => $supervisor_type_id,
    ];
});
