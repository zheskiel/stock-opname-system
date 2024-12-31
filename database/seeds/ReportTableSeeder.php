<?php

use App\Models\ {
    Reports
};

use App\Traits\HelpersTrait;
use Carbon\Carbon;

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
        $additionalFile = [
            [
                "name"  => "Tutup Botol",
                "unit"  => "PCS",
                "value" => "100",
                "file"  => "",
            ]
        ];

        $wasteFile = [
            [
                "name"  => "Telur Omega",
                "code"  => "BCK164",
                "unit"  => "GR",
                "value" => "100",
                "file"  => "",
            ],
            [
                "name"  => "HAITAI PEACH JUICE",
                "code"  => "BCK124",
                "unit"  => "KALENG",
                "value" => "1",
                "file"  => "",
            ],
            [
                "name"  => "TUSUKAN TELUR EGG HOLE PUNCHER",
                "code"  => "POT172",
                "unit"  => "PCS",
                "value" => "100",
                "file"  => "",
            ]
        ];

        $damageFile = [
            [
                "name"  => "HAITAI PEACH JUICE",
                "code"  => "BCK124",
                "unit"  => "KALENG",
                "value" => "1",
                "file"  => "",
            ]
        ];

        $additionalArr = [
            'name' => "Addition of Items",
            'items' => []
        ];

        $wasteArr = [
            'name' => "Item Waste",
            'items' => $wasteFile
        ];

        $damage = [
            'name' => "Damaged Goods",
            'items' => []
        ];

        $listItems = [
            '0' => [
                'items' => [
                    'additional' => $additionalArr,
                    'waste'      => $wasteArr,
                    'damage'     => $damage
                ],
                'notes' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book."
            ]
        ];


        foreach ($listItems as $list) {
            $items = $list['items'];
            $notes = $list['notes'];

            $this->reports->create([
                'date'       => Carbon::now()->isoFormat('YYYY-MM-DD'),
                'additional' => json_encode($items['additional']),
                'waste'      => json_encode($items['waste']),
                'damage'     => json_encode($items['damage']),
                'notes'      => $notes
            ]);
        }
    }
}