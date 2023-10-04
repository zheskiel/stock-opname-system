<?php
namespace App\Http\Controllers;

use App\Models\Master;
use App\Traits\HelpersTrait;
use Illuminate\Http\Request;

class IndexController extends BaseController
{
    use HelpersTrait;

    private $master;

    public function __construct(
        Master $master
    ) {
        $this->master = $master;
    }

    public function Test(Request $request)
    {
        $page = (int) $request->get('page', 1);

        $model = $this->master;
        $total = $model->count();
        $query = $model
            ->limit($this->limit)
            ->offset($this->limit * ($page - 1))
            ->get();

        $data = $this->sortItemsByParams($query, 'units', 'value');
        $result = $this->generatePagination($data, $total, $this->limit, $page);

        // return response()->json($result);

        return View('home')->with([$result]);
    }

    public function Index()
    {
        return View('welcome');
    }
}
