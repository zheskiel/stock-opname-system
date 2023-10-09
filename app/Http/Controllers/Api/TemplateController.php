<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

use App\Models\Templates;
use App\Models\Details;
use App\Traits\HelpersTrait;
use App\Traits\HierarchyTrait;

class TemplateController extends BaseController
{
    use HelpersTrait;
    use HierarchyTrait;

    private $templates;
    private $details;

    public function __construct(
        Templates $templates,
        Details $details
    ) {
        $this->templates = $templates;
        $this->details = $details;
        $this->limit = 15;
    }


    private function handleFetchData($templateId, $page = 1)
    {
        $template = $this->templates->where('id', $templateId)->first();

        $model = $this->details;
        $query = $model
            ->where('templates_id', $template->id)
            ->orderBy('id', 'desc');

        $total = $query->count();
        $query = $query
            ->limit($this->limit)
            ->offset($this->limit * ($page - 1))
            ->get();

        $items = $this->sortItemsByParams($query);

        $newItems = $template;
        $newItems['details'] = $items;

        $path = route('template.view', ['templateId' => $templateId]);
        $url = preg_replace('#^.+://[^/]+#', '', $path);

        $result = $this->generatePagination($newItems, $total, $this->limit, $page, $url);

        return $result;
    }

    public function View(Request $request, $templateId)
    {
        $page = (int) $request->get('page', 1);

        $result = $this->handleFetchData($templateId, $page);

        return $this->respondWithSuccess($result);
    }

    public function createTemplateDetail(Request $request)
    {
        $templateId  = $request->get('template_id');
        $productId   = $request->get('product_id');
        $productCode = $request->get('product_code');
        $productName = $request->get('product_name');
        $tolerance   = $request->get('receipt_tolerance');
        $units       = $request->get('units');

        $template = $this->templates->where('id', $templateId)->first();

        $params = [
            'templates_id' => $templateId,
            'product_id' => $productId,
            'product_code' => $productCode,
            'product_name' => $productName,
            "receipt_tolerance" => $tolerance,
            "units" => $units
        ];

        $detail = $this->details->create($params);

        $template->details()->attach($detail);

        $this->templates->refresh();
        $this->details->refresh();

        $result = $this->handleFetchData($templateId);

        return $this->respondWithSuccess($result);
    }

    public function removeTemplateDetail(Request $request)
    {
        $templateId  = $request->get('template_id');
        $productId   = $request->get('product_id');

        $template = $this->templates->where('id', $templateId)->first();
        $detail = $this->details
            ->where('templates_id', $template->id)
            ->where('product_id', $productId)
            ->first();

        if ($detail) {
            $template->details()->detach($detail);
            $detail->delete();
        }

        $result = $this->handleFetchData($templateId);

        return $this->respondWithSuccess($result);
    }

    public function FetchAllSelected($templateId)
    {
        $model  = $this->templates;
        $source = $model
            ->with(['details'])
            ->where('id', $templateId)
            ->first();

        $result = $source->details->pluck('product_code');

        return $this->respondWithSuccess($result);
    }
}