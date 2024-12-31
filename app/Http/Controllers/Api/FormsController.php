<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

use DB;

use App\Models\{
    Forms,
    Items,
    Notes,
    Staff,
    Manager,
    Templates,
    Outlet
};

use App\Traits\ {
    HelpersTrait,
    HierarchyTrait
};

use Carbon\Carbon;

class FormsController extends BaseController
{
    use HelpersTrait;
    use HierarchyTrait;

    private $forms;
    private $items;
    private $notes;
    private $staff;
    private $outlet;
    private $manager;
    private $templates;

    public function __construct(
        Templates $templates,
        Manager $manager,
        Outlet $outlet,
        Staff $staff,
        Notes $notes,
        Items $items,
        Forms $forms
    ) {
        $this->templates = $templates;
        $this->manager = $manager;
        $this->outlet = $outlet;
        $this->staff = $staff;
        $this->notes = $notes;
        $this->items = $items;
        $this->forms = $forms;
        $this->limit = 15;
    }

    public function fetchManager()
    {
        $managers = $this->manager->get();

        return $this->respondWithSuccess($managers);
    }

    public function fetchTemplatesByManager(Request $request)
    {
        $managerId = $request->get('managerId');

        $items = $this->templates
            ->where('manager_id', $managerId)
            ->get();

        return $this->respondWithSuccess($items);
    }

    public function fetchOutletsByManager(Request $request)
    {
        $managerId = $request->get('managerId');

        $items = $this->outlet
            ->where('manager_id', $managerId)
            ->get();

        return $this->respondWithSuccess($items);
    }

    public function fetchSupervisorByManager(Request $request)
    {
        $managerId = $request->get('managerId');
        $outletId = $request->get('outletId');

        $manager = $this->manager
            ->with([
                'supervisor' => function($query) use ($outletId) {
                    return $query
                        ->with(['supervisor_pic'])
                        ->wherePivot('outlet_id', $outletId)
                        ->orderBy('name');
                }
            ])
            ->where('id', $managerId)
            ->first();

        return $this->respondWithSuccess($manager);
    }

    public function fetchStaffBySupervisor(Request $request)
    {
        $supervisorId = $request->get("supervisorId");
        $managerId = $request->get('managerId');
        $outletId = $request->get('outletId');

        $items = $this->staff
            ->where('is_supervisor', 0)
            ->where('supervisor_id', $supervisorId)
            ->where('manager_id', $managerId)
            ->where('outlet_id', $outletId)
            ->get();

        return $this->respondWithSuccess($items);
    }

    public function Index(Request $request)
    {
        $managerId = $request->get('managerId');
        $supervisorId = $request->get('supervisorId', 2);

        $manager = $this->manager->where('id', $managerId)->first();
        $staffs = DB::select(
            DB::raw(
                "SELECT staff.id, staff.name, staff.manager_id,
                    CASE WHEN forms.staff_id IS NOT NULL THEN 1 ELSE 0 END AS has_form_record
                FROM staff
                LEFT JOIN forms ON staff.id = forms.staff_id
                WHERE staff.manager_id = :managerId AND staff.supervisor_id = :supervisorId AND staff.is_supervisor = 0;"
            ), [
                "managerId" => $managerId,
                "supervisorId" => $supervisorId
            ]
        );

        $manager->staff = $staffs;

        return $this->respondWithSuccess($manager);
    }

