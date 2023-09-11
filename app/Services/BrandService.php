<?php
namespace App\Services;

use App\Repositories\brandRepository;

class BrandService extends BaseService
{
    protected $brandRepository;

    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    public function createSeederData($data)
    {
        return $this->brandRepository->saveSeeder($data);
    }
}