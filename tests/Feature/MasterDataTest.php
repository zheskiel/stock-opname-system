<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Traits\HelpersTrait;

use App\Models\ {
    Master
};

use Importer as C;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;
    use HelpersTrait;

    private $unsetLists = [
        'Unit',
        'Qty',
        'Flag Default',
        'Flag Base Unit',
        'Flag Default Purchase',
        'Flag Transfer Unit',
        'Flag Sales Unit',
        'Barcode Number'
    ];

    private function getStartKeyAndTitleArray($collection, $startKey = 0, $titleArr = "") : array
    {
        foreach ($collection as $key => $item) {
            if ($item[0] == 'Product ID') {
                $startKey = $key;
                $titleArr = $item;

                break;
            }
        }

        return [$startKey, $titleArr];
    }

    private function getListData($collection, $titleArr, $startKey, $list = []) : array
    {
        $endKey = count($collection) - 1;

        $newCollection = array_slice($collection, $startKey, $endKey);

        foreach ($newCollection as $key => $item) {
            for ($z=0; $z < count($item); $z++) {
                $dataList[$titleArr[$z]] = $item[$z];
            }

            // Include only items from Inventory & Non Inventory's Category Type
            // Exclude all other items
            if (
                $dataList['Category Type'] == 'Inventory' ||
                $dataList['Category Type'] == 'Non Inventory'
            ) {
                $list[$item[0]][] = $dataList;
            }
        }

        return $list;
    }

    private function getUnitListData($listArr) : array
    {
        foreach($listArr as $l => $items) {
            $unitList = [];

            foreach($items as $item) {
                $unitList[ $item['Unit'] ] = [
                    'value'                 => $item['Qty'],
                    'barcode_number'        => $item['Barcode Number'],
                    'flag_base_unit'        => $item['Flag Base Unit'],
                    'flag_default'          => $item['Flag Default'],
                    'flag_default_purchase' => $item['Flag Default Purchase'],
                    'flag_transfer_unit'    => $item['Flag Transfer Unit'],
                    'flag_sales_unit'       => $item['Flag Sales Unit'],
                ];
            }

            $newData = array_merge($listArr[$l][0], [ 'units' => $unitList ]);
            $newData['units'] = json_encode($newData['units']);

            // Remove arrays of data
            array_diff($newData, $this->unsetLists);

            $listArr[$l] = $newData;
        }

        return $listArr;
    }

    private function getCollection()
    {
        $filePath = '/../../database/seeds/Master Product Data.xlsx';
        $file = __DIR__ . $filePath;

        $excel = C::make('Excel');
        $excel->load($file);

        return $excel->getCollection()->toArray();
    }

    private function createMasterData($listArr)
    {
        foreach($listArr as $data) {
            $params = [
                'product_id'        => $data["Product ID"],
                'category'          => $data["Category"],
                'subcategory'       => $data["Subcategory"],
                'category_type'     => $data["Category Type"],
                'bom_name'          => $data["BOM Name"],
                'product_code'      => $data["Product Code"],
                'product_name'      => $data["Product Name"],
                'base_price'        => $data["Base Price"],
                'requestable'       => $data["Requestable"],
                'receipt_tolerance' => $data["Receipt Tolerance (%)"],
                'saleable'          => $data["Saleable(YES/NO)"],
                'notes'             => $data["Notes"],
                'vat'               => $data["VAT"],
                'status_uom'        => $data["Status UOM"],
                'formula'           => $data["Formula Of These Menu"],
                'units'             => $data['units']
            ];

            Master::firstOrCreate([
                'product_id' => $data["Product ID"]
            ], $params);
        };
    }

    private function initMasterData()
    {
        $collection = $this->getCollection();

        list($startKey, $titleArr) = $this->getStartKeyAndTitleArray($collection);

        $listData = $this->getListData($collection, $titleArr, $startKey);
        $listArr  = $this->getUnitListData($listData);

        $this->createMasterData($listArr);

        return [$listArr];
    }

    private $listArr;
    private $master;

    public function setup() : void
    {
        parent::setUp();

        list($listArr) = $this->initMasterData();

        $this->listArr = $listArr;
    }

    public function testMasterDataCreation()
    {
        $master = Master::first();
        $currentItem = $this->listArr[$master->product_id];

        $this->assertEquals($currentItem['Product ID'], $master->product_id);
        $this->assertEquals($currentItem['Category'], $master->category);
    }

    public function IDProviders()
    {
        $ids = [];

        for ($x = 2; $x <= 10; $x++) $ids[] = [ $x ];

        return $ids;
    }

    /**
     * @dataProvider IDProviders
     */
    public function testMasterDataCreationWithAnyRandomId($id)
    {
        $master = Master::where('id', $id)->first();

        $currentItem = $this->listArr[$master->product_id];

        $this->assertEquals($currentItem['Product ID'], $master->product_id);
        $this->assertEquals($currentItem['Category'], $master->category);
    }

    /**
     * @dataProvider IDProviders
     */
    public function testMasterDataUnitsIsSortedByDescending($id)
    {
        $master = Master::where('id', $id)->first();
        $units = json_decode($master['units'], true);

        $totalUnits = count($units);
        $unitKeys = array_keys($units);

        if (!($totalUnits > 0)) return;

        for ($x=0; $x < $totalUnits; $x++) {
            if ($x > 0) {
                $now = $units[$unitKeys[$x]]['value'];
                $prev = $units[$unitKeys[$x - 1]]['value'];

                $isGreater = $prev >= $now;

                $this->assertTrue($isGreater);
            }
        }
    }
}
