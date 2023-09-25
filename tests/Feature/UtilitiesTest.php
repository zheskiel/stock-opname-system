<?php
namespace Tests\Feature;

use App\Models\Manager;
use App\Models\Outlet;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Traits\HelpersTrait;
use Tests\Traits\HierarchyDataTraits;

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

    public function test_when_manager_load_supervisor_need_to_be_in_certain_outlet()
    {
        $outlet = Outlet::first();
        $items = Manager::with($this->loadSupervisorWithSupervisorPicAndTypeByOutlet($outlet))->first();

        $supervisors = $items->supervisor;

        if (count($supervisors) > 0)
        {
            foreach($supervisors as $supervisor)
            {
                $this->assertSame((int) $outlet->id, (int) $supervisor->outlet_id);
            }
        }
    }
}