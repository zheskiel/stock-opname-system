<?php

use App\Models\{
    Forms,
    Items,
};

use App\Traits\HelpersTrait;
use App\Traits\HierarchyTrait;

class FormItemsTableSeeder extends BaseSeeder
{
    use HelpersTrait;
    use HierarchyTrait;

    private $forms;
    private $items;

    public function __construct(
        Forms $forms,
        Items $items
    ) {
        $this->forms      = $forms;
        $this->items      = $items;
    }

    private function getRandomUnit($item) : array
    {
        $units = json_decode($item->units, true);

        $totalUnits = count($units);
        $randNum = rand(0, $totalUnits - 1);
        $unitKey = $totalUnits > 1 ? $randNum : 0;

        $unitKeys = array_keys($units);

        $selectedKey = $unitKeys[$unitKey];
        $selectedUnit = $units[$unitKeys[$unitKey]];

        return [$selectedKey, $selectedUnit];
    }

    public function run()
    {
        $forms = $this->forms
            ->withCount('template')
            ->with([
                'template' => function($query) {
                    return $query->with(['details']);
                }
            ])->get();

        foreach($forms as $form) {
            $template = $form->template;

            $details = $template->details;
            
            for ($x = 0; $x < rand(50, 250); $x++) {
                $detail =  $details[rand(0, count($details) - 1)];

                $units = $this->getRandomUnit($detail);

                list ($selectedKey, $selectedUnit) = $units;

                $item = $this->items->create([
                    'forms_id'      => $form->id,
                    'product_id'    => $detail->product_id,
                    'product_code'  => $detail->product_code,
                    'product_name'  => $detail->product_name,
                    'unit'          => $selectedKey,
                    'value'         => $selectedUnit['value'],
                ]);

                $form->items()->attach($item);
            }
        }
    }
}