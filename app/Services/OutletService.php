<?php
namespace App\Services;

use App\Repositories\OutletRepository;

class OutletService
{
    protected $outletRepository;

    public function __construct(OutletRepository $outletRepository)
    {
        $this->outletRepository = $outletRepository;
    }

    public function createSeederData($data)
    {
        return $this->outletRepository->saveSeeder($data);
    }

    public function updateByParams($params)
    {
        return $this->outletRepository->updateByParams($params);
    }
}