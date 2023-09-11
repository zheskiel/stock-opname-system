<?php
namespace App\Services;

use App\Repositories\SupervisorTypeRepository;

class SupervisorTypeService extends BaseService
{
    protected $supervisorTypeRepository;

    public function __construct(SupervisorTypeRepository $supervisorTypeRepository)
    {
        $this->supervisorTypeRepository = $supervisorTypeRepository;
    }

    public function getFirstItemByQuery($level)
    {
        $levelTitle = $level['title'];
        $levelSlug = $this->processTitleSlug($levelTitle);

        return $this->supervisorTypeRepository->getFirstItemByQuery('slug', $levelSlug);
    }

    public function createSeederData($data)
    {
        return $this->supervisorTypeRepository->saveSeeder($data);
    }
}