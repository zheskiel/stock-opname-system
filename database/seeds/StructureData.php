<?php

$brand = [
    0 => 'Brand', // current
    1 => 1,       // limit
    2 => []       // params
];

$provinces = [
    0 => 'Province', // current
    1 => 1,         // limit
    2 => [
        'brand'
    ]
];

$regency = [
    0 => 'Regency',  // current
    1 => 1,         // limit
    2 => [
        'province'
    ]
];

$district = [
    0 => 'District',  // current
    1 => 1,         // limit
    2 => [
        'regency'
    ]
];

$location = [
    0 => 'Location',  // current
    1 => 1,         // limit
    2 => [
        'district'
    ]
];


$outlet = [
    0 => 'Outlet',   // current
    1 => 1,       // limit
    2 => [
        'location'
    ]
];

$manager = [
    0 => 'Manager',    // current
    1 => 1,          // limit
    2 => [
        'outlet'
    ]
];

$supervisor = [
    0 => 'Supervisor', // current
    1 => 1,          // limit
    2 => [             // params
        'outlet',
        'manager'
    ]
];

$staffType = [
    0 => 'Type',     // current
    1 => 1,        // limit
    2 => [           // params
        'supervisor'
    ]
];

$staff = [
    0 => 'Staff', // current
    1 => 25,    // limit
    2 => [        // params
        'type'
    ]
];


return [
    $brand,
    $provinces,
    $regency,
    $district,
    $location,
    $outlet,
    $manager,
    $supervisor,
    $staffType,
    $staff
];