<?php

use App\Models\ {
    Master,
    Outlet,
    Supervisor,
    Template
};
use App\Traits\HelpersTrait;

class TemplateDataSeeder extends BaseSeeder
{
    use HelpersTrait;

    private $outlet;
    private $master;
    private $template;
    private $supervisor;

    public function __construct(
        Outlet $outlet,
        Master $master,
        Template $template,
        Supervisor $supervisor
    ) {
        $this->outlet = $outlet;
        $this->master = $master;
        $this->template = $template;
        $this->supervisor = $supervisor;
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
        $randNum = rand(0, $totalUnits - 1);
        $unitKey = $totalUnits > 1 ? $randNum : 0;

        $unitKeys = array_keys($units);

        $selectedKey = $unitKeys[$unitKey];
        $selectedUnit = $units[$unitKeys[$unitKey]];

        return [$owned, $selectedKey, $selectedUnit];
    }

    private function getDefaultParams($item, $selectedKey, $selectedUnit, $outlet)
    {
        $defaultParams = [
            'product_id'   => $item->product_id,
            'product_code' => $item->product_code,
            'product_name' => $item->product_name,
            'unit_label'   => $selectedKey,
            'unit_value'   => $selectedUnit['value'],
            'outlet_id'    => $outlet->id,
            'manager_id'   => $outlet->manager->id
        ];

        return $defaultParams;
    }

    private function getFinalParams($defaultParams, $owned)
    {
        switch($owned) {
            case $owned == $this->OWNED_BY_BOTH:

                $params = [
                    0 => array_merge($defaultParams, [
                        'owned' => $this->OWNED_BY_LEADER_KITCHEN
                    ]),
                    1 => array_merge($defaultParams, [
                        'owned' => $this->OWNED_BY_OUTLET_SUPERVISOR
                    ])
                ];
                break;

            case $owned == $this->OWNED_BY_LEADER_KITCHEN:
                $params = [
                    0 => array_merge($defaultParams, [
                        'owned' => $this->OWNED_BY_LEADER_KITCHEN
                    ])
                ];
                break;

            case $owned == $this->OWNED_BY_OUTLET_SUPERVISOR:
                $params = [
                    0 => array_merge($defaultParams, [
                        'owned' => $this->OWNED_BY_OUTLET_SUPERVISOR
                    ])
                ];
                break;
        }

        return $params;
    }

    private function processTemplateCreation($params, $outlet)
    {
        foreach($params as $param) {
            $duty = ($param['owned'] == $this->OWNED_BY_LEADER_KITCHEN) ? 'production' : 'serve';

            $supervisor = $this->supervisor
                ->where('manager_id', $outlet->manager->id)
                ->where('outlet_id', $outlet->id)
                ->where('duty', $duty)
                ->first();

            $newParams = [];

            if ($supervisor) {
                $newParams = [
                    'supervisor_id' => $supervisor->id,
                    'supervisor_duty' => $supervisor->duty
                ];

                $param = array_merge($param, $newParams);
            }

            $this->createTemplate($param);
        }
    }

    private function createTemplateData($outlet, $masterDatas)
    {
        foreach ($masterDatas as $item)
        {
            list ($owned, $selectedKey, $selectedUnit) = $this->getRandomUnit($item);

            $defaultParams = $this->getDefaultParams(
                $item, $selectedKey, $selectedUnit, $outlet
            );

            $params = $this->getFinalParams($defaultParams, $owned);

            $this->processTemplateCreation($params, $outlet);
        }
    }

    public function run()
    {
        $query   = $this->outlet->with(['manager', 'supervisor']);
        $total   = $query->count();
        $outlets = $query->get();

        $masterDatas = $this->master->get();
        
        foreach($outlets as $key => $outlet) {
            $this->progressBar($key, $total - 1) .  "\n";
            $this->createTemplateData($outlet, $masterDatas);
        }

        echo "\nDone\n\n";
    }
}
