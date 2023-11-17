<?php
namespace App\Http\Controllers\Api;

use Carbon\Carbon;
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

    public function fetchWaste()
    {
        $now = Carbon::now()->format('Y-m-d');

        $model = $this->reports->where('date', $now);
        $query = $model->first();

        $result = json_decode($query->waste, true);

        return $this->respondWithSuccess($result);
    }

    public function Index($items = [])
    {
        $now = Carbon::now()->format('Y-m-d');
        $query = $this->reports->where('date', $now)->first();

        if (!$query) {
            foreach ($this->listItems as $item) {
                $dList[$item] = [
                    'name'  => $item,
                    'items' => []
                ];
            }
        } else {
            foreach ($this->listItems as $item) {
                $target = isset($query->{$item})
                    ? $this->processDisabled(json_decode($query->{$item}, true)) : [];

                $dList[$item] = $target;
            }
        }

        $items = [
            'additional' => $dList['additional'],
            'waste'      => $dList['waste'],
            'damage'     => $dList['damage'],
        ];

        $result = [
            'items' => $items,
            'notes' => $query->notes ?? ""
        ];

        return $this->respondWithSuccess($result);
    }

    public function Store(Request $request)
    {
        $now = Carbon::now()->format('Y-m-d');

        $items = $request->get('items');
        $notes = $request->get('notes');

        $keyList = ['additional', 'waste', 'damage'];

        $newItems = $this->buildItems($items);
        $result = $this->reports
            ->updateOrCreate([
                'date' => $now 
            ], [
                $keyList[0] => json_encode($newItems[$keyList[0]]),
                $keyList[1] => json_encode($newItems[$keyList[1]]),
                $keyList[2] => json_encode($newItems[$keyList[2]]),
                'notes' => $notes,
                'date' => $now
            ]
        );

        return $this->respondWithSuccess($result);
    }

    private function processDisabled($target)
    {
        foreach($target['items'] as $key => $item) {
            $newItem = array_merge($item, $this->defaultDisabled);

            $target['items'][$key] = $newItem;
        }

        return $target;
    }

    private function buildItems($items)
    {
        $newItems = [];

        foreach($items as $z => $entity) {
            $newItems[$z]['name'] = $entity['name'];
            $newItems[$z]['items'] = [];

            if (count($entity['items']) > 0) {
                foreach($entity['items'] as $k => $item) {
                    $defaultParams = [
                        'name' => $item['name'],
                        'unit' => $item['unit'],
                        'value' => $item['value'],
                        'file' => $item['file'],
                    ];
                    $additionalParams = [];

                    if ($z !== "additional") {
                        $additionalParams = [
                            'code' => $item['code'],
                        ];
                    }

                    $params = array_merge($defaultParams, $additionalParams);

                    $newItems[$z]['items'][$k] = $params;
                }
            }
        }

        return $newItems;
    }
}