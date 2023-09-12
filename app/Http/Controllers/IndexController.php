<?php

namespace App\Http\Controllers;

use Importer as C;
use Illuminate\Http\Request;

use App\Models\Master;

class IndexController extends BaseController
{
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

        $data = $query->map(function($query) {
            $query->units = $this->sortUnitsByValue($query, 'value');

            return $query;
        });

        $result = $this->generatePagination($data, $total, $this->limit, $page);

        return response()->json($result);
    }

    public function Index()
    {
        return View('welcome');
    }
}
