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

    public function createData($data)
    {
        return $this->staffRepository
            ->save($data);
    }
}