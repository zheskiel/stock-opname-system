<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Traits\HierarchyDataTraits;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Traits\HelpersTrait;
use App\Models\ {
    District,
    Location,
    Outlet,
    Province,
    Regency,
    Staff,
    Supervisor
};

class HierarchyCreationTest extends TestCase
{
    use HierarchyDataTraits;
    use RefreshDatabase;
    use HelpersTrait;

    private $structure, $svParams;

    /**
     * @dataProvider hierarchyProvider
     */
    public function testCreation($lastKey)
    {
        $filePath = '/../../database/seeds/StructureData.php';
        $structureData = include(__DIR__ . $filePath);

        list($svParams, $structure) = $structureData;

        $items = $structure['brand'];

        $this->beforeCreateBrands($items, $lastKey);
        $this->afterAllCreation();
    }

    public function afterAllCreation()
    {
        $isSupervisor = 1;

        $supervisor = Supervisor::inRandomOrder()->first();

        if (isset($supervisor->supervisor_pic)) {
            $supervisorPic = $supervisor->supervisor_pic;

            $this->assertSame((int) $supervisorPic->is_supervisor, $isSupervisor);
        }
    }

    public function hierarchyProvider()
    {
        return [
            ['brand'],
            ['province'],
            ['regency'],
            ['district'],
            ['location'],
            ['outlet'],
        ];
    }

    private function generateNameAndSlug($params, $target = 'name')
    {
        $name = $params[$target];
        $slug = $this->processTitleSlug($name);

        return [$name, $slug];
    }

    private function createBrands($params)
    {
        list($name, $slug) = $this->generateNameAndSlug($params);

        $item = $this->createBrandsTrait($params);
        
        $this->assertEquals($item->name, $name);
        $this->assertEquals($item->slug, $slug);

        return $item;
    }

    private function createProvinces($params, $brand)
    {
        list($name, $slug) = $this->generateNameAndSlug($params);

        $item = $this->createProvincesTrait($params, $brand);

        $this->assertEquals($item->name, $name);
        $this->assertEquals($item->slug, $slug);
        $this->assertEquals($item->brand_id, $brand->id);

        return $item;
    }

    private function createRegencies($params, $province)
    {
        list($name, $slug) = $this->generateNameAndSlug($params);

        $item = $this->createRegenciesTrait($params, $province);

        $this->assertEquals($item->name, $name);
        $this->assertEquals($item->slug, $slug);
        $this->assertEquals($item->province_id, $province->id);

        return $item;
    }

    private function createDistricts($params, $regency)
    {
        list($name, $slug) = $this->generateNameAndSlug($params);

        $item = $this->createDistrictsTrait($params, $regency);

        $this->assertEquals($item->name, $name);
        $this->assertEquals($item->slug, $slug);
        $this->assertEquals($item->regency_id, $regency->id);

        return $item;
    }

    private function createLocations($params, $district)
    {
        list($name, $slug) = $this->generateNameAndSlug($params);

        $item = $this->createLocationsTrait($params, $district);

        $this->assertEquals($item->name, $name);
        $this->assertEquals($item->slug, $slug);
        $this->assertEquals($item->district_id, $district->id);

        return $item;
    }

    private function createOutlets($params, $manager, $location)
    {
        list($name, $slug) = $this->generateNameAndSlug($params);

        $item = $this->createOutletsTrait($params, $manager, $location);

        $this->assertEquals($item->name, $name);
        $this->assertEquals($item->slug, $slug);
        $this->assertEquals($item->manager_id, $manager->id);
        $this->assertEquals($item->location_id, $location->id);

        return $item;
    }

    private function createManagers($params)
    {
        list($name, $slug) = $this->generateNameAndSlug($params);

        $item = $this->createManagersTrait($params);

        $this->assertEquals(ucwords($item->name), ucwords($name));
        $this->assertEquals($item->slug, $slug);

        return $item;
    }

    private function createSupervisors($params, $supervisorType, $manager, $outlet)
    {
        $params['name'] = $params['title'] . ' - ' . $outlet->name;

        list($name, $slug) = $this->generateNameAndSlug($params);

        $item = $this->createSupervisorsTrait(
            $params, $supervisorType, $manager, $outlet
        );

        $this->assertEquals($item->name, $name);
        $this->assertEquals($item->slug, $slug);
        $this->assertEquals($item->outlet_id, $outlet->id);
        $this->assertEquals($item->manager_id, $manager->id);

        $manager->supervisor()->attach($item, ['outlet_id' => $outlet->id]);

        return $item;
    }

    private function getDutyType($slug)
    {
        $kitchenArr = ['leader-kitchen', 'head-production'];
        // $outletArr = ['outlet-supervisor', 'central-kitchen-supervisor'];

        $dutyTypeArr = ['production', 'serve'];

        return in_array($slug, $kitchenArr) ? $dutyTypeArr[0] : $dutyTypeArr[1];
    }

    private function createSupervisorTypes($params)
    {
        $params['name'] = $params['title'];

        list($name, $slug) = $this->generateNameAndSlug($params);

        $item = $this->createSupervisorTypesTrait($params);

        $this->assertEquals($item->name, $name);
        $this->assertEquals($item->slug, $slug);

        return $item;
    }

