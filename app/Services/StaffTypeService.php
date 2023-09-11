<?php
namespace App\Services;

use App\Repositories\StaffTypeRepository;

class StaffTypeService extends BaseService
{
    protected $staffTypeRepository;

    public function __construct(staffTypeRepository $staffTypeRepository)
    {
        $this->staffTypeRepository = $staffTypeRepository;
    }

    public function createSeederData($data)
    {
        return $this->staffTypeRepository
            ->saveSeeder($data);
    }
}