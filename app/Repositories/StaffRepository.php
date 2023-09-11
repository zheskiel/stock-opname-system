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

    public function searchByFirstAndParam($search, $param)
    {
        return $this->staff->where($search, $param)->first();
    }

    public function saveSeeder($parameters)
    {
        list($level, $staff, $staffType, $outlet, $manager, $supervisor) = $parameters;

        $staffName = $staff['name'];
        $staffSlug = $this->processTitleSlug($staffName);

        $query = ['slug' => $staffSlug];
        $params = [
            'name'          => $staffName,
            'slug'          => $staffSlug,
            'email'         => $staffSlug . "@gmail.com",
            'password'      => bcrypt('test123'),
            'outlet_id'     => $outlet->id,
            'manager_id'    => $manager->id,
            'supervisor_id' => $supervisor->id,
            'staff_type_id' => $staffType->id,
            'sv_type_label' => $level['title']
        ];

        return $this->firstOrCreate($query, $params);
    }

    public function updateByParams($model, $params)
    {
        return $model->update($params);
    }

    public function firstOrCreate($query, $params)
    {
        return $this->staff->firstOrCreate($query, $params);
    }
}