<?php

use App\Models\StaffType;
use Faker\Generator as Faker;

$factory->define(StaffType::class, function (
    Faker $faker, $params = null
) {
    $title = $params['title'] ?? null;
    $slug = $params['slug'] ?? null;
    $supervisor_id = $params['supervisor_id'] ?? null;

    return [
        'title' => $title,
        'slug' => $slug,
        'supervisor_id' => $supervisor_id
    ];
});
