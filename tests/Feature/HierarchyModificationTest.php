<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Traits\HierarchyDataTraits;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Traits\HelpersTrait;
use App\Models\ {
    Manager,
    Staff,
    StaffType,
    Supervisor
};

class HierarchyModificationTest extends TestCase
{
    use HierarchyDataTraits;
    use RefreshDatabase;
    use HelpersTrait;

    public function setUp() :void
    {
        parent::setUp();

        $this->initCreation();
    }

    private function createStaff($manager, $supervisor, $randType, $xLimit)
    {
        for ($x = 0; $x < $xLimit; $x++) {
            $name = 'staff '. rand(8500, 95000);
            $slug = $this->processTitleSlug($name);

            $attributes = ['slug' => $slug];
            $newAttributes = array_merge([
                'name'          => $name,
                'slug'          => $slug,
                'sv_type_label' => $supervisor->supervisor_pic->sv_type_label,
                'outlet_id'     => (int) $supervisor->outlet_id,
                'manager_id'    => $manager->id,
                'staff_type_id' => $randType->id,
                'supervisor_id' => $supervisor->id,
                'is_supervisor' => 0,
                'brand_id'      => 1
            ], $attributes);

            $staff = Staff::firstOrCreate(
                $attributes,
                factory(Staff::class)->raw($newAttributes)
            );

            $supervisor->staffs()->attach($staff, ['staff_type_id' => $randType->id]);
        }
    }

    private function createStaffTypes($supervisor, $xLimit)
    {
        for ($x = 0; $x < $xLimit; $x++) {
            $name = 'staff_type_ '. rand(8500, 95000);
            $slug = $this->processTitleSlug($name);

            $attributes = ['slug' => $slug];
            $newAttributes = array_merge([
                'name'          => $name,
                'slug'          => $slug,
                'supervisor_id' => $supervisor->id
            ], $attributes);

            StaffType::firstOrCreate(
                $attributes,
                factory(StaffType::class)->raw($newAttributes)
            );
        }
    }

    private function removeStaffType($types)
    {
        foreach ($types as $type)
        {
            $type->delete();
        }
    }

    public function test_if_create_staff_will_increase_staff_number()
    {
        $xLimit = 10;
        $oldStaffs = Staff::count();

        $manager = Manager::first();

        $supervisor = $manager->supervisor[0];
        $totalTypes = count($supervisor->type);
        $randType   = $supervisor->type[rand(0, $totalTypes - 1)];

        $this->createStaff($manager, $supervisor, $randType, $xLimit);

        $newStaffs = Staff::count();

        $this->assertSame($oldStaffs + $xLimit, $newStaffs);
    }

    public function test_add_staff_to_certain_supervisor_also_increase_supervisor_staffs_count()
    {
        $xLimit = 100;

        $supervisor = Supervisor::first();

        $manager = $supervisor->manager;
        $staffs  = $supervisor->staffs()->count();

        $totalTypes = count($supervisor->type);
        $randType   = $supervisor->type[rand(0, $totalTypes - 1)];

        $this->createStaff($manager, $supervisor, $randType, $xLimit);

        $supervisor2 = Supervisor::first();
        $staffs2  = $supervisor2->staffs()->count();

        $this->assertSame($staffs2, $staffs + $xLimit);
    }

    public function test_add_staff_type_according_to_supervisor()
    {
        $xLimit = 10;

        $supervisor = Supervisor::withCount(['type'])->first();

        $firstCount = $supervisor->type_count;

        $this->createStaffTypes($supervisor, $xLimit);

        $supervisor2 = Supervisor::withCount(['type'])->first();
        $secondCount = $supervisor2->type_count;

        $this->assertSame($firstCount + $xLimit, $secondCount);
    }

    public function test_remove_staff_type_according_to_supervisor()
    {
        $xLimit = 1;
        $supervisor = Supervisor::withCount(['type'])->first();

        $staffTypes = StaffType::where('supervisor_id', $supervisor->id)
            ->limit($xLimit)
            ->get();

        $firstCount = $supervisor->type_count;

        $this->removeStaffType($staffTypes);

        $supervisor2 = Supervisor::withCount(['type'])->first();
        $secondCount = $supervisor2->type_count;

        $expected = $firstCount - $xLimit;
        $actual = $secondCount;

        $this->assertSame($expected, $actual);
    }
}