    private function processMap($items, $dailyItems, $result = [], $units = [])
    {
        return $items->map(function($items) use ($dailyItems, $result, $units) {
            $value = 0;
            foreach ($items as $item) {
                if (isset($dailyItems[$item->id])) {
                    $dailyArrs = $dailyItems[$item->id];

                    $dailyArr = $item;
                    foreach ($dailyArrs as $arr) {
                        $dailyArr['value'] += $arr->value;
                    }

                    $value += $dailyArr['value'] * $item->unit_value;
                    $units[] = $dailyArr['value'] ." ". $item->unit . " = " . $dailyArr['value'] * $item->unit_value ." ". $item->unit_sku;
                    $original[] = $dailyArr['value'];

                    $result = [
                        "id"            => $item->id,
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
            }

            return $result;
        });
    }

    private function fetchCombinedItems($managerId, $outletId, $today, $page, $totalItems = 0)
    {
        $items = $this->forms
            ->with([
                'items' => function($query) use ($page, &$totalItems) {
                    $totalItems = $query->count();

                    return $query
                        ->take($this->limit)
                        ->offset($this->limit * ($page - 1))
                        ->orderBy('product_name', 'asc')
                        ->get();
                },
                'daily' => function ($query) use ($today) {
                    return $query->where('date', $today);
                },
                'notes' => function ($query) use ($today) {
                    return $query->with(['staff'])->where('date', $today);
                }
            ])
            ->where('manager_id', $managerId)
            ->where('outlet_id', $outletId)
            ->orderBy('id')
            ->get();

        $dailyItems = $items->pluck('daily')->flatten()->groupBy('items_id');
        $dataItems  = $items->pluck('items')->flatten()->groupBy('product_id');
        $noteItems  = $items->pluck('notes')->flatten();

        $items = $this->processMap($dataItems, $dailyItems)->toArray();
        $items = array_slice($items, 0, count($items));

        return [$totalItems, $items, $noteItems];
    }

    public function fetchCombinedForm(Request $request, $managerId, $outletId)
    {
        $page = $request->get('page', 1);
        $today = Carbon::now()->isoFormat('YYYY-MM-DD');

        list($totalItems, $items, $noteItems) = $this
            ->fetchCombinedItems($managerId, $outletId, $today, $page);

        $items = $this->usortItemsAsc($items, 'product_name');

        $data = [
            'items' => $items,
            'notes' => $noteItems
        ];

        $result = $this->generatePagination($data, $totalItems, $this->limit, $page);

        return $this->respondWithSuccess($result);
    }

    private function handleFetchData(
        $managerId,
        $staffId,
        $page = 1,
        $sort = 'id',
        $order = 'desc',
        $withLimit = true
    ) {
        $form = $this->forms
            ->with(['staff'])
            ->where('manager_id', $managerId)
            ->where('staff_id', $staffId)
            ->first();

        $model = $this->items;
        $items = $model
            ->where('forms_id', $form->id)
            ->orderBy($sort, $order)
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

        if ($withLimit != false) {
            $start = $this->limit * ($page - 1);
            $end = $this->limit;

            $items = array_slice($items, $start, $end);
        }

        $newItems = $form;
        $newItems['items'] = array_values($items);

        $result = $this->generatePagination($newItems, $total, $this->limit, $page);

        return $result;
    }

    public function FetchFormByStaffId(Request $request, $managerId, $staffId)
    {
        $page  = (int) $request->get('page', 1);
        $sort  = $request->get("sort", "id");
        $order = $request->get("order", "desc");
        $withLimit = (bool) $request->get("withLimit", true);

        $result = $this->handleFetchData(
            $managerId, $staffId, $page, $sort, $order, $withLimit
        );

        return $this->respondWithSuccess($result);
    }

    public function UpdateFormDetail(Request $request)
    {
        $formId    = $request->get('form_id');
        $outletId  = $request->get('outlet_id');
        $items     = $request->get('items');

        usort($items, function($a, $b) {
            // First compare by product_id (ascending)
            if ($a['product_id'] == $b['product_id']) {
                // If product_id is the same, compare by unit_value (descending)
                return $b['unit_value'] <=> $a['unit_value'];
            }

            return $a['product_id'] <=> $b['product_id'];
        });

        // first find the record
        $form = $this->forms
            ->where('id', $formId)
            ->where('outlet_id', $outletId)
            ->first();

        // detach all many to many relation
        $form->items()->detach();

        // delete all items by form id
        $this->items->where('forms_id', $formId)->delete();

        foreach($items as $item) {
            $newItem = $this->items->create([
                'forms_id'     => $form->id,
                'product_id'   => $item['product_id'],
                'product_code' => $item['product_code'],
                'product_name' => $item['product_name'],
                'unit'         => $item['unit'],
                'unit_value'   => $item['unit_value'],
                'unit_sku'     => $item['unit_sku'],
                'value'        => 0
            ]);

            if ($newItem) {
                $form->items()->syncWithoutDetaching($newItem);
            }
        }

        return $this->respondWithSuccess( $items );
    }

    public function CreateNewFormDetail(Request $request)
    {
        $templateId    = $request->get('template_id');
        $outletId      = $request->get('outlet_id'); 
        $managerId     = $request->get('manager_id');
        $supervisorId  = $request->get('supervisor_id');
        $staffId       = $request->get('staff_id');
        $items         = $request->get('items');

        usort($items, function($a, $b) {
            // First compare by product_id (ascending)
            if ($a['product_id'] == $b['product_id']) {
                // If product_id is the same, compare by unit_value (descending)
                return $b['unit_value'] <=> $a['unit_value'];
            }

            return $a['product_id'] <=> $b['product_id'];
        });

        $formParam = [
            'template_id'   => $templateId,
            'outlet_id'     => $outletId,
            'manager_id'    => $managerId,
            'supervisor_id' => $supervisorId,
            'staff_id'      => $staffId
        ];

        $form = $this->forms
            ->firstOrCreate($formParam, $formParam);

        foreach($items as $item) {
            $params = [
                'forms_id'     => $form->id,
                'product_id'   => $item['product_id'],
                'product_code' => $item['product_code'],
                'product_name' => $item['product_name'],
                'unit'         => $item['unit'],
                'unit_value'   => $item['unit_value'],
                'unit_sku'     => $item['unit_sku'],
                'value'        => 0
            ];

            $newItem = $this->items->firstOrCreate([
                'forms_id'     => $form->id,
                'product_id'   => $item['product_id'],
                'product_code' => $item['product_code'],
                'unit'         => $item['unit'],
            ], $params);

            if ($newItem) {
                $form->items()->syncWithoutDetaching($newItem);
            }
        }

        return $this->respondWithSuccess( $newItem );
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

    public function removeAllFormDetail(Request $request)
    {
        $managerId   = $request->get('manager_id');
        $staffId     = $request->get('staff_id');

        $form = $this->forms
            ->where('manager_id', $managerId)
            ->where('staff_id', $staffId)
            ->first();

        $items = $this->items
            ->where('forms_id', $form->id)
            ->get();

        if ($items) {
            foreach($items as $item) {
                $form->items()->detach($item);
                $item->delete();
            }
        }

        $currentPage = 1;
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