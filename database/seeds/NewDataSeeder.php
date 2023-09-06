<?php

use App\Models\ {
    Admin,
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

class NewDataSeeder extends BaseSeeder
{
    public function __construct(
        Admin $admin,
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
        $this->admin      = $admin;
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

        $this->structure = require_once(__DIR__ . '/StructureData2.php');
    }

    public function run()
    {
        $this->buildBrand();
    }

    public function buildBrand() :void
    {
        echo "Build Brand Data\n\n";

        $admin = $this->admin->first();

        $brandBase = $this->structure['brand'][0];
        $brandName = $brandBase['name'];

        $this->brandModel = new $this->brand;
        $this->brandModel = $this->brandModel->create([
            'name'      => $brandName,
            'slug'      => $this->processTitleSlug($brandName),
            'admin_id'  => $admin->id
        ]);

        $this->buildProvince($brandBase);
    }

    public function buildProvince($brandBase, $extraParams = []) :void
    {
        if (isset($brandBase['province'])) {
            echo "Build Province Data\n\n";

            $provinces = $brandBase['province'];

            foreach($provinces as $provItem) {
                $provinceName = $provItem['name'];

                $defaultParams = [
                    'name'     => $provinceName,
                    'slug'     => $this->processTitleSlug($provinceName),
                ];

                $extraParams = array_merge($extraParams, [
                    'brand_id' => $this->brandModel->id
                ]);

                $finalParams = array_merge($defaultParams, $extraParams);

                $this->provinceModel = new $this->province;
                $this->provinceModel = $this->provinceModel->create($finalParams);

                $this->buildRegency($provItem);
            }
        }
    }

    public function buildRegency($provItem) :void
    {
        if (isset($provItem['regency'])) {
            echo "Build Regency Data\n\n";

            $regencies = $provItem['regency'];

            foreach($regencies as $regItem) {
                $regencyName = $regItem['name'];

                $defaultParams = [
                    'name' => $regencyName,
                    'slug' => $this->processTitleSlug($regencyName),
                ];

                $extraParams = [
                    'province_id' => $this->provinceModel->id
                ];

                $finalParams = array_merge($defaultParams, $extraParams);

                $this->regencyModel = new $this->regency;
                $this->regencyModel = $this->regencyModel->create($finalParams);

                $this->buildDistrict($regItem);
            }
        }
    }

    public function buildDistrict($regItem) :void
    {
        if (isset($regItem['district'])) {
            echo "Build District Data\n\n";

            $districts = $regItem['district'];

            foreach($districts as $disItem) {
                $districtName = $disItem['name'];

                $defaultParams = [
                    'name' => $districtName,
                    'slug' => $this->processTitleSlug($districtName),
                ];

                $extraParams = [
                    'regency_id'    => $this->regencyModel->id
                ];

                $finalParams = array_merge($defaultParams, $extraParams);

                $this->districtModel = new $this->district;
                $this->districtModel = $this->districtModel->create($finalParams);

                $this->buildLocation($disItem);
            }
        };
    }

    public function buildLocation($disItem) :void
    {
        if (isset($disItem['location'])) {
            echo "Build Location Data\n\n";

            $locations = $disItem['location'];

            foreach($locations as $locItem) {
                $locationName = $locItem['name'];
                $locationAlias = $locItem['alias'];

                $defaultParams = [
                    'name'  => $locationName,
                    'slug'  => $this->processTitleSlug($locationName),
                    'alias' => $locationAlias,
                ];

                $extraParams = [
                    'district_id' => $this->districtModel->id
                ];

                $finalParams = array_merge($defaultParams, $extraParams);

                $this->locationModel = new $this->location;
                $this->locationModel = $this->locationModel->create($finalParams);

                $this->buildOutlet($locItem);
            }
        }
    }

    public function buildOutlet($locItem) :void
    {
        if (isset($locItem['outlet'])) {
            echo "Build Outlet Data\n\n";

            $outlets = $locItem['outlet'];

            foreach($outlets as $outItem) {
                $outletName = $outItem['name'];

                $defaultParams = [
                    'name' => $outletName,
                    'slug' => $this->processTitleSlug($outletName),
                ];

                $extraParams = [
                    'location_id' => $this->locationModel->id
                ];

                $finalParams = array_merge($defaultParams, $extraParams);

                $this->outletModel = new $this->outlet;
                $this->outletModel = $this->outletModel->create($finalParams);

                $this->buildManager($outItem);
            }
        }
    }

    public function buildManager($outItem):void
    {
        if(isset($outItem['manager'])) {
            echo "Build " . $this->outletModel->name . "'s Manager Data\n\n";

            $manager = $outItem['manager'];
            $managerName = $manager['name'];

            $slug = $this->processTitleSlug($managerName);

            $defaultParams = [
                'name'      => $managerName,
                'slug'      => $slug,
                'email'     => "$slug@gmail.com",
                'password'  => bcrypt('test123')
            ];

            $extraParams = [
                'outlet_id'   => $this->outletModel->id
            ];

            $finalParams = array_merge($defaultParams, $extraParams);
            
            $this->managerModel = new $this->manager;
            $this->managerModel = $this->managerModel
                ->firstOrCreate(['slug' => $slug], $finalParams);

            $this->outletModel
                ->update([
                    'manager_id' => $this->managerModel->id
                ]);

            $this->buildSupervisor($manager);
        }
    }

    public function buildSupervisor($svItems):void
    {
        if (isset($svItems['supervisor'])) {
            echo "Build " . $this->outletModel->name . "'s Supervisor Data\n\n";

            $supervisor = $svItems['supervisor'];

            $svLevel = $supervisor['level'];

            foreach($svLevel as $level)
            {
                $levelTitle = $level['title'];

                $defaultParams = [
                    'name' => $levelTitle,
                    'slug' => $this->processTitleSlug($levelTitle),
                ];

                $extraParams = [
                    'outlet_id'   => $this->outletModel->id,
                    'manager_id'  => $this->managerModel->id
                ];

                $finalParams = array_merge($defaultParams, $extraParams);

                $this->svModel = $this->supervisor
                    ->create($finalParams);

                $this->buildStaffTypes($level);
            }
        }
    }

    public function buildStaffTypes($level):void
    {
        if (isset($level['types'])) {
            echo "Build " . $this->outletModel->name . "'s Staff Types Data\n\n";

            $types = $level['types'];
            
            foreach($types as $type) {
                $typeTitle = $type['title'];
                $typeSlug = $this->processTitleSlug($typeTitle);

                $defaultParams = [
                    'name' => $typeTitle,
                    'slug'  => $typeSlug
                ];

                $extraParams = [
                    'supervisor_id' => $this->svModel->id
                ];

                $finalParams = array_merge($defaultParams, $extraParams);

                $this->staffTypesModel = $this->type
                    ->firstOrCreate(['slug' => $typeSlug], $finalParams);
                
                $this->buildStaff($type);
            }
        }
    }

    public function buildStaff($type):void
    {
        if (isset($type['staff'])) {
            echo "Build " . $this->outletModel->name . "'s Staff Data\n\n";

            $staffs = $type['staff'];

            foreach($staffs as $staff)
            {
                $staffName = $staff['name'];
                $staffSlug = $this->processTitleSlug($staffName);

                $defaultParams = [
                    'name'      => $staffName,
                    'slug'      => $staffSlug,
                    'email'     => "$staffSlug@gmail.com",
                    'password'  => bcrypt('test123')
                ];

                $extraParams = [
                    'outlet_id'     => $this->outletModel->id,
                    'manager_id'    => $this->managerModel->id,
                    'supervisor_id' => $this->svModel->id,
                    'type_id'       => $this->staffTypesModel->id,
                ];

                $finalParams = array_merge($defaultParams, $extraParams);

                $currentStaff = $this->staff
                    ->firstOrCreate(['slug' => $staffSlug], $finalParams);
            }
        }
    }
}
