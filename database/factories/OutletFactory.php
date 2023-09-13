<?php

use App\Models\Outlet;

$factory->define(Outlet::class, function ($name = null, $slug = null, $location_id = null) {
    return [
        'name' => $name,
        'slug' => $slug,
        'location_id' => $location_id
    ];
});
