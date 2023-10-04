<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

use App\Models\Templates;
use App\Traits\HelpersTrait;
use App\Traits\HierarchyTrait;

class TemplateController extends BaseController
{
    use HelpersTrait;
    use HierarchyTrait;

    private $templates;

    public function __construct(
        Templates $templates
    ) {
        $this->templates = $templates;
        $this->limit = 50;
    }

    public function View(Request $request, $templateId)
    {
        $page = (int) $request->get('page', 1);

        $model  = $this->templates;
        $source = $model->withCount(['details'])->where('id', $templateId)->first();
        $total  = $source->details_count;

        $items  = $source->load(['details' => function($query) use ($page) {
            return $query
                ->limit($this->limit)
                ->offset($this->limit * ($page - 1))
                ->get();
        }]);

        $result = $this->generatePagination($items, $total, $this->limit, $page);

        return $this->respondWithSuccess($result);
    }
}