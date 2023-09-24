<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Traits\MasterDatatraits;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Master;
use App\Traits\HelpersTrait;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;
    use MasterDatatraits;
    use HelpersTrait;

    private $listArr;

    public function setup() : void
    {
        parent::setUp();

        $this->listArr = $this->initMasterData();
    }

    public function test_master_data_creation() : void
    {
        $master = Master::first();

        $currentItem = $this->listArr[$master->product_id];

        $this->assertEquals($currentItem['Product ID'], $master->product_id);
        $this->assertEquals($currentItem['Category'], $master->category);
    }

    public function test_master_data_creation_with_any_random_id() : void
    {
        $master = Master::inRandomOrder()->first();

        $currentItem = $this->listArr[$master->product_id];

        $this->assertEquals($currentItem['Product ID'], $master->product_id);
        $this->assertEquals($currentItem['Category'], $master->category);
    }

    public function test_master_data_units_is_sorted_by_descending() : void
    {
        $master = Master::where('product_id', 1046)->first();

        $jsonFile = json_decode($master['units'], true);
        $units = $this->sortItems($jsonFile);

        $totalUnits = count($units);
        $unitKeys = array_keys($units);

        if (!($totalUnits > 0)) return;

        for ($x = 0; $x < $totalUnits; $x++) {
            if ($x > 0) {
                $now = $units[$unitKeys[$x]]['value'];
                $prev = $units[$unitKeys[$x - 1]]['value'];

                $isGreater = $prev >= $now;

                $this->assertTrue($isGreater);
            }
        }
    }

    public function test_master_data_in_approved_categories() : void
    {
        $master = Master::inRandomOrder()->first();

        $allowedList = ['Non Inventory', 'Inventory'];
        $inArray = in_array($master->category_type, $allowedList);

        $this->assertTrue($inArray);
    }

    public function test_master_data_not_in_approved_categories() : void
    {
        $master = Master::inRandomOrder()->first();
        $master->category_type = 'Something Else';

        $allowedList = ['Non Inventory', 'Inventory'];

        $inArray = in_array($master->category_type, $allowedList);

        $this->assertFalse($inArray);
    }
}
