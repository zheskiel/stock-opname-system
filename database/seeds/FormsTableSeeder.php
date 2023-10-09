<?php

use App\Models\{
    Forms,
    Manager,
    Staff,
    Supervisor,
    Templates
};

use App\Traits\HelpersTrait;
use App\Traits\HierarchyTrait;

class FormsTableSeeder extends BaseSeeder
{
    use HelpersTrait;
    use HierarchyTrait;

    private $forms;
    private $manager;
    private $staff;
    private $supervisor;
    private $templates;

    public function __construct(
        Forms $forms,
        Manager $manager,
        Staff $staff,
        Supervisor $supervisor,
        Templates $templates
    ) {
        $this->forms      = $forms;
        $this->manager    = $manager;
        $this->staff      = $staff;
        $this->supervisor = $supervisor;
        $this->templates  = $templates;
    }

    public function run()
    {
        $manager = Manager::with($this->loadSupervisorWithSupervisorPicAndType())->first();
        $supervisors = $manager->supervisor;

        $model = $this->forms;

        foreach ($supervisors as $supervisor) {
            $template = $this->templates
                ->with(['details'])
                ->where('supervisor_id', $supervisor->id)    
                ->where('manager_id', $manager->id)
                ->inRandomOrder()
                ->first();

            $types = $supervisor->type;

            foreach ($types as $type) {
                $staffs = $type->staffs;

                foreach ($staffs as $staff) {
                    
                    $model->create([
                        'template_id'   => $template->id,
                        'manager_id'    => $manager->id,
                        'supervisor_id' => $supervisor->id,
                        'staff_id'      => $staff->id
                    ]);

                }
            }
        }
    }
}