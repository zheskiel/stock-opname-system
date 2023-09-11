<?php
namespace App\Services;

use App\Repositories\StaffRepository;

class StaffService
{
    protected $staffRepository;

    public function __construct(staffRepository $staffRepository)
    {
        $this->staffRepository = $staffRepository;
    }

    public function searchByFirstAndParam($search, $param)
    {
        return $this->staffRepository->searchByFirstAndParam($search, $param);
    }

    public function createSeederData($data)
    {
        return $this->staffRepository->saveSeeder($data);
    }

    public function updateByParams($model, $params)
    {
        return $this->staffRepository->updateByParams($model, $params);
    }
}