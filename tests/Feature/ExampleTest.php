<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Brand;
use App\Models\District;
use App\Models\Location;
use App\Models\Manager;
use App\Models\Outlet;
use App\Models\Province;
use App\Models\Regency;
use App\Traits\HelpersTrait;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    use HelpersTrait;

    private $structure;
    private $svParams;


    public function loadFiles()
    {
        return include(__DIR__ . '/../../database/seeds/StructureData.php');
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

    private function createSupervisors($item, $manager, $outlet)
    {
        $name = $item['title'] . ' - ' . $outlet->name;
        $slug = $this->processTitleSlug($name);

        $attributes = ['slug' => $slug];
        $newAttributes = array_merge([
            'name' => $name,
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

        return $item;
    }

    /**
     * @dataProvider hierarchyProvider
     */
    public function testCreation($lastKey)
    {
        list($svParams, $structure) = $this->loadFiles();

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
        foreach ($items as $item) {
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
            $this->createSupervisors($item, $manager, $outlet);
        }
    }
}
