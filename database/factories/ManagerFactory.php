<?php

use App\Models\Manager;
use Faker\Generator as Faker;

$factory->define(Manager::class, function (
    Faker $faker, $params = null
) {
    $name = $params['name'] ?? null;
    $slug = $params['slug'] ?? null;

    return [
        'name' => $name,
        'slug' => $slug,
        'email' => "testing-$slug@gmail.com",
        'password' => 'test123'
    ];
});
