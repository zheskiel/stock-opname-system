<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;

use App\Models\Brand;
use App\Traits\HelpersTrait;
use App\Traits\HierarchyTrait;

class HierarchyController extends BaseController
{
    use HelpersTrait;
    use HierarchyTrait;

    private $brand;
    private $keyList;

    public function __construct(
        Brand $brand
    ) {
        $this->brand = $brand;
        $this->keyList = [
            'province',
            'regency',
            'district',
            'location',
            'outlet',
            'manager',
            'supervisor',
            'type',
            'staffs'
        ];
    }

    private function formatDataItems($current, $index = 0)
    {
        $index += 1;
        $type = $this->keyList[$index];

        foreach ($current->children as $item) {
            $current = $item;

            $current->children = $type == "manager" ? [$current->{$type}->makeHidden('id')] : $current->{$type}->makeHidden('id');
            unset( $current->{$type} );

            if ($index < count($this->keyList) - 1) {
                $this->formatDataItems($current, $index, $this->keyList[$index]);
            }
        }
    }

    public function fetchHierarchy()
    {
        $items = $this->brand->with($this->loadProvinceWithRegency())->first();
        
        $items->children = $items->province->makeHidden('id');
        unset( $items->province );

        $this->formatDataItems($items);

        return $this->respondWithSuccess($items);
    }
}