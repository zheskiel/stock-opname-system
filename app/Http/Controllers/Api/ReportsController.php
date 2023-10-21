<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;

use App\Models\Reports;
use App\Traits\HelpersTrait;

class ReportsController extends BaseController
{
    
    use HelpersTrait;

    private $reports;
    private $defaultDisabled = [
        "name_disabled"  => true,
        "unit_disabled"  => true,
        "value_disabled" => true,
        "file_disabled"  => true,
    ];

    public function __construct(
        Reports $reports
    ) {
        $this->reports = $reports;
    }

    private function processDisabled($target)
    {
        $items = $target['items'];

        foreach($items as  $key => $item) {
            $newItem = array_merge($item, $this->defaultDisabled);

            $target['items'][$key] = $newItem;
        }

        return $target;
    }

    public function Index()
    {
        $model = $this->reports;
        $query = $model->first();

        $listItems = ['additional', 'waste', 'damage'];

        foreach ($listItems as $item) {
            $target = json_decode($query->{$item}, true);
            $target = $this->processDisabled($target);

            $dList[$item] = $target;
        }
        
        $result = [
            'items' => [
                'additional' => $dList['additional'],
                'waste'      => $dList['waste'],
                'damage'     => $dList['damage'],
            ],
            'notes' =>  $query->notes
        ];

        return $this->respondWithSuccess($result);
    }
}