<?php
namespace App\Services;

use App\Repositories\DistrictRepository;

class DistrictService extends BaseService
{
    protected $districtRepository;

    public function __construct(DistrictRepository $districtRepository)
    {
        $this->districtRepository = $districtRepository;
    }

    public function createSeederData($data)
    {
        return $this->districtRepository->saveSeeder($data);
    }
}