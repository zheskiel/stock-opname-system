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
        $additionalArr = [
            'name' => "Penambahan Barang",
            'items' => [
                [
                    "name"  => "Tutup Botol",
                    "unit"  => "pieces",
                    "value" => "100",
                    "file"  => "",
                ]
            ]
        ];

        $wasteArr = [
            'name' => "Waste",
            'items' => [
                [
                    "name"  => "Telur Ayam",
                    "code"  => "kode",
                    "unit"  => "gram",
                    "value" => "100",
                    "file"  => "",
                ],
                [
                    "name"  => "Telur Bebek",
                    "code"  => "kode",
                    "unit"  => "gram",
                    "value" => "100",
                    "file"  => "",
                ]
            ]
        ];

        $damage = [
            'name' => "Kerusakan Barang",
            'items' => [
                [
                    "name"  => "Teflon",
                    "code"  => "kode",
                    "unit"  => "pieces",
                    "value" => "1",
                    "file"  => "",
                ]
            ]
        ];

        $listItems = [
            '0' => [
                'items' => [
                    'additional' => $additionalArr,
                    'waste'      => $wasteArr,
                    'damage'     => $damage
                ],
                'notes' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum."
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