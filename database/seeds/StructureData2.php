<?php

$managers = [
    'manager 1',
    'manager 2',
    'manager 3',
    'manager 4',
    'manager 5',
    'manager 6'
];

$locationDetails = [
    0 => [
        'name' => 'Greenlake',
        'alias' => 'GLC',
        'outlet' => [
            0 => [
                'name' => 'GLC 1',
                'manager' => [
                    'name' => $managers[0],
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
            1 => [
                'name' => 'GLC 2',
                'manager' => [
                    'name' => $managers[0],
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
                    'name' => $managers[1],
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
                                        'title' => 'Vice Outlet Supervisor'
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
                    'name' => $managers[1],
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
                    'name' => $managers[2],
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
                    'name' => $managers[5],
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

return $structure;