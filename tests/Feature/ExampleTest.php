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

        $brand = factory(Brand::class)->create([
            'name' => $name,
            'slug' => $slug
        ]);
        
        $this->assertEquals($brand->name, $name);
        $this->assertEquals($brand->slug, $slug);

        return $brand;
    }

    private function createProvinces($pItem, $brand)
    {
        $pName = $pItem['name'];

        $pSlug = $this->processTitleSlug($pName);

        $params = [
            'name' => $pName,
            'slug' => $pSlug,
            'brand_id' => $brand->id
        ];

        $province = factory(Province::class)->create($params);

        $this->assertEquals($province->name, $pName);
        $this->assertEquals($province->slug, $pSlug);

        return $province;
    }

    private function createRegencies($rItem, $province)
    {
        $rName = $rItem['name'];
        $rSlug = $this->processTitleSlug($rName);

        $params = [
            'name' => $rName,
            'slug' => $rSlug,
            'province_id' => $province->id
        ];

        $regency = factory(Regency::class)->create($params);

        $this->assertEquals($regency->name, $rName);
        $this->assertEquals($regency->slug, $rSlug);

        return $regency;
    }

    private function createDistricts($dItem, $regency)
    {
        $dName = $dItem['name'];
        $dSlug = $this->processTitleSlug($dName);

        $params = [
            'name' => $dName,
            'slug' => $dSlug,
            'regency_id' => $regency->id
        ];

        $district = factory(District::class)->create($params);

        $this->assertEquals($district->name, $dName);
        $this->assertEquals($district->slug, $dSlug);

        return $district;
    }

    private function createLocations($lItem, $district)
    {
        $lName = $lItem['name'];
        $lSlug = $this->processTitleSlug($lName);
        $lAlias = $lItem['alias'];

        $params = [
            'name' => $lName,
            'slug' => $lSlug,
            'alias' => $lAlias,
            'district_id' => $district->id
        ];

        $location = factory(Location::class)->create($params);

        $this->assertEquals($location->name, $lName);
        $this->assertEquals($location->slug, $lSlug);

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

                                foreach ($districts as $k => $dItem)
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
}
