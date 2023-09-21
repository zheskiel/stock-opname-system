<?php
namespace Tests\Traits;

use App\Traits\HelpersTrait;
use App\Traits\HierarchyTrait;
use App\Models\ {
    Brand,
    District,
    Location,
    Manager,
    Outlet,
    Province,
    Regency,
    Staff,
    StaffType,
    Supervisor,
    SupervisorType
};

trait HierarchyDataTraits
{
    use HelpersTrait;
    use HierarchyTrait;

    public function initCreation()
    {
        $keys = [
            'brand',
            'province',
            'regency',
            'district',
            'location',
            'outlet',
        ];

        $filePath = '/../../database/seeds/StructureData.php';
        $structureData = include(__DIR__ . $filePath);

        list($svParams, $structure) = $structureData;

        foreach($keys as $key)
        {
            $items = $structure['brand'];

            $this->beforeCreateBrands($items, $key);
        }
    }

    public function createBrandsTrait($item)
    {
        $name = $item['name'];
        $slug = $this->processTitleSlug($name);

        $attributes = ['slug' => $slug];
        $newAttributes = array_merge([
            'name' => $name
        ], $attributes);

        $item = Brand::firstOrCreate(
            $attributes,
            factory(Brand::class)->raw($newAttributes)
        );

        return $item;
    }

    public function createProvincesTrait($item, $brand)
    {
        $name = $item['name'];
        $slug = $this->processTitleSlug($name);

        $attributes = ['slug' => $slug];
        $newAttributes = array_merge([
            'name' => $name,
            'brand_id' => $brand->id
        ], $attributes);

        $item = Province::firstOrCreate(
            $attributes,
            factory(Province::class)->raw($newAttributes)
        );

        return $item;
    }

    public function createRegenciesTrait($item, $province)
    {
        $name = $item['name'];
        $slug = $this->processTitleSlug($name);

        $attributes = ['slug' => $slug];
        $newAttributes = array_merge([
            'name' => $name,
            'province_id' => $province->id
        ], $attributes);

        $item = Regency::firstOrCreate(
            $attributes,
            factory(Regency::class)->raw($newAttributes)
        );

        return $item;
    }

    public function createDistrictsTrait($item, $regency)
    {
        $name = $item['name'];
        $slug = $this->processTitleSlug($name);

        $attributes = ['slug' => $slug];
        $newAttributes = array_merge([
            'name' => $name,
            'regency_id' => $regency->id
        ], $attributes);

        $item = District::firstOrCreate(
            $attributes,
            factory(District::class)->raw($newAttributes)
        );

        return $item;
    }

    public function createLocationsTrait($item, $district)
    {
        $name = $item['name'];
        $alias = $item['alias'];
        $slug = $this->processTitleSlug($name);

        $attributes = ['slug' => $slug];
        $newAttributes = array_merge([
            'name' => $name,
            'alias' => $alias,
            'district_id' => $district->id
        ], $attributes);

        $item = Location::firstOrCreate(
            $attributes,
            factory(Location::class)->raw($newAttributes)
        );

        return $item;
    }

    public function createOutletsTrait($item, $manager, $location)
    {
        $name = $item['name'];
        $slug = $this->processTitleSlug($name);

        $attributes = ['slug' => $slug];
        $newAttributes = array_merge([
            'name' => $name,
            'manager_id' => $manager->id,
            'location_id' => $location->id
        ], $attributes);

        $item = Outlet::firstOrCreate(
            $attributes,
            factory(Outlet::class)->raw($newAttributes)
        );

        return $item;
    }

    public function createManagersTrait($item)
    {
        $name = $item['name'];
        $slug = $this->processTitleSlug($name);

        $attributes = ['slug' => $slug];
        $newAttributes = array_merge([
            'name' => $name,
        ], $attributes);

        $item = Manager::firstOrCreate(
            $attributes,
            factory(Manager::class)->raw($newAttributes)
        );

        return $item;
    }

    public function createSupervisorsTrait($item, $supervisorType, $manager, $outlet)
    {
        $name = $item['title'] . ' - ' . $outlet->name;
        $slug = $this->processTitleSlug($name);

        $attributes = ['slug' => $slug];
        $newAttributes = array_merge([
            'name' => $name,
            'duty' => $supervisorType->duty,
            'supervisor_type_id' => $supervisorType->id,
            'manager_id'  => $manager->id,
            'outlet_id'   => $outlet->id
        ], $attributes);

        $item = Supervisor::firstOrCreate(
            $attributes,
            factory(Supervisor::class)->raw($newAttributes)
        );

        $manager->supervisor()->attach($item, ['outlet_id' => $outlet->id]);

        return $item;
    }

    public function getDutyType($slug)
    {
        $kitchenArr = ['leader-kitchen', 'head-production'];
        // $outletArr = ['outlet-supervisor', 'central-kitchen-supervisor'];

        $dutyTypeArr = ['production', 'serve'];

        return in_array($slug, $kitchenArr) ? $dutyTypeArr[0] : $dutyTypeArr[1];
    }

    public function createSupervisorTypesTrait($item)
    {
        $name = $item['title'];
        $slug = $this->processTitleSlug($name);
        $duty = $this->getDutyType($slug);

        $attributes = ['slug' => $slug];
        $newAttributes = array_merge([
            'name' => $name,
            'duty' => $duty
        ], $attributes);

        $item = SupervisorType::firstOrCreate(
            $attributes,
            factory(SupervisorType::class)->raw($newAttributes)
        );

        return $item;
    }

