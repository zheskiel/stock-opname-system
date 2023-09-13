<?php

use App\Models\Regency;


$factory->define(Regency::class, function ($name = null, $slug = null, $province_id = null) {
    return [
        'name' => $name,
        'slug' => $slug,
        'province_id' => $province_id
    ];
});
