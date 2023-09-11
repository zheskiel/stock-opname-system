<?php
namespace App\Services;

use App\Repositories\ManagerRepository;

class ManagerService extends BaseService
{
    protected $managerRepository;

    public function __construct(ManagerRepository $managerRepository)
    {
        $this->managerRepository = $managerRepository;
    }

    public function createSeederData($data)
    {
        return $this->managerRepository->saveSeeder($data);
    }
}