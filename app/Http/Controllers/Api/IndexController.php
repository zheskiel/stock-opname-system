<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

use App\Models\Brand;
use App\Models\Outlet;
use App\Models\Master;
use App\Models\Manager;
use App\Models\Templates;
use App\Models\Supervisor;
use App\Traits\HelpersTrait;
use App\Traits\HierarchyTrait;

class IndexController extends BaseController
{
    use HelpersTrait;
    use HierarchyTrait;

    private $brand;
    private $outlet;
    private $master;
    private $manager;
    private $template;
    private $supervisor;
    private $keyList;

    public function __construct(
        Brand $brand,
        Outlet $outlet,
        Master $master,
        Manager $manager,
        Templates $template,
        Supervisor $supervisor
    ) {
        $this->brand = $brand;
        $this->outlet = $outlet;
        $this->master = $master;
        $this->manager = $manager;
        $this->template = $template;
        $this->supervisor = $supervisor;

        $this->limit = 15;
        $this->keyList = [
            'province',
            'regency',
            'district',
            'location',
            'outlet',
            'manager',
            'supervisor',
            'type',
            'staffs'
        ];
    }

    private function formatDataItems($current, $index = 0)
    {
        $index += 1;
        $type = $this->keyList[$index];

        foreach ($current->children as $item) {
            $current = $item;

            $current->children = $type == "manager" ? [$current->{$type}->makeHidden('id')] : $current->{$type}->makeHidden('id');
            unset( $current->{$type} );

            if ($index < count($this->keyList) - 1) {
                $this->formatDataItems($current, $index, $this->keyList[$index]);
            }
        }
    }

    public function testHierarchy()
    {
        $items = $this->brand
            ->with($this->loadProvinceWithRegency())
            ->first();

        $items->children = $items->province->makeHidden('id');
        unset( $items->province );

        $this->formatDataItems($items);

        return $this->respondWithSuccess($items);
    }

    public function testManager()
    {
        $outlet = $this->outlet->first();
        $items = $this->manager
            ->with($this->loadSupervisorWithSupervisorPicAndTypeByOutlet($outlet))
            ->first();

        return $this->respondWithSuccess($items);
    }

    public function testSupervisor()
    {
        $items = $this->supervisor->with(['type'])->first();

        return $this->respondWithSuccess($items);
    }

    public function testTemplate(Request $request)
    {
        $page = (int) $request->get('page', 1);

        $model = $this->template;
        $total = $model->count();
        $items = $model
            ->with([
                // 'manager',
                // 'supervisor',
                // 'outlet',
                // 'details'
            ])
            ->limit($this->limit)
            ->offset($this->limit * ($page - 1))
            ->get();

        $result = $this->generatePagination($items, $total, $this->limit, $page);

        return $this->respondWithSuccess($result);
    }

    public function testMaster(Request $request)
    {
        $page = (int) $request->get('page', 1);

        $model = $this->master;
        $total = $model->count();
        $query = $model
            ->limit($this->limit)
            ->offset($this->limit * ($page - 1))
            ->get();
        
        $items = $this->sortItemsByParams($query, 'units', 'value');
        $result = $this->generatePagination($items, $total, $this->limit, $page);

        return $this->respondWithSuccess($result);
    }
}
