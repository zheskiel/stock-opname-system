<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Brand;
use App\Models\District;
use App\Models\Location;
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

    private function createBrands($bItem)
    {
        $name = $bItem['name'];

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

    private function createProvinces($pItem, $brand)
    {
        $pName = $pItem['name'];
        $pSlug = $this->processTitleSlug($pName);

        $attributes = ['slug' => $pSlug];
        $newAttributes = array_merge([
            'name' => $pName,
            'brand_id' => $brand->id
        ], $attributes);

        $item = Province::firstOrCreate(
            $attributes,
            factory(Province::class)->raw($newAttributes)
        );

        $this->assertEquals($item->name, $pName);
        $this->assertEquals($item->slug, $pSlug);
        $this->assertEquals($item->brand_id, $brand->id);

        return $item;
    }

    private function createRegencies($rItem, $province)
    {
        $rName = $rItem['name'];
        $rSlug = $this->processTitleSlug($rName);

        $attributes = ['slug' => $rSlug];
        $newAttributes = array_merge([
            'name' => $rName,
            'province_id' => $province->id
        ], $attributes);

        $item = Regency::firstOrCreate(
            $attributes,
            factory(Regency::class)->raw($newAttributes)
        );

        $this->assertEquals($item->name, $rName);
        $this->assertEquals($item->slug, $rSlug);
        $this->assertEquals($item->province_id, $province->id);

        return $item;
    }

    private function createDistricts($dItem, $regency)
    {
        $dName = $dItem['name'];
        $dSlug = $this->processTitleSlug($dName);

        $attributes = ['slug' => $dSlug];
        $newAttributes = array_merge([
            'name' => $dName,
            'regency_id' => $regency->id
        ], $attributes);

        $item = District::firstOrCreate(
            $attributes,
            factory(District::class)->raw($newAttributes)
        );

        $this->assertEquals($item->name, $dName);
        $this->assertEquals($item->slug, $dSlug);
        $this->assertEquals($item->regency_id, $regency->id);

        return $item;
    }

    private function createLocations($lItem, $district)
    {
        $lName = $lItem['name'];
        $lSlug = $this->processTitleSlug($lName);

        $lAlias = $lItem['alias'];

        $attributes = ['slug' => $lSlug];
        $newAttributes = array_merge([
            'name' => $lName,
            'alias' => $lAlias,
            'district_id' => $district->id
        ], $attributes);

        $item = Location::firstOrCreate(
            $attributes,
            factory(Location::class)->raw($newAttributes)
        );

        $this->assertEquals($item->name, $lName);
        $this->assertEquals($item->slug, $lSlug);
        $this->assertEquals($item->district_id, $district->id);

        return $item;
    }

    private function createOutlets($oItem, $location)
    {
        $oName = $oItem['name'];
        $oSlug = $this->processTitleSlug($oName);

        $attributes = ['slug' => $oSlug];
        $newAttributes = array_merge([
            'name' => $oName,
            'location_id' => $location->id
        ], $attributes);

        $item = Outlet::firstOrCreate(
            $attributes,
            factory(Outlet::class)->raw($newAttributes)
        );

        $this->assertEquals($item->name, $oName);
        $this->assertEquals($item->slug, $oSlug);
        $this->assertEquals($item->location_id, $location->id);

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

    private function beforeCreateBrands($items, $lastKey)
    {
        foreach ($items as $item) {
            $brand = $this->createBrands($item);

            $key = 'province';

            if ($lastKey !== $key && isset($item[$key])) {
                $provinces = $item[$key];

                $this->beforeCreateProvinces($provinces, $brand, $lastKey);
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
            }
        }
    }

    private function beforeCreateOutlet($items, $location)
    {
        foreach ($items as $item)
        {
            $this->createOutlets($item, $location);
        }
    }
}
