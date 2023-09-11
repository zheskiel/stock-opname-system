<?php
namespace App\Services;

use App\Repositories\LocationRepository;

class LocationService extends BaseService
{
    protected $locationRepository;

    public function __construct(LocationRepository $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    public function createSeederData($data)
    {
        return $this->locationRepository->saveSeeder($data);
    }
}