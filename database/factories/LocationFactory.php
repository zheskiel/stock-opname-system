<?php

use App\Models\Location;

$factory->define(Location::class, function ($name = null, $slug = null, $alias = null, $district_id = null) {
    return [
        'name' => $name,
        'slug' => $slug,
        'alias' =>  $alias,
        'district_id' => $district_id
    ];
});
