<?php

namespace App\Http\Controllers;

use Importer as C;
use Illuminate\Http\Request;

use App\Models\Master;

class IndexController extends BaseController
{
    public function __construct(
        Master $master
    ) {
        $this->master = $master;
    }

    public function Test(Request $request)
    {
        $model = $this->master;

        $page = (int) $request->get('page', 1);

        $total = $model->count();
        $query = $model
            ->limit($this->limit)
            ->offset($this->limit * ($page - 1))
            ->get();

        $data = $query
            ->map(function($query) {
                $units = json_decode($query->units, true);

                uasort($units, function ($item1, $item2) {
                    return $item2['value'] <=> $item1['value'];
                });

                $query->units = $units;

                return $query;
            });

        $result = $this->generatePagination($data, $total, $this->limit, $page);

        return response()->json($result);
    }

    private function getStartKeyAndTitleArray($collection, $startKey = 0)
    {
        foreach ($collection as $key => $item) {
            if ($item[0] == 'Product ID') {
                $startKey = $key;
                $titleArr = $item;
            }
        }

        return [$startKey, $titleArr];
    }

    private function getCollection() : array
    {
        $filepath = __DIR__ . '/Master Product Data - 20230815142421.xlsx';

        $excel = C::make('Excel');
        $excel->load($filepath);

        return $excel->getCollection()->toArray();
    }

    public function Index()
    {
        $collection = $this->getCollection();

        $list = [];
        $endKey = count($collection) - 1;
        
        list($startKey, $titleArr) = $this->getStartKeyAndTitleArray($collection);

        // echo "Start Key $startKey - End Key $endKey\n\n";

        $collection = array_slice($collection, $startKey, $endKey);

        foreach ($collection as $key => $item) {
            for ($z=0; $z < count($item); $z++) {
                $dataList[$titleArr[$z]] = $item[$z];
            }

            if (
                $dataList['Category Type'] == 'Inventory' ||
                $dataList['Category Type'] == 'Non Inventory'
            ) {
                $list[$item[0]][] = $dataList;
            }
        }

        foreach($list as $l => $items) {
            $unitList = [];

            foreach($items as $i => $item) {
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

            arsort( $unitList );

            $newData = array_merge($list[$l][0], [
                'units' => $unitList
            ]);

            $unsetLists = [
                'Unit',
                'Qty',
                'Flag Default',
                'Flag Base Unit',
                'Flag Default Purchase',
                'Flag Transfer Unit',
                'Flag Sales Unit',
                'Barcode Number'
            ];

            foreach($unsetLists as $target) {
                unset($newData[$target]);
            }

            $list[$l] = $newData;
        }

        usort($list, function ($item1, $item2) {
            return $item1['Product Name'] <=> $item2['Product Name'];
        });

        foreach($list as $k => $item) {
            // echo "\nCreate data for $k\n\n\n";
            // dd( $item );
            $master = $this->master->firstOrCreate([
                'product_id' => $item["Product ID"]
            ],[
                'product_id' => $item["Product ID"],
                'category' => $item["Category"],
                'subcategory' => $item["Subcategory"],
                'category_type'=> $item["Category Type"],
                'bom_name' => $item["BOM Name"],
                'product_code'=> $item["Product Code"],
                'product_name' => $item["Product Name"],
                'base_price' => $item["Base Price"],
                'requestable' => $item["Requestable"],
                'receipt_tolerance' => $item["Receipt Tolerance (%)"],
                'saleable' => $item["Saleable(YES/NO)"],
                'notes' => $item["Notes"],
                'vat' => $item["VAT"],
                'status_uom' => $item["Status UOM"],
                'formula' => $item["Formula Of These Menu"],
                'units' => json_encode($item['units'])
            ]);
        }

        return response()->json($list);
    }
}
