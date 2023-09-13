<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Brand;
use App\Models\District;
use App\Models\Location;
use App\Models\Outlet;
use App\Models\Province;
use App\Models\Regency;
use App\Traits\HelpersTrait;

class ExampleTest extends TestCase
{
    // use RefreshDatabase;
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

        $brand = Brand::firstOrCreate(
            $attributes,
            factory(Brand::class)->raw($newAttributes)
        );
        
        $this->assertEquals($brand->name, $name);
        $this->assertEquals($brand->slug, $slug);

        return $brand;
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

        $province = Province::firstOrCreate(
            $attributes,
            factory(Province::class)->raw($newAttributes)
        );

        $this->assertEquals($province->name, $pName);
        $this->assertEquals($province->slug, $pSlug);
        $this->assertEquals($province->brand_id, $brand->id);

        return $province;
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

        $regency = Regency::firstOrCreate(
            $attributes,
            factory(Regency::class)->raw($newAttributes)
        );

        $this->assertEquals($regency->name, $rName);
        $this->assertEquals($regency->slug, $rSlug);
        $this->assertEquals($regency->province_id, $province->id);

        return $regency;
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

        $district = District::firstOrCreate(
            $attributes,
            factory(District::class)->raw($newAttributes)
        );

        $this->assertEquals($district->name, $dName);
        $this->assertEquals($district->slug, $dSlug);
        $this->assertEquals($district->regency_id, $regency->id);

        return $district;
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

        $location = Location::firstOrCreate(
            $attributes,
            factory(Location::class)->raw($newAttributes)
        );

        $this->assertEquals($location->name, $lName);
        $this->assertEquals($location->slug, $lSlug);
        $this->assertEquals($location->district_id, $district->id);

        return $location;
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

        $outlet = Outlet::firstOrCreate(
            $attributes,
            factory(Outlet::class)->raw($newAttributes)
        );

        $this->assertEquals($outlet->name, $oName);
        $this->assertEquals($outlet->slug, $oSlug);
        $this->assertEquals($outlet->location_id, $location->id);

        return $location;
    }

    /**
     * @return void
     */
    public function testBrandCreation()
    {
        list($svParams, $structure) = $this->loadFiles();

        $items = $structure['brand'];

        foreach ($items as $item) {
            $this->createBrands($item);
        }
    }

    /**
     * @return void
     */
    public function testProvinceCreation()
    {
        list($svParams, $structure) = $this->loadFiles();

        $items = $structure['brand'];

        foreach ($items as $item) {
            $brand = $this->createBrands($item);

            $provinces = $item['province'];

            foreach($provinces as $pItem)
            {
                $this->createProvinces($pItem, $brand);
            }
        }
    }

    public function testRegencyCreation()
    {
        list($svParams, $structure) = $this->loadFiles();

        $items = $structure['brand'];

        foreach ($items as $item) {
            $brand = $this->createBrands($item);

            $provinces = $item['province'];

            foreach($provinces as $pItem)
            {
                $province = $this->createProvinces($pItem, $brand);

                $regencies = $pItem['regency'];

                foreach ($regencies as $rItem)
                {
                    $this->createRegencies($rItem, $province);
                }
            }
        }
    }

    public function testDistrictCreation()
    {
        list($svParams, $structure) = $this->loadFiles();

        $items = $structure['brand'];

        foreach ($items as $item) {
            $brand = $this->createBrands($item);

            $provinces = $item['province'];

            foreach($provinces as $pItem)
            {
                $province = $this->createProvinces($pItem, $brand);

                $regencies = $pItem['regency'];

                foreach ($regencies as $rItem)
                {
                    $regency = $this->createRegencies($rItem, $province);

                    $districts = $rItem['district'];

                    foreach ($districts as $dItem)
                    {
                        $this->createDistricts($dItem, $regency);
                    }
                }
            }
        }
    }

    public function testLocationCreation()
    {
        list($svParams, $structure) = $this->loadFiles();

        $items = $structure['brand'];

        foreach ($items as $item) {
            $brand = $this->createBrands($item);

            if (isset($item['province'])) {
                $provinces = $item['province'];

                foreach($provinces as $pItem)
                {
                    $province = $this->createProvinces($pItem, $brand);

                    if (isset($pItem['regency'])) {
                        $regencies = $pItem['regency'];

                        foreach ($regencies as $rItem)
                        {
                            $regency = $this->createRegencies($rItem, $province);

                            if (isset($rItem['district'])) {
                                $districts = $rItem['district'];

                                foreach ($districts as $dItem)
                                {
                                    $district = $this->createDistricts($dItem, $regency);

                                    if (isset($dItem['location'])) {
                                        $locations = $dItem['location'];

                                        foreach ($locations as $lItem)
                                        {
                                            $this->createLocations($lItem, $district);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function testOutletCreation()
    {
        list($svParams, $structure) = $this->loadFiles();

        $items = $structure['brand'];

        foreach ($items as $item) {
            $brand = $this->createBrands($item);

            if (isset($item['province'])) {
                $provinces = $item['province'];

                foreach($provinces as $pItem)
                {
                    $province = $this->createProvinces($pItem, $brand);

                    if (isset($pItem['regency'])) {
                        $regencies = $pItem['regency'];

                        foreach ($regencies as $rItem)
                        {
                            $regency = $this->createRegencies($rItem, $province);

                            if (isset($rItem['district'])) {
                                $districts = $rItem['district'];

                                foreach ($districts as $dItem)
                                {
                                    $district = $this->createDistricts($dItem, $regency);

                                    if (isset($dItem['location'])) {
                                        $locations = $dItem['location'];

                                        foreach ($locations as $lItem)
                                        {
                                            $location = $this->createLocations($lItem, $district);

                                            if (isset($lItem['outlet'])) {
                                                $outlets = $lItem['outlet'];

                                                foreach ($outlets as $oItem)
                                                {
                                                    $this->createOutlets($oItem, $location);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