    private function createStaffTypes($params, $supervisor)
    {
        list ($name, $slug) = $this->generateNameAndSlug($params, 'title');

        $item = $this->createStaffTypesTrait($params, $supervisor);

        $this->assertEquals($item->name, $name);
        $this->assertEquals($item->slug, $slug);

        return $item;
    }

    private function createStaffs($params)
    {
        list ($item, $staffType, $supervisor, $supervisorType, $manager, $outlet) = $params;

        list ($name, $slug) = $this->generateNameAndSlug($item);

        $item = $this->createStaffsTrait($params);

        $this->assertEquals($item->name, $name);
        $this->assertEquals($item->slug, $slug);
        $this->assertEquals($item->outlet_id, $outlet->id);
        $this->assertEquals($item->manager_id, $manager->id);
        $this->assertEquals($item->supervisor_id, $supervisor->id);
        $this->assertEquals($item->staff_type_id, $staffType->id);

        return $item;
    }

    private function afterCreate($model, $target, $relation) : void
    {
        $items = $target->{$relation};

        foreach ( $items as $item )
        {
            $this->assertInstanceOf($model, $item);
        }
    }

    private function beforeCreateBrands($items, $lastKey) : void
    {
        foreach ($items as $item)
        {
            $brand = $this->createBrands($item);

            $key = 'province';

            if ($lastKey !== $key && isset($item[$key])) {
                $provinces = $item[$key];

                $this->beforeCreateProvinces($provinces, $brand, $lastKey);
                $this->afterCreate(Province::class, $brand, $key);
            }
        }
    }

    private function beforeCreateProvinces($items, $brand, $lastKey) : void
    {
        foreach($items as $item)
        {
            $province = $this->createProvinces($item, $brand);

            $key = 'regency';

            if ($lastKey !== $key && isset($item[$key])) {
                $regencies = $item[$key];

                $this->beforeCreateRegencies($regencies, $province, $lastKey);
                $this->afterCreate(Regency::class, $province, $key);
            }
        }
    }

    private function beforeCreateRegencies($items, $province, $lastKey) : void
    {
        foreach ($items as $item)
        {
            $regency = $this->createRegencies($item, $province);

            $key = 'district';

            if ($lastKey !== $key && isset($item[$key])) {
                $districts = $item[$key];

                $this->beforeCreateDistrict($districts, $regency, $lastKey);
                $this->afterCreate(District::class, $regency, $key);
            }
        }
    }

    private function beforeCreateDistrict($items, $regency, $lastKey) : void
    {
        foreach ($items as $item)
        {
            $district = $this->createDistricts($item, $regency);

            $key = 'location';

            if ($lastKey !== $key && isset($item[$key])) {
                $locations = $item[$key];

                $this->beforeCreateLocation($locations, $district, $lastKey);
                $this->afterCreate(Location::class, $district, $key);
            }
        }
    }

    private function beforeCreateLocation($items, $district, $lastKey) : void
    {
        foreach ($items as $item)
        {
            $location = $this->createLocations($item, $district);

            $key = 'outlet';

            if ($lastKey !== $key && isset($item[$key])) {
                $outlets = $item[$key];

                $this->beforeCreateOutlet($outlets, $location);
                $this->afterCreate(Outlet::class, $location, $key);
            }
        }
    }

    private function beforeCreateOutlet($items, $location) : void
    {
        foreach ($items as $item)
        {
            $managerData = $item['manager'];

            $manager = $this->createManagers($managerData);
            $outlet = $this->createOutlets($item, $manager, $location);

            if (isset($managerData['supervisor'])) {
                $supervisorData = $managerData['supervisor'];

                $this->beforeCreateSupervisor($supervisorData, $manager, $outlet);
            }
        }
    }

    private function beforeCreateSupervisor($items, $manager, $outlet) : void
    {
        $itemsData = $items['level'];

        foreach ($itemsData as $item)
        {
            $supervisorType = $this->beforeCreateSupervisorType($item);
            $supervisor = $this->createSupervisors(
                $item, $supervisorType, $manager, $outlet
            );

            $params = [
                $item, $supervisor, $supervisorType, $manager, $outlet
            ];

            $this->beforeCreateStaffTypes($params);
        }
    }

    private function beforeCreateSupervisorType($item)
    {
        return $this->createSupervisorTypes($item);
    }

    private function beforeCreateStaffTypes($params) : void
    {
        list ($items, $supervisor, $supervisorType, $manager, $outlet) = $params;

        $staffs = [];
        $level = $items['title'];

        $itemsData = $items['types'];

        foreach ($itemsData as $item)
        {
            $staffType = $this->createStaffTypes($item, $supervisor);

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

    private function beforeCreateStaffs($params, $staffs = [])
    {
        list ($items, $staffType, $supervisor, $supervisorType, $manager, $outlet) = $params;

        $itemsData = $items['staff'];

        foreach($itemsData as $item)
        {
            $newParams = [
                $item, $staffType, $supervisor, $supervisorType, $manager, $outlet
            ];

            $staff = $this->createStaffs($newParams);

            $supervisor->staff_id = $staff->id;
            $supervisor->save();

            $supervisor->multiPivotType()->attach($staffType, ['staff_id' => $staff->id]);

            $staffs[] = $staff;
        }

        return $staffs;
    }
}
