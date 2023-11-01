<?php

use Carbon\Carbon;
use Faker\Factory as Faker;

use App\Models\{
    Forms,
    Items,
    Daily,
    Notes,
    Staff
};

use App\Traits\HelpersTrait;
use App\Traits\HierarchyTrait;

class FormItemsTableSeeder extends BaseSeeder
{
    use HelpersTrait;
    use HierarchyTrait;

    private $forms;
    private $items;
    private $daily;
    private $notes;
    private $faker;

    public function __construct(
        Forms $forms,
        Items $items,
        Daily $daily,
        Notes $notes
    ) {
        $this->forms = $forms;
        $this->items = $items;
        $this->daily = $daily;
        $this->notes = $notes;
        $this->faker =  Faker::create();
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
            
            for ($x = 0; $x < rand(500, 1000); $x++) {
                $detail =  $details[rand(0, count($details) - 1)];

                $units = $this->getRandomUnit($detail);

                list ($selectedKey, $selectedUnit) = $units;

                $exist = $this->items
                    ->where('product_id', $detail->product_id)
                    ->first();

                if (!$exist) {
                    $value = rand(10, 50);
                    $item = $this->items->create([
                        'forms_id'      => $form->id,
                        'product_id'    => $detail->product_id,
                        'product_code'  => $detail->product_code,
                        'product_name'  => $detail->product_name,
                        'unit'          => str_replace(" ", "", $selectedKey),
                        'unit_value'    => $selectedUnit['value'],
                        'unit_sku'      => $selectedUnit['sku']
                    ]);

                    $today = Carbon::now()->format('Y-m-d');

                    $daily = $this->daily->create([
                        'forms_id'   => $form->id,
                        'items_id'   => $item->id,
                        'items_code' => $item->product_code,
                        'date'       => $today,
                        'value'      => $value
                    ]);

                    $note = $this->notes->firstOrCreate([
                        'forms_id' => $form->id,
                        'staff_id' => $form->staff_id,
                        'date'     => $today,
                    ],[
                        'forms_id' => $form->id,
                        'staff_id' => $form->staff_id,
                        'date'     => $today,
                        'notes'    => $this->faker->paragraph(10)
                    ]);

                    $form->daily()->attach($daily);
                    $form->notes()->syncWithoutDetaching($note);

                    $form->items()->attach($item);
                }
            }
        }
    }
}