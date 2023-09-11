<?php
namespace App\Repositories;

use App\Models\SupervisorType;

class SupervisorTypeRepository extends BaseRepository
{
    protected $supervisorType;

    public function __construct(SupervisorType $supervisorType)
    {
        $this->supervisorType = $supervisorType;
    }

    public function getFirstItemByQuery($query, $param)
    {
        return $this->supervisorType->where($query, $param)->first();
    }

    public function saveSeeder($parameters)
    {
        list($param) = $parameters;

        $name = $param;
        $slug = $this->processTitleSlug($name);

        $query = ['slug' => $slug];
        $params = [
            'name' => $name,
            'slug' => $slug
        ];

        return $this->supervisorType->firstOrCreate($query, $params);
    }
}