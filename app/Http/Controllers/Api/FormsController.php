<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

use App\Models\Forms;
use App\Models\Items;
use App\Models\Manager;
use App\Models\Templates;
use App\Traits\HelpersTrait;
use App\Traits\HierarchyTrait;

use JWTAuth;

class FormsController extends BaseController
{
    use HelpersTrait;
    use HierarchyTrait;

    private $forms;
    private $items;
    private $manager;
    private $templates;

    public function __construct(
        Templates $templates,
        Manager $manager,
        Items $items,
        Forms $forms
    ) {
        $this->templates = $templates;
        $this->manager = $manager;
        $this->items = $items;
        $this->forms = $forms;
        $this->limit = 15;
    }

    public function Index()
    {
        // $manager = JWTAuth::parseToken()->authenticate();
        $manager = $this->manager
            ->with(['staff'])
            ->first();

        return $this->respondWithSuccess($manager);
    }

    private function processMap($items)
    {
        return $items->map(function($items) {
            $result = [];
            $units = [];

            $value = 0;
            foreach ($items as $item) {
                $value += $item->value * $item->unit_value;
                $units[] = $item->value ." ". $item->unit . " = " . $item->value * $item->unit_value ." ". $item->unit_sku;
                $original[] = $item->value;

                $result = [
                    "forms_id"      => $item->forms_id,
                    'product_id'    => $item->product_id,
                    'product_code'  => $item->product_code,
                    'product_name'  => $item->product_name,
                    'unit'          => $units,
                    'original'      => $original,
                    'unit_value'    => $item->unit_value,
                    'unit_sku'      => $item->unit_sku,
                    'value'         => $value,
                ];
            }

            return $result;
        });
    }

    public function fetchCombinedForm($managerId, $outletId)
    {
        $items = $this->forms
            ->with(['items'])
            // ->where('manager_id', $managerId)
            // ->where('outlet_id', $outletId)
            ->orderBy('id')
            ->get()
            ->pluck('items');

        $items = collect($items)->flatten();
        $items = $items->groupBy('product_id');
        $items = $this->processMap($items)->toArray();
        $items = array_slice($items, 0, count($items));

        $result = $items;

        return $this->respondWithSuccess($result);
    }

    private function handleFetchData2($managerId, $staffId, $page = 1)
    {
        $form = $this->forms
            ->with(['staff'])
            ->where('manager_id', $managerId)
            ->where('staff_id', $staffId)
            ->first();

        $model = $this->items;
        $query = $model->where('forms_id', $form->id);

        $total = $query->count();
        $items = $query
            ->orderBy('id', 'desc')
            ->groupBy('product_id')
            ->limit($this->limit)
            ->offset($this->limit * ($page - 1))
            ->get();

        $newItems = $form;
        $newItems['items'] = $items;

        $result = $this->generatePagination($newItems, $total, $this->limit, $page);

        return $result;
    }

    private function handleFetchData($managerId, $staffId, $page = 1)
    {
        $form = $this->forms
            ->with(['staff'])
            ->where('manager_id', $managerId)
            ->where('staff_id', $staffId)
            ->first();

        $start = $this->limit * ($page - 1);
        $end = $this->limit;

        $model = $this->items;
        $items = $model
            ->where('forms_id', $form->id)
            ->orderBy('id', 'desc')
            ->get();

        $group = $items->mapToGroups(function ($item) {
            return [$item['product_id'] => [
                "unit"       => $item['unit'],
                "unit_value" => $item['unit_value'],
                "unit_sku"   => $item['unit_sku']
            ]];
        });

        $items = $items->groupBy('product_id')->toArray();

        foreach ($items as $item) {
            $item = array_merge(...$item);

            unset( $item['unit_value'] );
            unset( $item['unit_sku'] );

            $unit = $group[$item['product_id']];
            $unitArr = $unit->toArray();

            $item['units'] = $this->usortItems($unitArr, 'unit_value');

            $items[$item['product_id']] = $item;
        }

        $total = count($items);
        $items = array_slice($items, $start, $end);

        $newItems = $form;
        $newItems['items'] = $items;

        $result = $this->generatePagination($newItems, $total, $this->limit, $page);

        return $result;
    }

    public function FetchFormByStaffId(Request $request, $managerId, $staffId)
    {
        $page = (int) $request->get('page', 1);

        $result = $this->handleFetchData($managerId, $staffId, $page);

        return $this->respondWithSuccess($result);
    }

    public function createFormDetail(Request $request)
    {
        $managerId   = $request->get('manager_id');
        $staffId     = $request->get('staff_id');
        $productId   = $request->get('product_id');
        $productCode = $request->get('product_code');
        $productName = $request->get('product_name');
        $unit        = $request->get('selected_unit');
        $units       = $request->get('units');

        try {
            $currentUnit = $units[$unit];

            $form = $this->forms
                ->where('manager_id', $managerId)
                ->where('staff_id', $staffId)
                ->first();

            $params = [
                'forms_id'     => $form->id,
                'product_id'   => $productId,
                'product_code' => $productCode,
                'product_name' => $productName,
                'unit'         => $unit,
                'unit_value'   => $currentUnit['value'],
                'unit_sku'     => $currentUnit['sku'],
                'value'        => 0
            ];

            $item = $this->items
                ->create($params);

            $form->items()->attach($item);

            $result = $this->handleFetchData($managerId, $staffId);

            return $this->respondWithSuccess($result);
        } catch(\Exception $e) {
            return $this->respondError($e->getMessage());
        }
    }

    public function removeFormDetail(Request $request)
    {
        $currentPage = $request->get('current_page');
        $managerId   = $request->get('manager_id');
        $staffId     = $request->get('staff_id');
        $productId   = $request->get('product_id');
        $itemId      = $request->get('item_id');

        $form = $this->forms
            ->where('manager_id', $managerId)
            ->where('staff_id', $staffId)
            ->first();
        
        $item = $this->items
            ->where('id', $itemId)
            ->where('forms_id', $form->id)
            ->where('product_id', $productId)
            ->first();
        
        if ($item) {
            $form->items()->detach($item);
            $item->delete();
        }

        $result = $this->handleFetchData($managerId, $staffId, $currentPage);

        return $this->respondWithSuccess($result);
    }

    public function FetchAllSelected($managerId, $staffId)
    {
        $form = $this->forms
            ->with(['items'])
            ->where('manager_id', $managerId)
            ->where('staff_id', $staffId)
            ->first();

        $result = $form->items->map->only(['product_code', 'unit'])->values();

        return $this->respondWithSuccess($result);
    }
}