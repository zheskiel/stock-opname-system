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

    private $templateDetails;
    private $templates;
    private $details;

    public function __construct(
        Details $templateDetails,
        Templates $templates,
        Details $details
    ) {
        $this->templateDetails = $templateDetails;
        $this->templates = $templates;
        $this->details = $details;
        $this->limit = 15;
    }

    private function handleFetchData($templateId, $page = 1, $sort = 'id', $order = 'desc', $total = 0, $items = [])
    {
        $template = $this->templates->where('id', $templateId)->first();

        if ($template) {
            $model = $this->details;
            $query = $model->where('templates_id', $template->id);

            $total = $query->count();
            $query = $query
                ->orderBy($sort, $order)
                ->limit($this->limit)
                ->offset($this->limit * ($page - 1))
                ->get();

            $items = $this->sortItemsByParams($query);
        }

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
        $sort = $request->get("sort", "id");
        $order = $request->get("order", "desc");

        $result = $this->handleFetchData($templateId, $page, $sort, $order);

        return $this->respondWithSuccess($result);
    }

    public function createTemplateForOutlet(Request $request)
    {
        $title          = $request->get('title');
        $outletId       = $request->get('outletId');
        $supervisorId   = $request->get('supervisorId');
        $supervisorDuty = $request->get('supervisorDuty');
        $managerId      = $request->get('managerId');
        $items          = $request->get('items');

        $items = json_decode($items, true);

        $slug = $this->processTitleSlug($title);
        $template = $this->templates->firstOrCreate([
            'slug' => $slug,
        ], [
            'title'           => $title,
            'slug'            => $slug,
            'outlet_id'       => $outletId,
            'supervisor_id'   => $supervisorId,
            'supervisor_duty' => $supervisorDuty,
            'manager_id'      => $managerId,
            'owned'           => 0,
            'status'          => 1
        ]);

        foreach($items as $item) {
            $detail = $this->templateDetails
                ->firstOrCreate([
                    'templates_id' => $template->id,
                    'product_id'   => $item['product_id'],
                    'product_code' => $item['product_code'],
                ],
                [
                    'templates_id' => $template->id,
                    'product_id'   => $item['product_id'],
                    'product_code' => $item['product_code'],
                    'product_name' => $item['product_name'],
                    'units'        => json_encode($item['units']),
                    'receipt_tolerance' => $item['receipt_tolerance'],
                ]);

            $template->details()->attach($detail);
        }

        return $this->respondWithSuccess($items);
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
            'templates_id'      => $templateId,
            'product_id'        => $productId,
            'product_code'      => $productCode,
            'product_name'      => $productName,
            "receipt_tolerance" => $tolerance,
            "units"             => $units
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
        $currentPage = $request->get('current_page');
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

        $result = $this->handleFetchData($templateId, $currentPage);

        return $this->respondWithSuccess($result);
    }

    public function removeAllTemplateDetail(Request $request)
    {
        $templateId = $request->get('template_id');
        $template = $this->templates->where('id', $templateId)->first();
        $details = $this->details
            ->where('templates_id', $template->id)
            ->get();

        if ($details) {
            foreach($details as $detail) {
                $template->details()->detach($detail);
                $detail->delete();
            }
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