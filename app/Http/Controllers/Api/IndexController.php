<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

use App\Models\Brand;
use App\Models\Outlet;
use App\Models\Master;
use App\Models\Manager;
use App\Models\Template;
use App\Models\Supervisor;
use App\Traits\HelpersTrait;
use App\Traits\HierarchyTrait;

class IndexController extends BaseController
{
    use HelpersTrait;
    use HierarchyTrait;

    private $outlet;
    private $master;
    private $manager;
    private $template;
    private $supervisor;

    public function __construct(
        Outlet $outlet,
        Master $master,
        Manager $manager,
        Template $template,
        Supervisor $supervisor
    ) {
        $this->outlet = $outlet;
        $this->master = $master;
        $this->manager = $manager;
        $this->template = $template;
        $this->supervisor = $supervisor;
    }

    public function testHierarchy()
    {
        $items = Brand::with($this->loadProvinceWithRegency())->first();

        return $this->respondWithSuccess($items);
    }

    public function testManager()
    {
        $outlet = $this->outlet->inRandomOrder()->first();
        $items = $this->manager
            ->with($this->loadSupervisorWithSupervisorPicAndTypeByOutlet($outlet))
            ->first();

        return $this->respondWithSuccess($items);
    }

    public function testSupervisor()
    {
        $items = Supervisor::with(['type'])->first();

        return $this->respondWithSuccess($items);
    }

    public function testTemplate(Request $request)
    {
        $page = (int) $request->get('page', 1);

        $model = $this->template;
        $total = $model->count();
        $items = $model
            ->with(['manager', 'supervisor', 'outlet'])
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
