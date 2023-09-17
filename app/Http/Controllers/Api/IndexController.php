<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

use App\Models\Brand;
use App\Models\Master;
use App\Models\Template;
use App\Traits\HelpersTrait;

class IndexController extends BaseController
{
    use HelpersTrait;

    private $master;
    private $template;

    public function __construct(
        Master $master,
        Template $template
    ) {
        $this->master = $master;
        $this->template = $template;
    }

    public function testHierarchy()
    {
        $items = Brand::with(['province'])->first();

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
