<?php

use App\Services\ {
    BrandService,
    ManagerService,
    SupervisorService,
    SupervisorTypeService,
    StaffService,
    StaffTypeService,
    LocationService,
    OutletService,
    DistrictService,
    RegencyService,
    ProvinceService
};

use App\Models\Admin;

class HierarchyDataSeeder extends BaseSeeder
{
    public function __construct(
        BrandService $brandService,
        ManagerService $managerService,
        LocationService $locationService,
        OutletService $outletService,
        ProvinceService $provinceService,
        DistrictService $districtService,
        RegencyService $regencyService,
        StaffService $staffService,
        StaffTypeService $staffTypeService,
        SupervisorService $supervisorService,
        SupervisorTypeService $supervisorTypeService,
        Admin $admin
    ) {
        $this->admin                 = $admin;
        $this->brandService          = $brandService;
        $this->managerService        = $managerService;
        $this->staffService          = $staffService;
        $this->staffTypeService      = $staffTypeService;
        $this->supervisorService     = $supervisorService;
        $this->supervisorTypeService = $supervisorTypeService;
        $this->locationService       = $locationService;
        $this->outletService         = $outletService;
        $this->districtService       = $districtService;
        $this->regencyService        = $regencyService;
        $this->provinceService       = $provinceService;

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
            $this->supervisorTypeService->createSeederData([$param]);
        }
    }

    public function buildBrand() :void
    {
        echo "Build Brand Data\n\n";

        $admin = $this->admin->first();

        $brandBase = $this->structure['brand'][0];

        $this->brandModel = $this->brandService->createSeederData([$brandBase, $admin]);

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
                $this->provinceModel = $this->provinceService->createSeederData([$provItem, $this->brandModel]);

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
                $this->regencyModel = $this->regencyService->createSeederData([$regItem, $this->provinceModel]);

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
                $this->districtModel = $this->districtService->createSeederData([$disItem, $this->regencyModel]);

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
                $this->locationModel = $this->locationService->createSeederData([$locItem, $this->districtModel]);

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
                $params = [$outItem, $this->locationModel];

                $this->outletModel = $this->outletService->createSeederData($params);

                $this->buildManager($outItem);
            }
        }
    }

    public function buildManager($outItem):void
    {
        if(isset($outItem['manager'])) {
            echo "Build " . $this->outletModel->name . "'s Manager Data\n\n";

            $this->managerModel = $this->managerService
                ->createSeederData([ $outItem, $this->outletModel ]);

            $this->outletService->updateByParams([
                'manager_id' => $this->managerModel->id
            ]);

            $this->buildSupervisor($outItem['manager']);
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
                $svType = $this->supervisorTypeService->getFirstItemByQuery($level);
                $this->svModel = $this->supervisorService
                    ->createSeederData([
                        $svType,
                        $this->outletModel,
                        $this->managerModel
                    ]);

                $choosenSV = $this->buildStaffTypes($level);

                $crStaff = $this->staffService
                    ->searchByFirstAndParam('slug', $this->processTitleSlug($choosenSV));

                $this->supervisorService->updateByParams([ $crStaff ]);
                $this->staffService->updateByParams([
                    'is_supervisor' => true,
                    'supervisor_id' => NULL,
                    'staff_type_id' => NULL
                ]);
            }
        }
    }

    public function buildStaffTypes($level, $choosenStaff = ''):string
    {
        if (isset($level['types'])) {
            echo "Build " . $this->outletModel->name . "'s Staff Types Data\n\n";

            $types = $level['types'];
            
            $staffList = [];

            foreach($types as $type) {
                $this->staffTypesModel = $this->staffTypeService
                    ->createSeederData([$type, $this->svModel]);
                
                $staffs = $this->buildStaff($type);

                $staffList = array_merge($staffList, $staffs);
            }

            $randStaff = rand(0, count($staffList) - 1);
            $choosenStaff = $staffList[$randStaff];

            return $choosenStaff;
        }
    }

    public function buildStaff($type, $staffLists = []):array
    {
        if (isset($type['staff'])) {
            echo "Build " . $this->staffTypesModel->name . "-" . $this->outletModel->name . "'s Staff Data\n\n";

            $staffs = $type['staff'];

            foreach($staffs as $k => $staff)
            {
                $currentStaff = $this->staffService
                    ->createSeederData([
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
