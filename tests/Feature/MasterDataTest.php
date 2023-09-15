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

    public function testMasterDataCreation() : void
    {
        $master = Master::first();

        $currentItem = $this->listArr[$master->product_id];

        $this->assertEquals($currentItem['Product ID'], $master->product_id);
        $this->assertEquals($currentItem['Category'], $master->category);
    }

    public function testMasterDataCreationWithAnyRandomId() : void
    {
        $randomId = rand(2 , 10);
        $master = Master::where('id', $randomId)->first();

        $currentItem = $this->listArr[$master->product_id];

        $this->assertEquals($currentItem['Product ID'], $master->product_id);
        $this->assertEquals($currentItem['Category'], $master->category);
    }

    public function testMasterDataUnitsIsSortedByDescending() : void
    {
        $randomId = rand(2 , 10);
        $master = Master::where('id', $randomId)->first();
        $units = json_decode($master['units'], true);

        $totalUnits = count($units);
        $unitKeys = array_keys($units);

        if (!($totalUnits > 0)) return;

        for ($x=0; $x < $totalUnits; $x++) {
            if ($x > 0) {
                $now = $units[$unitKeys[$x]]['value'];
                $prev = $units[$unitKeys[$x - 1]]['value'];

                $isGreater = $prev >= $now;

                $this->assertTrue($isGreater);
            }
        }
    }

    public function testMasterdataInApprovedCategories() : void
    {
        $randomId = rand(2 , 10);
        $master = Master::where('id', $randomId)->first();

        $allowedList = ['Non Inventory', 'Inventory'];
        $inArray = in_array($master->category_type, $allowedList);

        $this->assertTrue($inArray);
    }

    public function testMasterdataNotInApprovedCategories() : void
    {
        $randomId = rand(2 , 10);
        $master = Master::where('id', $randomId)->first();
        $master->category_type = 'Something Else ' . $randomId;

        $allowedList = ['Non Inventory', 'Inventory'];

        $inArray = in_array($master->category_type, $allowedList);

        $this->assertFalse($inArray);
    }
}
