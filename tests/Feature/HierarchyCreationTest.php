<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Traits\HelpersTrait;
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

class HierarchyCreationTest extends TestCase
{
    // use RefreshDatabase;
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

    private function createBrands($item)
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
        
        $this->assertEquals($item->name, $name);
        $this->assertEquals($item->slug, $slug);

        return $item;
    }

    private function createProvinces($item, $brand)
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

        $this->assertEquals($item->name, $name);
        $this->assertEquals($item->slug, $slug);
        $this->assertEquals($item->brand_id, $brand->id);

        return $item;
    }

    private function createRegencies($item, $province)
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

        $this->assertEquals($item->name, $name);
        $this->assertEquals($item->slug, $slug);
        $this->assertEquals($item->province_id, $province->id);

        return $item;
    }

    private function createDistricts($item, $regency)
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

        $this->assertEquals($item->name, $name);
        $this->assertEquals($item->slug, $slug);
        $this->assertEquals($item->regency_id, $regency->id);

        return $item;
    }

    private function createLocations($item, $district)
    {
        $name = $item['name'];
        $slug = $this->processTitleSlug($name);

        $alias = $item['alias'];

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

        $this->assertEquals($item->name, $name);
        $this->assertEquals($item->slug, $slug);
        $this->assertEquals($item->district_id, $district->id);

        return $item;
    }

    private function createOutlets($item, $manager, $location)
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

        $this->assertEquals($item->name, $name);
        $this->assertEquals($item->slug, $slug);
        $this->assertEquals($item->manager_id, $manager->id);
        $this->assertEquals($item->location_id, $location->id);

        return $item;
    }

    private function createManagers($item)
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

        $this->assertEquals(ucwords($item->name), ucwords($name));
        $this->assertEquals($item->slug, $slug);

        return $item;
    }

    private function createSupervisors($item, $supervisorType, $manager, $outlet)
    {
        $name = $item['title'] . ' - ' . $outlet->name;
        $slug = $this->processTitleSlug($name);

        $attributes = ['slug' => $slug];
        $newAttributes = array_merge([
            'name' => $name,
            'supervisor_type_id' => $supervisorType->id,
            'manager_id'  => $manager->id,
            'outlet_id'   => $outlet->id
        ], $attributes);

        $item = Supervisor::firstOrCreate(
            $attributes,
            factory(Supervisor::class)->raw($newAttributes)
        );

        $this->assertEquals($item->name, $name);
        $this->assertEquals($item->slug, $slug);
        $this->assertEquals($item->outlet_id, $outlet->id);
        $this->assertEquals($item->manager_id, $manager->id);

        $manager->supervisor()->attach($item, ['outlet_id' => $outlet->id]);

        return $item;
    }

    private function createSupervisorTypes($item)
    {
        $name = $item['title'];
        $slug = $this->processTitleSlug($name);

        $attributes = ['slug' => $slug];
        $newAttributes = array_merge([
            'name' => $name,
        ], $attributes);

        $item = SupervisorType::firstOrCreate(
            $attributes,
            factory(SupervisorType::class)->raw($newAttributes)
        );

        $this->assertEquals($item->name, $name);
        $this->assertEquals($item->slug, $slug);

        return $item;
    }

    private function createStaffTypes($item, $supervisor)
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

        $this->assertEquals($item->name, $name);
        $this->assertEquals($item->slug, $slug);

        return $item;
    }

    private function createStaffs($params)
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

        $this->assertEquals($item->name, $name);
        $this->assertEquals($item->slug, $slug);
        $this->assertEquals($item->outlet_id, $outlet->id);
        $this->assertEquals($item->manager_id, $manager->id);
        $this->assertEquals($item->supervisor_id, $supervisor->id);
        $this->assertEquals($item->staff_type_id, $staffType->id);

        return $item;
    }

    private function afterCreate($model, $target, $relation)
    {
        $items = $target->{$relation};

        foreach ( $items as $item )
        {
            $this->assertInstanceOf($model, $item);
        }
    }

    private function beforeCreateBrands($items, $lastKey)
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

    private function beforeCreateProvinces($items, $brand, $lastKey)
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

    private function beforeCreateRegencies($items, $province, $lastKey)
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

    private function beforeCreateDistrict($items, $regency, $lastKey)
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

    private function beforeCreateLocation($items, $district, $lastKey)
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

    private function beforeCreateOutlet($items, $location)
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

    private function beforeCreateSupervisor($items, $manager, $outlet)
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

    private function beforeCreateStaffTypes($params)
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

        $crStaff = $this->getRandomStaffFromCertainLevel($staffs, $level);

        $crStaff->is_supervisor = true;
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

    private function getRandomStaffFromCertainLevel($staffs, $level)
    {
        $crStaffType = $staffs[$level];

        $totalStaff = count($crStaffType);
        $randStaff = rand(0, $totalStaff - 1);

        return $crStaffType[$randStaff];
    }
}
