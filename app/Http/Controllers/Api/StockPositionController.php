<?php
namespace App\Http\Controllers\Api;

use Importer as C;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Models\Master;
use App\Models\StockPosition;
use App\Traits\HelpersTrait;
use App\Traits\HierarchyTrait;
use App\Http\Controllers\BaseController;

class StockPositionController extends BaseController
{
    use HelpersTrait;
    use HierarchyTrait;

    private $stockPosition;
    private $master;

    public function __construct(
        StockPosition $stockPosition,
        Master $master
    ) {
        $this->stockPosition = $stockPosition;
        $this->master = $master;
        $this->limit = 15;
    }

    private function getStartKeyAndTitleArray($collection, $startKey = 0, $titleArr = "") : array
    {
        foreach ($collection as $key => $item) {
            if ($item[0] == 'Product Name') {
                $startKey = $key + 1;
                $titleArr = $item;

                break;
            }
        }

        return [$startKey, $titleArr];
    }

    private function getCollection($filePath)
    {
        $excel = C::make('Excel');
        $excel->load($filePath);

        unset($filePath);

        return $excel->getCollection()->toArray();
    }

    private function getListData($collection, $titleArr, $startKey, $list = []) : array
    {
        $endKey = count($collection) - 1;

        $newCollection = array_slice($collection, $startKey, $endKey);

        foreach ($newCollection as $item) {
            if ($item['Product Code' != '']) {
                $list[] = $item;
            }
        }

        return $list;
    }

    private function createData($listItems)
    {
        $now = Carbon::now()->format('Y-m-d');

        foreach($listItems as $item) {
            $this->stockPosition->firstOrCreate([
                'date'         => $now,
                'product_code' => $item[1],
            ], [
                'date'         => $now,
                'product_name' => $item[0],
                'product_code' => $item[1],
                'unit'         => $item[2],
                'category'     => $item[3],
                'subcategory'  => $item[4],
                'value'        => $item[5],
            ]);
        }
    }

    public function CreateStockPosition(Request $request)
    {
        $file = $request->file('file');
        $destination = "/tmp";

        $filePath = $this->handleUpload($file, $destination);

        $collection = $this->getCollection($filePath);

        list($startKey, $titleArr) = $this->getStartKeyAndTitleArray($collection);

        $listData = $this->getListData($collection, $titleArr, $startKey);

        $this->createData($listData);

        return $this->respondWithSuccess($listData);
    }

    public function FetchStockPosition(Request $request)
    {
        $page = (int) $request->get('page', 1);

        $now = Carbon::now()->format('Y-m-d');

        $model = $this->stockPosition
            ->where('date', $now);

        $total = $model->count();
        $items = $model
            ->limit($this->limit)
            ->offset($this->limit * ($page - 1))
            ->get()
            ->each(function($query) {
                $masterUnit = $this->master
                    ->where('product_code', $query->product_code)
                    ->first();

                if($masterUnit) {
                    $units = json_decode($masterUnit->units, true);
                    $unit = str_replace(" ", "", $query->unit);

                    $selectedUnit = $units[$unit];

                    $query->unit = $selectedUnit['sku'];
                    $query->value = $query->value * $selectedUnit['value'];
                }

                return $query;
            });

        $result = $this->generatePagination($items, $total, $this->limit, $page);

        return $this->respondWithSuccess($result);
    }
}