<?php
namespace App\Services;

use App\Repositories\BrandRepository;

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