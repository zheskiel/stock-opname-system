<?php

use App\Models\ {
    Master,
    Outlet,
    Supervisor,
    Details,
    Templates
};
use App\Traits\HelpersTrait;

class TemplateDataSeeder extends BaseSeeder
{
    use HelpersTrait;

    private $outlet;
    private $master;
    private $template;
    private $supervisor;
    private $templateDetails;

    public function __construct(
        Outlet $outlet,
        Master $master,
        Templates $template,
        Supervisor $supervisor,
        Details $templateDetails
    ) {
        $this->outlet = $outlet;
        $this->master = $master;
        $this->template = $template;
        $this->supervisor = $supervisor;
        $this->templateDetails = $templateDetails;
    }

    private function createTemplateForSupervisors($outlet)
    {
        $supervisors = $outlet->supervisor;

        foreach($supervisors as $supervisor) {
            $title = "Template $outlet->name $supervisor->name";
            $slug = $this->processTitleSlug($title);

            $this->template->create([
                'title'           => $title,
                'slug'            => $slug,
                'supervisor_id'   => $supervisor->id,
                'supervisor_duty' => $supervisor->duty,
                'outlet_id'       => $outlet->id,
                'manager_id'      => $outlet->manager->id,
                'owned'           => 0,
                'status'          => 0
            ]);
        }
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

    private function createTemplateDetails()
    {
        $templates = $this->template->get();

        foreach($templates as $template) {
            for ($x = 0; $x < rand(50, 250); $x++) {
                $item = $this->master->inRandomOrder()->first();

                // list ($selectedKey, $selectedUnit) = $this->getRandomUnit($item);

                $detail = $this->templateDetails->create([
                    'templates_id' => $template->id,
                    'product_id'   => $item->product_id,
                    'product_code' => $item->product_code,
                    'product_name' => $item->product_name,
                    'units'        => $item->units,
                    'receipt_tolerance' => $item->receipt_tolerance,
                ]);

                $template->details()->attach($detail);
            }
        }
    }

    public function run()
    {
        $model = $this->outlet;
        $query = $model->with(['manager', 'supervisor']);

        $total = $query->count();
        $items = $query->get();
        
        foreach($items as $key => $item) {
            $this->progressBar($key, $total - 1) .  "\n";
            $this->createTemplateForSupervisors($item);
        }

        $this->createTemplateDetails();

        echo "\nDone\n\n";
    }
}
