<?php

use App\Services\ {
    StaffService
};

use App\Models\ {
    Admin,
    Brand,
    Province,
    Regency,
    District,
    Location,
    Outlet,
    Supervisor,
    SupervisorType,
    StaffType,
    Staff,
    Manager
};

class NewDataSeeder extends BaseSeeder
{
    public function __construct(
        StaffService $staffService,
        Admin $admin,
        Brand $brand,
        Province $province,
        Regency $regency,
        District $district,
        Location $location,
        Outlet $outlet,
        Supervisor $supervisor,
        SupervisorType $supervisorType,
        StaffType $type,
        Staff $staff,
        Manager $manager
    ) {
        $this->admin          = $admin;
        $this->brand          = $brand;
        $this->province       = $province;
        $this->regency        = $regency;
        $this->district       = $district;
        $this->location       = $location;
        $this->outlet         = $outlet;
        $this->supervisor     = $supervisor;
        $this->supervisorType = $supervisorType;
        $this->type           = $type;
        $this->staff          = $staff;
        $this->manager        = $manager;

        $this->staffService   = $staffService;

        list($svParams, $parameters) = require_once(__DIR__ . '/StructureData.php');

        $this->structure = $parameters;
        $this->svParams = $svParams;
    }

    public function run()
    {
        $this->buildUtilities();
        $this->buildBrand();
    }

    private function buildUtilities()
    {
        foreach($this->svParams as $param) {
            $name = $param;
            $slug = $this->processTitleSlug($name);

            $this->supervisorType->firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'slug' => $slug
                ]);
        }
    }

    public function buildBrand() :void
    {
        echo "Build Brand Data\n\n";

        $admin = $this->admin->first();

        $brandBase = $this->structure['brand'][0];
        $brandName = $brandBase['name'];

        $this->brandModel = new $this->brand;
        $this->brandModel = $this->brandModel
            ->create([
                'name'      => $brandName,
                'slug'      => $this->processTitleSlug($brandName),
                'admin_id'  => $admin->id
            ]);

        $this->buildProvince($brandBase);

        $admin->update([
            'brand_id' => $this->brandModel->id
        ]);
    }

    public function buildProvince($brandBase) :void
    {
        if (isset($brandBase['province'])) {
            echo "Build Province Data\n\n";

            $provinces = $brandBase['province'];

            foreach($provinces as $provItem) {
                $provinceName = $provItem['name'];

                $this->provinceModel = new $this->province;
                $this->provinceModel = $this->provinceModel
                    ->create([
                        'name'     => $provinceName,
                        'slug'     => $this->processTitleSlug($provinceName),
                        'brand_id' => $this->brandModel->id
                    ]);

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

                $this->regencyModel = new $this->regency;
                $this->regencyModel = $this->regencyModel
                    ->create([
                        'name'        => $regencyName,
                        'slug'        => $this->processTitleSlug($regencyName),
                        'province_id' => $this->provinceModel->id
                    ]);

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

                $this->districtModel = new $this->district;
                $this->districtModel = $this->districtModel
                    ->create([
                        'name'       => $districtName,
                        'slug'       => $this->processTitleSlug($districtName),
                        'regency_id' => $this->regencyModel->id
                    ]);

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

                $this->locationModel = new $this->location;
                $this->locationModel = $this->locationModel
                    ->create([
                        'name'  => $locationName,
                        'alias' => $locationAlias,
                        'slug'  => $this->processTitleSlug($locationName),
                        'district_id' => $this->districtModel->id
                    ]);

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

                $this->outletModel = new $this->outlet;
                $this->outletModel = $this->outletModel
                    ->create([
                        'name' => $outletName,
                        'slug' => $this->processTitleSlug($outletName),
                        'location_id' => $this->locationModel->id
                    ]);

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

            $this->managerModel = new $this->manager;
            $this->managerModel = $this->managerModel
                ->firstOrCreate(
                    ['slug' => $slug],
                    [
                        'name'        => $managerName,
                        'slug'        => $slug,
                        'email'       => $slug . "@gmail.com",
                        'password'    => bcrypt('test123'),
                        'outlet_id'   => $this->outletModel->id
                    ]);

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
                $levelSlug = $this->processTitleSlug($levelTitle);

                $svType = $this->supervisorType
                    ->where('slug', $levelSlug)
                    ->first();

                $this->svModel = $this->supervisor
                    ->create([
                        'name'               => $svType->name . ' - ' . $this->outletModel->name,
                        'slug'               => $svType->slug . '-' . $this->outletModel->slug,
                        'supervisor_type_id' => $svType->id,
                        'outlet_id'          => $this->outletModel->id,
                        'manager_id'         => $this->managerModel->id
                    ]);

                $choosenSV = $this->buildStaffTypes($level);
                $choosenSVSlug = $this->processTitleSlug($choosenSV);

                $query = $this->staff->where('slug', $choosenSVSlug);
                $crStaff = $query->first();

                $this->svModel->update([
                    'staff_id' => $crStaff->id
                ]);

                $crStaff->update([
                    'is_supervisor' => true,
                    'supervisor_id' => NULL,
                    'staff_type_id' => NULL
                ]);
            }
        }
    }

    public function buildStaffTypes($level):string
    {
        $choosenStaff = '';

        if (isset($level['types'])) {
            echo "Build " . $this->outletModel->name . "'s Staff Types Data\n\n";

            $types = $level['types'];
            
            $staffList = [];

            foreach($types as $type) {
                $typeTitle = $type['title'];
                $typeSlug = $this->processTitleSlug($typeTitle);

                $this->staffTypesModel = $this->type
                    ->firstOrCreate(
                        ['slug' => $typeSlug],
                        [
                            'name' => $typeTitle,
                            'slug'  => $typeSlug,
                            'supervisor_id' => $this->svModel->id
                        ]);
                
                $staffs = $this->buildStaff($type);

                $staffList = array_merge($staffList, $staffs);
            }

            $randStaff = rand(0, count($staffList) - 1);
            $choosenStaff = $staffList[$randStaff];

            return $choosenStaff;
        }
    }

    public function buildStaff($type):array
    {
        $staffLists = [];

        if (isset($type['staff'])) {
            echo "Build " . $this->staffTypesModel->name . "-" . $this->outletModel->name . "'s Staff Data\n\n";

            $staffs = $type['staff'];

            foreach($staffs as $k => $staff)
            {
                $currentStaff = $this->staffService
                    ->createData([
                        $staff,
                        $this->staffTypesModel,
                        $this->outletModel,
                        $this->managerModel,
                        $this->svModel
                    ]);

                $staffLists[] = $currentStaff->name;
            }
        }

        return $staffLists;
    }
}
