<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\ {
    Reports,
    Templates
};

use App\Http\Controllers\BaseController;

class ReportsController extends BaseController
{
    private $listItems = [
        'additional', 'waste', 'damage'
    ];

    private $defaultDisabled = [
        "name_disabled"  => true,
        "unit_disabled"  => true,
        "value_disabled" => true,
        "file_disabled"  => true,
    ];

    private $reports;
    private $templates;

    public function __construct(
        Reports $reports,
        Templates $templates
    ) {
        $this->reports = $reports;
        $this->templates = $templates;
    }

    public function fetchWasteByTemplate(Request $request, $templateId = 1)
    {
        $query = $request->get('query');

        $data = $this->templates->with([
            'details' => function($q) use ($query) {
                return $q->where('product_name', 'LIKE', '%' . $query . '%');
            }
        ])->where('id', $templateId)->first();

        $items = [];
        $details = $data->details;
        $details->each(function($query) use (&$items) {
            $units = json_decode($query->units, true);
            $firstKey = array_key_first($units);

            $items[] = [
                'product_id'   => $query->product_id,
                'product_name' => $query->product_name,
                'product_code' => $query->product_code,
                'product_sku'  => $units[$firstKey]['sku']
            ];
        });

        $result = $this->usortItemsAsc($items, 'product_name');

        return $this->respondWithSuccess($result);
    }

    public function Index()
    {
        $model = $this->reports;
        $query = $model->first();

        foreach ($this->listItems as $item) {
            $target = json_decode($query->{$item}, true);

            $dList[$item] = $this->processDisabled($target);
        }
        
        $items = [
            'additional' => $dList['additional'],
            'waste'      => $dList['waste'],
            'damage'     => $dList['damage'],
        ];

        $result = [
            'items' => $items,
            'notes' =>  $query->notes
        ];

        return $this->respondWithSuccess($result);
    }

    private function processDisabled($target)
    {
        foreach($target['items'] as  $key => $item) {
            $newItem = array_merge($item, $this->defaultDisabled);

            $target['items'][$key] = $newItem;
        }

        return $target;
    }
}