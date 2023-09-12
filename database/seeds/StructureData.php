<?php
$svParams = [
    'Leader Kitchen',
    'Outlet Supervisor',
    'Head Production',
    'Central Kitchen Supervisor'
];


$locationDetails = [
    0 => [
        'name' => 'Greenlake',
        'alias' => 'GLC',
        'outlet' => [
            0 => [
                'name' => 'Central Kitchen',
                'manager' => [
                    'name' => 'manager 1',
                    'supervisor' => [
                        'level' => [
                            0 => [
                                // Head Production
                                'title'     => 'Head Production',
                                'assistant' => [
                                    0 => [
                                        'title' => 'Vice Head Production'
                                    ]
                                ],
                                'types' => [ // Staff Types
                                    0 => [
                                        'title' => 'Cook',
                                        'staff' => [
                                            0 => [
                                                'name' => 'Head Production - Cook - Staff 1',
                                            ],
                                            1 => [
                                                'name' => 'Head Production - Cook - Staff 2',
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            1 => [
                                // Central Kitchen Supervisor
                                'title' => 'Central Kitchen Supervisor',
                                'assistant' => [
                                    0 => [
                                        'title' => 'Vice Central Kitchen Supervisor'
                                    ]
                                ],
                                'types' => [ // Staff Types
                                    0 => [
                                        'title' => 'Production Staff',
                                        'staff' => [
                                            0 => [
                                                'name' => 'Central Kitchen Supervisor - Production Staff - Staff 1',
                                            ]
                                        ]
                                    ],
                                    1 => [
                                        'title' => 'Store Keeper',
                                        'staff' => [
                                            0 => [
                                                'name' => 'Central Kitchen Supervisor - Store Keeper - Staff 1',
                                            ],
                                            1 => [
                                                'name' => 'Central Kitchen Supervisor - Store Keeper - Staff 2',
                                            ]
                                        ]
                                    ]      
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            1 => [
                'name' => 'GLC 1',
                'manager' => [
                    'name' => 'manager 1',
                    'supervisor' => [
                        'level' => [
                            0 => [
                                'title'     => 'Leader Kitchen',
                                'assistant' => [
                                    0 => [
                                        'title' => 'Vice Leader Kitchen'
                                    ]
                                ],
                                'types' => [ // Staff Types
                                    0 => [
                                        'title' => 'Cook',
                                        'staff' => [
                                            0 => [
                                                'name' => 'Staff 100',
                                            ],
                                            1 => [
                                                'name' => 'Staff 200',
                                            ]
                                        ]
                                    ],
                                    1 => [
                                        'title' => 'Steward Kitchen',
                                        'staff' => [
                                            0 => [
                                                'name' => 'Staff 300',
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            1 => [
                                'title' => 'Outlet Supervisor',
                                'assistant' => [
                                    0 => [
                                        'title' => 'Vice Outlet Supervisor'
                                    ]
                                ],
                                'types' => [ // Staff Types
                                    0 => [
                                        'title' => 'Floor',
                                        'staff' => [
                                            0 => [
                                                'name' => 'Staff 400',
                                            ],
                                            1 => [
                                                'name' => 'Staff 500',
                                            ]
                                        ]
                                    ],
                                    1 => [
                                        'title' => 'Kitchen',
                                        'staff' => [
                                            0 => [
                                                'name' => 'Staff 600',
                                            ],
                                            1 => [
                                                'name' => 'Staff 700',
                                            ]
                                        ]
                                    ],
                                    2 => [
                                        'title' => 'Steward Outlet',
                                        'staff' => [
                                            0 => [
                                                'name' => 'Staff 800',
                                            ],
                                            1 => [
                                                'name' => 'Staff 900',
                                            ]
                                        ]
                                    ],
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            2 => [
                'name' => 'GLC 2',
                'manager' => [
                    'name' => 'Manager 1',
                    'supervisor' => [
                        'level' => [
                            0 => [
                                'title' => 'Outlet Supervisor',
                                'assistant' => [
                                    0 => [
                                        'title' => 'Vice Outlet Supervisor'
                                    ]
                                ],
                                'types' => [ // Staff Types
                                    0 => [
                                        'title' => 'Floor',
                                        'staff' => [
                                            0 => [
                                                'name' => 'Staff 450',
                                            ],
                                            1 => [
                                                'name' => 'Staff 550',
                                            ]
                                        ]
                                    ],
                                    1 => [
                                        'title' => 'Kitchen',
                                        'staff' => [
                                            0 => [
                                                'name' => 'Staff 650',
                                            ],
                                            1 => [
                                                'name' => 'Staff 750',
                                            ]
                                        ]
                                    ],
                                    2 => [
                                        'title' => 'Steward Outlet',
                                        'staff' => [
                                            0 => [
                                                'name' => 'Staff 850',
                                            ],
                                            1 => [
                                                'name' => 'Staff 950',
                                            ]
                                        ]
                                    ],
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    1 => [
        'name' => 'Alam Sutera',
        'alias' => 'Alsut',
        'outlet' => [
            0 => [
                'name' => 'Alsut 1',
                'manager' => [
                    'name' => 'manager 2',
                    'supervisor' => [
                        'level' => [
                            0 => [
                                'title' => 'Leader Kitchen',
                                'assistant' => [
                                    0 => [
                                        'title' => 'Vice Leader Kitchen'
                                    ]
                                ],
                                'types' => [ // Staff Types
                                    0 => [
                                        'title' => 'Cook',
                                        'staff' => [
                                            0 => [
                                                'name' => 'Staff 1',
                                            ]
                                        ]
                                    ],
                                    1 => [
                                        'title' => 'Steward Kitchen',
                                        'staff' => [
                                            0 => [
                                                'name' => 'Staff 3',
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            1 => [
                                'title' => 'Outlet Supervisor',
                                'assistant' => [
                                    0 => [
                                        'title' => 'Vice ' . 'Outlet Supervisor'
                                    ]
                                ],
                                'types' => [ // Staff Types
                                    0 => [
                                        'title' => 'Floor',
                                        'staff' => [
                                            0 => [
                                                'name' => 'Staff 4',
                                            ],
                                            1 => [
                                                'name' => 'Staff 5',
                                            ]
                                        ]
                                    ],
                                    1 => [
                                        'title' => 'Kitchen',
                                        'staff' => [
                                            0 => [
                                                'name' => 'Staff 6',
                                            ],
                                            1 => [
                                                'name' => 'Staff 7',
                                            ]
                                        ]
                                    ],
                                    2 => [
                                        'title' => 'Steward Outlet',
                                        'staff' => [
                                            0 => [
                                                'name' => 'Staff 8',
                                            ],
                                            1 => [
                                                'name' => 'Staff 9',
                                            ]
                                        ]
                                    ],
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            1 => [
                'name' => 'Alsut 2',
                'manager' => [
                    'name' => 'manager 2',
                ]
            ]
        ]
    ],
    2 => [
        'name' => 'Gading Serpong',
        'alias' => 'Serpong',
        'outlet' => [
            0 => [
                'name' => 'Serpong 1',
                'manager' => [
                    'name' => 'manager 3',
                    'supervisor' => [
                        'level' => [
                            0 => [
                                'title' => 'Leader Kitchen',
                                'assistant' => [
                                    0 => [
                                        'title' => 'Vice Leader Kitchen'
                                    ]
                                ],
                                'types' => [ // Staff Types
                                    0 => [
                                        'title' => 'Cook',
                                        'staff' => [
                                            0 => [
                                                'name' => 'Staff 20',
                                            ]
                                        ]
                                    ],
                                    1 => [
                                        'title' => 'Steward Kitchen',
                                        'staff' => [
                                            0 => [
                                                'name' => 'Staff 45',
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            1 => [
                'name' => 'Serpong 2',
                'manager' => [
                    'name' => 'manager 6',
                ]
            ]
        ]
    ],
];

$structure = [
    'brand' => [
        0 => [
            'name' => 'Furaidon',
            'province' => [
                0 => [
                    'name' => 'Banten',
                    'regency' => [
                        0 => [
                            'name' => 'Tangerang',
                            'district' => [
                                0 => [
                                    'name' => 'Cipondoh',
                                    'location' => $locationDetails
                                ],
                                1 => [
                                    'name' => 'Cilegon'
                                ]
                            ]
                        ]
                    ]
                ],
                1 => [
                    'name' => 'DKI Jakarta',
                    'regency' => [
                        0 => [
                            'name' => 'Kepulauan Seribu',
                            'district' => [
                                0 => [
                                    'name' => 'Kepulauan Seribu Utara'
                                ]
                            ]
                        ],
                        1 => [
                            'name' => 'Jakarta Pusat',
                            'district' => []
                        ],
                        2 => [
                            'name' => 'Jakarta Utara',
                            'district' => []
                        ],
                        3 => [
                            'name' => 'Jakarta Barat',
                            'district' => []
                        ],
                        4 => [
                            'name' => 'Jakarta Selatan',
                            'district' => []
                        ],
                        5 => [
                            'name' => 'Jakarta Timur',
                            'district' => []
                        ]
                    ]
                ]
            ]
        ]
    ]
];

return [
    $svParams,
    $structure
];