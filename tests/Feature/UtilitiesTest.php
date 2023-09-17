<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Traits\HelpersTrait;
use Tests\Traits\HierarchyDataTraits;

use App\Models\Manager;
use App\Models\Outlet;
use App\Models\Staff;
use App\Models\Supervisor;

class UtilitiesTest extends TestCase
{
    use RefreshDatabase;
    use HierarchyDataTraits;
    use HelpersTrait;

    public function setUp() :void
    {
        parent::setUp();

        $this->initCreation();
    }

    public function test_if_each_level_only_have_one_supervisor()
    {
        $supervisors = Supervisor::with(['staffs'])->get();

        foreach ($supervisors as $supervisor)
        {
            $actual = 0;
            $expected = 1;

            $staffs = $supervisor->staffs;

            foreach ($staffs as $staff)
            {
                if ($staff->is_supervisor == 1) {
                    $actual += 1;
                }
            }

            $this->assertSame($expected, $actual);
        }
    }
}