<?php

use App\Models\District;

$factory->define(District::class, function ($name = null, $slug = null, $regency_id = null) {
    return [
        'name' => $name,
        'slug' => $slug,
        'regency_id' => $regency_id
    ];
});
