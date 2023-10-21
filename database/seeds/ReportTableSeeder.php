<?php

use App\Models\ {
    Reports
};

use App\Traits\HelpersTrait;

class ReportTableSeeder extends BaseSeeder
{
    use HelpersTrait;

    private $reports;

    public function  __construct(
        Reports $reports
    ) {
        $this->reports = $reports;
    }

    public function  run()
    {
        $listItems = [
            '0' => [
                'items' => [
                    'additional' => [
                        'name' => "Penambahan Barang",
                        'items' => [
                            [
                                "name" => "Tutup Botol",
                                "unit" => "pieces",
                                "value" => "100",
                                "file" => "",
                            ]
                        ]
                    ],
                    'waste' => [
                        'name' => "Waste",
                        'items' => [
                            [
                                "name" => "Telur Ayam",
                                "code" => "kode",
                                "unit" => "gram",
                                "value" => "100",
                                "file" => "",
                            ],
                            [
                                "name" => "Telur Bebek",
                                "code" => "kode",
                                "unit" => "gram",
                                "value" => "100",
                                "file" => "",
                            ]
                        ]
                    ],
                    'damage' => [
                        'name' => "Kerusakan Barang",
                        'items' => [
                            [
                                "name" => "Teflon",
                                "code" => "kode",
                                "unit" => "pieces",
                                "value" => "1",
                                "file" => "",
                            ]
                        ]
                    ]
                ],
                'notes' => "testing"
            ]
        ];


        foreach ($listItems as $list) {
            $items = $list['items'];
            $notes = $list['notes'];

            $this->reports->create([
                'date'       => '2023-10-21',
                'additional' => json_encode($items['additional']),
                'waste'      => json_encode($items['waste']),
                'damage'     => json_encode($items['damage']),
                'notes'      => $notes
            ]);
        }
    }
}