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

    private function handleFetchData($managerId, $staffId, $page = 1)
    {
        $form = $this->forms
            ->with(['staff'])
            ->where('manager_id', $managerId)
            ->where('staff_id', $staffId)
            ->first();

        $model = $this->items;
        $query = $model
            ->where('forms_id', $form->id)
            ->orderBy('id', 'desc');

        $total = $query->count();
        $items = $query
            ->limit($this->limit)
            ->offset($this->limit * ($page - 1))
            ->get();

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
            'value'        => 0
        ];

        $item = $this->items
            ->create($params);

        $form->items()->attach($item);

        $result = $this->handleFetchData($managerId, $staffId);

        return $this->respondWithSuccess($result);
    }

    public function removeFormDetail(Request $request)
    {
        $managerId = $request->get('manager_id');
        $staffId   = $request->get('staff_id');
        $productId = $request->get('product_id');
        $itemId    = $request->get('item_id');

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

        $result = $this->handleFetchData($managerId, $staffId);

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