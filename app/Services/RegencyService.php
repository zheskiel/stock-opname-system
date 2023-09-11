<?php
namespace App\Services;

use App\Repositories\RegencyRepository;

class RegencyService extends BaseService
{
    protected $regencyRepository;

    public function __construct(RegencyRepository $regencyRepository)
    {
        $this->regencyRepository = $regencyRepository;
    }

    public function createSeederData($data)
    {
        return $this->regencyRepository->saveSeeder($data);
    }
}