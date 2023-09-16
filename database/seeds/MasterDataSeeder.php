<?php

use App\Services\ {
    MasterDataService
};

use Importer as C;

class MasterDataSeeder extends BaseSeeder
{
    private $masterDataService;
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

    public function __construct(
        MasterDataService $masterDataService
    ) {
        $this->masterDataService = $masterDataService;
    }

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

        foreach ($newCollection as $item) {
            for ($z = 0; $z < count($item); $z++) {
                $dataList[$titleArr[$z]] = $item[$z];
            }

            // Include only items from Inventory & Non Inventory's Category Type
            // Exclude all other items
            if (
                $dataList['Category Type'] == 'Inventory' ||
                $dataList['Category Type'] == 'Non Inventory'
            ) {
                $dataList['owned'] = rand(1, 3);

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

    private function getCollection() : array
    {
        $filepath = __DIR__ . '/Master Product Data.xlsx';

        $excel = C::make('Excel');
        $excel->load($filepath);

        return $excel->getCollection()->toArray();
    }

    private function createMasterData($listArr) : void
    {
        foreach($listArr as $data) {
            $this->masterDataService->createData($data);
        }
    }

    public function run()
    {
        $collection = $this->getCollection();

        list($startKey, $titleArr) = $this->getStartKeyAndTitleArray($collection);

        $listData = $this->getListData($collection, $titleArr, $startKey);
        $listArr  = $this->getUnitListData($listData);

        $this->createMasterData($listArr);
    }
}
