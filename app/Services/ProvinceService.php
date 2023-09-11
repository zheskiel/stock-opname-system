<?php
namespace App\Services;

use App\Repositories\ProvinceRepository;

class ProvinceService extends BaseService
{
    protected $provinceRepository;

    public function __construct(ProvinceRepository $provinceRepository)
    {
        $this->provinceRepository = $provinceRepository;
    }

    public function createSeederData($data)
    {
        return $this->provinceRepository->saveSeeder($data);
    }
}