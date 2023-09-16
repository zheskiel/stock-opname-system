<?php
namespace App\Repositories;

use App\Models\Supervisor;

class SupervisorRepository extends BaseRepository
{
    protected $supervisor;

    public function __construct(Supervisor $supervisor)
    {
        $this->supervisor = $supervisor;
    }

    public function create($params)
    {
        return $this->supervisor->create($params);
    }

    public function saveSeeder($params)
    {
        list($supervisorType, $outlet, $manager) = $params;

        $params = [
            'name'               => $supervisorType->name . ' - ' . $outlet->name,
            'slug'               => $supervisorType->slug . '-' . $outlet->slug,
            'supervisor_type_id' => $supervisorType->id,
            'duty'               => $supervisorType->duty,
            'outlet_id'          => $outlet->id,
            'manager_id'         => $manager->id
        ];

        return $this->create($params);
    }

    public function updateByParams($model, $params)
    {
        list($crStaff) = $params;

        $params = [
            'staff_id' => $crStaff->id
        ];

        return $model->update($params);
    }
}