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
        list($outItem) = $parameters;

        $manager = $outItem['manager'];
        $managerName = $manager['name'];

        $slug = $this->processTitleSlug($managerName);

        $query = ['slug' => $slug];
        $params = [
            'name'        => $managerName,
            'slug'        => $slug,
            'email'       => $slug . "@gmail.com",
            'password'    => bcrypt('test123')
        ];

        return $this->manager->firstOrCreate($query, $params);
    }
}