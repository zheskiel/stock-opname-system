<?php

use App\Models\ {
    Master,
    Outlet,
    Template
};
use App\Traits\HelpersTrait;

class TemplateDataSeeder extends BaseSeeder
{
    use HelpersTrait;

    private $master;
    private $template;

    public function __construct(
        Master $master,
        Template $template
    ) {
        $this->master = $master;
        $this->template = $template;
    }

    private function createTemplate($params)
    {
        return $this->template->create($params);
    }

    private function getRandomUnit($item) : array
    {
        $owned = $item['owned'];

        $units = json_decode($item->units, true);

        $totalUnits = count($units);
        $unitKey = $totalUnits > 0 ? rand(0, $totalUnits - 1) : 0;
        $unitKeys = array_keys($units);

        $selectedKey = $unitKeys[$unitKey];
        $selectedUnit = $units[$unitKeys[$unitKey]];

        return [$owned, $selectedKey, $selectedUnit];
    }

    private function createTemplateData($outlet, $masterDatas)
    {
        foreach ($masterDatas as $item)
        {
            list ($owned, $selectedKey, $selectedUnit) = $this->getRandomUnit($item);

            $defaultParam = [
                'product_code' => $item->product_code,
                'product_name' => $item->product_name,
                'unit_label' => $selectedKey,
                'unit_value' => $selectedUnit['value'],
                'outlet_id'  => $outlet->id,
                'manager_id' => $outlet->manager->id
            ];

            switch($owned) {
                case $owned == $this->OWNED_BY_BOTH:

                    $params = [
                        0 => array_merge($defaultParam, [
                            'owned' => $this->OWNED_BY_LEADER_KITCHEN
                        ]),
                        1 => array_merge($defaultParam, [
                            'owned' => $this->OWNED_BY_OUTLET_SUPERVISOR
                        ])
                    ];
                    break;

                case $owned == $this->OWNED_BY_LEADER_KITCHEN:
                    $params = [
                        0 => array_merge($defaultParam, [
                            'owned' => $this->OWNED_BY_LEADER_KITCHEN
                        ])
                    ];
                    break;

                case $owned == $this->OWNED_BY_OUTLET_SUPERVISOR:
                    $params = [
                        0 => array_merge($defaultParam, [
                            'owned' => $this->OWNED_BY_OUTLET_SUPERVISOR
                        ])
                    ];
                    break;
            }

            foreach($params as $param) {
                $this->createTemplate($param);
            }
        }
    }

    public function run()
    {
        $outlets = Outlet::with(['manager', 'supervisor'])->get();
        dd( $outlets[0]->toArray() );
        $masterDatas = Master::get();
        
        foreach($outlets as $outlet) {
            $this->createTemplateData($outlet, $masterDatas);
        }
    }
}
