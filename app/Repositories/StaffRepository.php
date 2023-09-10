<?php
namespace App\Repositories;

use App\Models\Staff;

class StaffRepository extends BaseRepository
{
    protected $staff;

    public function __construct(Staff $staff)
    {
        $this->staff = $staff;
    }

    public function save($params)
    {
        list($staff, $staffType, $outlet, $manager, $supervisor) = $params;

        $staffName = $staff['name'];
        $staffSlug = $this->processTitleSlug($staffName);

        $data = $this->staff
            ->firstOrCreate(
                ['slug' => $staffSlug],
                [
                    'name'          => $staffName,
                    'slug'          => $staffSlug,
                    'email'         => $staffSlug . "@gmail.com",
                    'password'      => bcrypt('test123'),
                    'outlet_id'     => $outlet->id,
                    'manager_id'    => $manager->id,
                    'supervisor_id' => $supervisor->id,
                    'staff_type_id' => $staffType->id,
                ]
            );

        return $data;
    }
}