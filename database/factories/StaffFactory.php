<?php

use App\Models\Staff;
use Faker\Generator as Faker;

$factory->define(Staff::class, function (
    Faker $faker, $params = null
) {
    $name = $params['name'] ?? null;
    $slug = $params['slug'] ?? null;
    $manager_id = $params['manager_id'] ?? null;
    $supervisor_id = $params['supervisor_id'] ?? null;
    $staff_type_id = $params['staff_type_id'] ?? null;
    $sv_type_label = $params['sv_type_label'] ?? null;

    return [
        'name'          => $name,
        'slug'          => $slug,
        'manager_id'    => $manager_id,
        'supervisor_id' => $supervisor_id,
        'sv_type_label' => $sv_type_label,
        'staff_type_id' => $staff_type_id,
        'email'         => $slug . "@gmail.com",
        'password'      => bcrypt('test123'),
    ];
});
