<?php

use App\Models\Staff;
use Faker\Generator as Faker;

$factory->define(Staff::class, function (
    Faker $faker, $params = null
) {
    $title = $params['title'] ?? null;
    $slug = $params['slug'] ?? null;
    $staff_id = $params['staff_id'] ?? null;
    $manager_id = $params['manager_id'] ?? null;
    $supervisor_id = $params['supervisor_id'] ?? null;
    $sv_type_label = $params['sv_type_label'] ?? null;

    return [
        'title'         => $title,
        'slug'          => $slug,
        'staff_id'      => $staff_id,
        'manager_id'    => $manager_id,
        'supervisor_id' => $supervisor_id,
        'sv_type_label' => $sv_type_label,
        'email'         => $slug . "@gmail.com",
        'password'      => bcrypt('test123'),
    ];
});
