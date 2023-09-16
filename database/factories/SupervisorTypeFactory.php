<?php

use App\Models\SupervisorType;
use Faker\Generator as Faker;

$factory->define(SupervisorType::class, function (
    Faker $faker, $params = null
) {
    $title = $params['title'] ?? null;
    $slug = $params['slug'] ?? null;
    $duty = $params['duty'] ?? null;

    return [
        'title' => $title,
        'slug' => $slug,
        'duty' => $duty
    ];
});
