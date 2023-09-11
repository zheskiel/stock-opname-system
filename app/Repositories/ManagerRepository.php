<?php
namespace App\Repositories;

use App\Models\Manager;

class ManagerRepository extends BaseRepository
{
    protected $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    public function saveSeeder($parameters)
    {
        list($outItem, $outlet) = $parameters;

        $manager = $outItem['manager'];
        $managerName = $manager['name'];

        $slug = $this->processTitleSlug($managerName);

        $query = ['slug' => $slug];
        $params = [
            'name'        => $managerName,
            'slug'        => $slug,
            'email'       => $slug . "@gmail.com",
            'password'    => bcrypt('test123'),
            'outlet_id'   => $outlet->id
        ];

        return $this->manager->firstOrCreate($query, $params);
    }
}