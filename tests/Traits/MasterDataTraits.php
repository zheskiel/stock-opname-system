<?php
namespace Tests\Traits;

use App\Models\Master;

use Importer as C;

trait MasterDatatraits
{
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

    public function getStartKeyAndTitleArray($collection, $startKey = 0, $titleArr = "") : array
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

    public function getListData($collection, $titleArr, $startKey, $list = []) : array
    {
        $endKey = count($collection) - 1;

        $newCollection = array_slice($collection, $startKey, $endKey);

        foreach ($newCollection as $item) {
            for ($z=0; $z < count($item); $z++) {
                $dataList[$titleArr[$z]] = $item[$z];
            }

            // Include only items have Product Code
            // Include only items from Inventory & Non Inventory's Category Type
            // Exclude all other items
            if (
                $dataList['Product Code'] != '' &&
                ($dataList['Category Type'] == 'Inventory' ||
                $dataList['Category Type'] == 'Non Inventory')
            ) {
                $list[$item[0]][] = $dataList;
            }
        }

        return $list;
    }

    public function getUnitListData($listArr) : array
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

    public function getCollection()
    {
        $filePath = '/../../database/seeds/Master Product Data.xlsx';
        $file = __DIR__ . $filePath;

        $excel = C::make('Excel');
        $excel->load($file);

        return $excel->getCollection()->toArray();
    }

    public function createMasterData($listArr)
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

    public function initMasterData()
    {
        $collection = $this->getCollection();

        list($startKey, $titleArr) = $this->getStartKeyAndTitleArray($collection);

        $listData = $this->getListData($collection, $titleArr, $startKey);
        $listArr  = $this->getUnitListData($listData);

        $this->createMasterData($listArr);

        return $listArr;
    }
}