<?php

use App\Models\ {
    Brand,
    Province,
    Regency,
    District,
    Location,
    Outlet,
    Supervisor,
    StaffType,
    Staff,
    Manager
};

class DataSeeder extends BaseSeeder
{
    private $brand;
    private $province;

    public function __construct(
        Brand $brand,
        Province $province,
        Regency $regency,
        District $district,
        Location $location,
        Outlet $outlet,
        Supervisor $supervisor,
        StaffType $type,
        Staff $staff,
        Manager $manager
    ) {
        $this->brand      = $brand;
        $this->province   = $province;
        $this->regency    = $regency;
        $this->district   = $district;
        $this->location   = $location;
        $this->outlet     = $outlet;
        $this->supervisor = $supervisor;
        $this->type       = $type;
        $this->staff      = $staff;
        $this->manager    = $manager;
    }

    public function run()
    {
        $parameters = require_once(__DIR__ . '/StructureData.php');

        foreach($parameters as $params) {
            $this->buildProcess($params);
        }

        $this->extraProcess();
    }

    private function buildProcess($parameters)
    {

        list($current, $limit, $params) = $parameters;

        for ($x=1; $x <= $limit; $x++) {
            $name = $current.$x;

            $defaultParams = [
                'name' => $name,
                'slug' => $this->processTitleSlug($name),
            ];

            $newParams = [];

            if (count($params) > 0) {
                foreach ($params as $k => $param) {
                    $pKey = $params[$k];

                    $prevTarget = $this->{$pKey}->inRandomOrder()->first();
                    $newParams[$pKey.'_id'] = $prevTarget->id;
                }
            }

            $finalParams = array_merge($defaultParams, $newParams);

            $model = new $this->{strtolower($current)};
            $model->create($finalParams);
        }
    }

    public function extraProcess()
    {
        $model = $this->outlet;
        $total = $model->count();

        for ($x = 1; $x <= $total; $x++) {
            $manager = $this->manager->with(['supervisor'])->inRandomOrder()->first();

            $query = $model->inRandomOrder()->first();

            $query->manager_id = (int) $manager->id;

            $query->save();

            $staffs = $this->staff->get();

            foreach($staffs as $staff) {
                $staff->outlet_id       = $x;
                $staff->manager_id      = $x;
                $staff->supervisor_id   = $x;
                $staff->type_id         = $x;
                $staff->save();
            }
        }
    }
}
