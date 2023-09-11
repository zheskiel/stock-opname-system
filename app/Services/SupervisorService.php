<?php
namespace App\Services;

use App\Repositories\SupervisorRepository;

class SupervisorService extends BaseService
{
    protected $supervisorRepository;

    public function __construct(SupervisorRepository $supervisorRepository)
    {
        $this->supervisorRepository = $supervisorRepository;
    }

    public function createSeederData($data)
    {
        return $this->supervisorRepository->saveSeeder($data);
    }

    public function updateByParams($params)
    {
        return $this->supervisorRepository->updateByParams($params);
    }
}