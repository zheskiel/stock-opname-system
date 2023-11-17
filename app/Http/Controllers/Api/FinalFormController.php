<?php
namespace App\Http\Controllers\Api;

use DB;
use Carbon\Carbon;
use App\Models\ {
    Forms,
    Master,
    Reports,
    FinalForm,
    StockPosition
};

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

class FinalFormController extends BaseController
{
    private $forms;
    private $master;
    private $reports;
    private $finalForm;
    private $stockPosition;

    public function __construct(
        Forms $forms,
        Master $master,
        Reports $reports,
        FinalForm $finalForm,
        StockPosition $stockPosition
    ) {
        $this->forms = $forms;
        $this->master = $master;
        $this->reports = $reports;
        $this->finalForm = $finalForm;
        $this->stockPosition = $stockPosition;

        $this->limit = 15;
    }

    private function fetchWaste($now)
    {
        $model = $this->reports->where('date', $now);
        $query = $model->first();

        $result = json_decode($query->waste, true);

        return $result['items'];
    }

    private function processStockPositionMap($items, $listItems = [])
    {
        foreach ($items as $item) {
            $units = json_decode($item->units, true);
            $unit = str_replace(" ", "", $item->unit);

            $selectedUnit = $units[$unit];

            $listItems[$item->product_code] = [
                'date'         => $item->date,
                'product_name' => $item->product_name,
                'product_code' => $item->product_code,
                'unit'         => $selectedUnit['sku'],
                'unit_sku'     => $selectedUnit['sku'],
                'category'     => $item->category,
                'subcategory'  => $item->subcategory,
                'value'        => $item->value * $selectedUnit['value']
            ];
        }

        return $listItems;
    }

    private function processCombinedForms($items, $listItems = [])
    {
        foreach ($items as $item) {
            if ($item->id != "") {
                $listItems[$item->product_code] = [
                    'id'           => $item->id,
                    'forms_id'     => $item->forms_id,
                    'product_id'   => $item->product_id,
                    'product_code' => $item->product_code,
                    'product_name' => $item->product_name,
                    'unit_sku'     => $item->unit_sku,
                    'value'        => $item->unit_value * $item->value,
                ];
            }
        }

        return $listItems;
    }

    private function fetchStockPositionReports($now)
    {
        $items = DB::select(
            DB::raw(
                "SELECT sp.*, m.*
                FROM stock_position AS sp
                INNER JOIN (
                    SELECT product_code, SUM(value) AS total_value
                    FROM stock_position
                    WHERE date = '$now'
                    GROUP BY product_code
                ) AS temp ON sp.product_code = temp.product_code
                INNER JOIN master AS m ON sp.product_code = m.product_code;"
            )
        );

        return array_map(fn($x) => (array) $x, $this->processStockPositionMap($items));
    }

    private function fetchCombinedForms($managerId = 1, $outletId = 1)
    {
        $items = DB::select(
            DB::raw(
                "SELECT forms.*, items.*, daily.*
                FROM forms
                LEFT JOIN items ON forms.id = items.forms_id
                LEFT JOIN daily ON items.id = daily.items_id
                WHERE forms.manager_id = :managerId
                AND forms.outlet_id = :outletId
                ORDER BY forms.id;"
            ), [
                "managerId" => $managerId,
                "outletId" => $outletId
            ]
        );

        return array_map(fn($x) => (array) $x, $this->processCombinedForms($items));
    }

    // Define a function to update or add items in the result array
    private function updateResult(&$result, $key, $item)
    {
        $code = $item['product_code'] ?? $item['code'];
        $name = $item['product_name'] ?? $item['name'];
        $unit = $item['unit_sku'] ?? $item['unit'];

        if (!isset($result[$code])) {
            $defaultValue = [
                'value' => 0,
                'unit'  => $unit
            ];

            $defaultItems = [
                'combinedForm'  => $defaultValue,
                'stockPosition' => $defaultValue,
                'waste'         => $defaultValue
            ];
        
            $result[$code] = [
                'product_code' => $code,
                'product_name' => $name,
                'unit_sku'     => $unit,
                'items'        => $defaultItems
            ];
        }

        $result[$code]['items'][$key] = [
            "value" => (int) $item['value'],
            "unit"  => $unit
        ];

        return $result;
    }

    private function processUpdateResult($data, $result = [])
    {
        foreach($data as $key => $items) {
            foreach($items as $item) {
                $this->updateResult($result, $key, $item);
            }
        }

        return array_values($result);
    }

    private function processFetching()
    {
        $now = Carbon::now()->format('Y-m-d');

        $default = [
            'combinedForm'  => $this->fetchCombinedForms(),
            'stockPosition' => $this->fetchStockPositionReports($now),
            'waste'         => $this->fetchWaste($now),
        ];

        $items = $this->processUpdateResult($default);

        return $items;
    }

    private function processCalculate($list, $listItems = [])
    {
        foreach ($list as $k => $data) {
            $combinedForm = $data['items']['combinedForm']['value'];
            $stockPosition = $data['items']['stockPosition']['value'];
            $waste  = $data['items']['waste']['value'];

            $stockMinusWaste = -($stockPosition - $waste);
            $calculated = $stockMinusWaste + $combinedForm;

            $listItems[$k] = array_merge($data, [
                'calculated' => $calculated
            ]);
        }

        return $listItems;
    }

    public function Index(Request $request)
    {
        $page = (int) $request->get('page', 1);

        $model = $this->finalForm;
        $total = $model->count();
        $items = $model
            ->limit($this->limit)
            ->offset($this->limit * ($page - 1))
            ->get()
            ->each(function($query) {
                $query->items = json_decode($query->items, true);

                return $query;
            });

        $result = $this->generatePagination($items, $total, $this->limit, $page);

        return $this->respondWithSuccess($result);
    }

    public function CreateTest()
    {
        $items = $this->processFetching();
        $calculatedItems = $this->processCalculate($items);

        return $this->respondWithSuccess($calculatedItems);
    }

    public function Create()
    {
        $items = $this->processFetching();
        $calculatedItems = $this->processCalculate($items);

        foreach ($calculatedItems as $item) {
            $this->finalForm->updateOrCreate([
                'product_code' => $item['product_code'],
                'unit_sku'     => $item['unit_sku'],
            ],[
                'product_code' => $item['product_code'],
                'product_name' => $item['product_name'],
                'unit_sku'     => $item['unit_sku'],
                'calculated'   => $item['calculated'],
                'items'        => json_encode($item['items'])
            ]);
        }

        return $this->respondWithSuccess("done");
    }
}