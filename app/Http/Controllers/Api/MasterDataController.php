<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

use App\Models\Master;
use App\Traits\HelpersTrait;

class MasterDataController extends BaseController
{
    
    use HelpersTrait;

    private $master;

    public function __construct(
        Master $master
    ) {
        $this->master = $master;
        $this->limit = 15;        
    }

    public function Index(Request $request)
    {
        $page = (int) $request->get('page', 1);
        $sort = $request->get("sort", "id");
        $order = $request->get("order", "asc");

        $model = $this->master;
        $total = $model->count();
        $query = $model
            ->limit($this->limit)
            ->offset($this->limit * ($page - 1))
            ->orderBy($sort, $order)
            ->get();
        
        $items = $this->sortItemsByParams($query, 'units', 'value');
        $result = $this->generatePagination($items, $total, $this->limit, $page);

        return $this->respondWithSuccess($result);
    }
}