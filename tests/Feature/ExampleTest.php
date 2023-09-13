<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Brand;
use App\Models\Province;
use App\Traits\HelpersTrait;

class ExampleTest extends TestCase
{
    use RefreshDatabase, HelpersTrait;

    private $brand;

    /**
     * A basic test example.
     *
     * @dataProvider BrandProvider
     * @return void
     */
    public function testBrandCreation($name)
    {
        $slug = $this->processTitleSlug($name);

        $brand = factory(Brand::class)->create([
            'name' => $name,
            'slug' => $slug
        ]);
        
        $this->assertEquals($brand->name, $name);
        $this->assertEquals($brand->slug, $slug);
    }

    /**
     * 
     * @dataProvider ProvinceProvider
     * @return void
     */
    public function testProvinceCreation($provinceName)
    {
        $provinceSlug = $this->processTitleSlug($provinceName);

        $province = factory(Province::class)->make([
            'name' => $provinceName,
            'slug' => $provinceSlug
        ]);

        $this->assertEquals($province->name, $provinceName);
        $this->assertEquals($province->slug, $provinceSlug);
    }

    public function BrandProvider()
    {
        $arrs = [];

        for ($x = 0; $x < 10; $x++) {
            $arrs[] = [ 'Test ' . $x ];
        }

        return $arrs;
    }

    public function provinceProvider()
    {
        return [
            ['DKI Jakarta'],
            ['Banten'],
            ['Sumatera Utara'],
            ['Sumatera Barat'],
            ['Sumatera Selatan']
        ];
    }
}
