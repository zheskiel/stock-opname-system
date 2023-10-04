<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

use App\Models\Templates;
use App\Traits\HelpersTrait;
use App\Traits\HierarchyTrait;

class TemplatesController extends BaseController
{
    use HelpersTrait;
    use HierarchyTrait;

    private $templates;

    public function __construct(
        Templates $templates
    ) {
        $this->templates = $templates;
        $this->limit = 10;
    }

    public function Index(Request $request)
    {
        $page = (int) $request->get('page', 1);

        $model = $this->templates;
        $total = $model->count();
        $items = $model
            ->limit($this->limit)
            ->offset($this->limit * ($page - 1))
            ->get();

        $result = $this->generatePagination($items, $total, $this->limit, $page);

        return $this->respondWithSuccess($result);
    }
}