    public function createStaffTypesTrait($item, $supervisor)
    {
        $name = $item['title'];
        $slug = $this->processTitleSlug($name);

        $attributes = ['slug' => $slug];
        $newAttributes = array_merge([
            'name' => $name,
            'supervisor_id' => $supervisor->id
        ], $attributes);

        $item = StaffType::firstOrCreate(
            $attributes,
            factory(StaffType::class)->raw($newAttributes)
        );

        return $item;
    }

    public function createStaffsTrait($params)
    {
        list ($item, $staffType, $supervisor, $supervisorType, $manager, $outlet) = $params;

        $name = $item['name'];
        $slug = $this->processTitleSlug($name);

        $attributes = ['slug' => $slug];
        $newAttributes = array_merge([
            'name' => $name,
            'sv_type_label' => $supervisorType->name,
            'supervisor_id' => $supervisor->id,
            'staff_type_id' => $staffType->id,
            'manager_id'    => $manager->id,
            'outlet_id'     => $outlet->id,
        ], $attributes);

        $item = Staff::firstOrCreate(
            $attributes,
            factory(Staff::class)->raw($newAttributes)
        );

        return $item;
    }

    public function beforeCreateBrands($items, $lastKey) : void
    {
        foreach ($items as $item)
        {
            $brand = $this->createBrandsTrait($item);

            $key = 'province';

            if ($lastKey !== $key && isset($item[$key])) {
                $provinces = $item[$key];

                $this->beforeCreateProvinces($provinces, $brand, $lastKey);
            }
        }
    }

    public function beforeCreateProvinces($items, $brand, $lastKey) : void
    {
        foreach($items as $item)
        {
            $province = $this->createProvincesTrait($item, $brand);

            $key = 'regency';

            if ($lastKey !== $key && isset($item[$key])) {
                $regencies = $item[$key];

                $this->beforeCreateRegencies($regencies, $province, $lastKey);
            }
        }
    }

    public function beforeCreateRegencies($items, $province, $lastKey) : void
    {
        foreach ($items as $item)
        {
            $regency = $this->createRegenciesTrait($item, $province);

            $key = 'district';

            if ($lastKey !== $key && isset($item[$key])) {
                $districts = $item[$key];

                $this->beforeCreateDistrict($districts, $regency, $lastKey);
            }
        }
    }

    public function beforeCreateDistrict($items, $regency, $lastKey) : void
    {
        foreach ($items as $item)
        {
            $district = $this->createDistrictsTrait($item, $regency);

            $key = 'location';

            if ($lastKey !== $key && isset($item[$key])) {
                $locations = $item[$key];

                $this->beforeCreateLocation($locations, $district, $lastKey);
            }
        }
    }

    public function beforeCreateLocation($items, $district, $lastKey) : void
    {
        foreach ($items as $item)
        {
            $location = $this->createLocationsTrait($item, $district);

            $key = 'outlet';

            if ($lastKey !== $key && isset($item[$key])) {
                $outlets = $item[$key];

                $this->beforeCreateOutlet($outlets, $location);
            }
        }
    }

    public function beforeCreateOutlet($items, $location) : void
    {
        foreach ($items as $item)
        {
            $managerData = $item['manager'];

            $manager = $this->createManagersTrait($managerData);
            $outlet = $this->createOutletsTrait($item, $manager, $location);

            if (isset($managerData['supervisor'])) {
                $supervisorData = $managerData['supervisor'];

                $this->beforeCreateSupervisor($supervisorData, $manager, $outlet);
            }
        }
    }

    public function beforeCreateSupervisor($items, $manager, $outlet) : void
    {
        $itemsData = $items['level'];

        foreach ($itemsData as $item)
        {
            $supervisorType = $this->beforeCreateSupervisorType($item);
            $supervisor = $this->createSupervisorsTrait(
                $item, $supervisorType, $manager, $outlet
            );

            $params = [
                $item, $supervisor, $supervisorType, $manager, $outlet
            ];

            $this->beforeCreateStaffTypes($params);
        }
    }

    public function beforeCreateSupervisorType($item)
    {
        return $this->createSupervisorTypesTrait($item);
    }

    public function beforeCreateStaffTypes($params) : void
    {
        list ($items, $supervisor, $supervisorType, $manager, $outlet) = $params;

        $staffs = [];
        $level = $items['title'];

        $itemsData = $items['types'];

        foreach ($itemsData as $item)
        {
            $staffType = $this->createStaffTypesTrait($item, $supervisor);

            $newParams = [
                $item, $staffType, $supervisor, $supervisorType, $manager, $outlet
            ];

            $staffs[$level] = $this->beforeCreateStaffs($newParams);
        }

        $crStaff = Staff::where('id', $supervisor->supervisor_pic->id)->first();

        $crStaff->is_supervisor = 1;
        $crStaff->supervisor_id = NULL;
        $crStaff->staff_type_id = NULL;

        $crStaff->save();
    }

    public function beforeCreateStaffs($params, $staffs = [])
    {
        list ($items, $staffType, $supervisor, $supervisorType, $manager, $outlet) = $params;

        $itemsData = $items['staff'];

        foreach($itemsData as $item)
        {
            $newParams = [
                $item, $staffType, $supervisor, $supervisorType, $manager, $outlet
            ];

            $staff = $this->createStaffsTrait($newParams);

            $supervisor->staff_id = $staff->id;
            $supervisor->save();

            $supervisor->multiPivotType()->attach($staffType, ['staff_id' => $staff->id]);

            $staffs[] = $staff;
        }

        return $staffs;
    }
}