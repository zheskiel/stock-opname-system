<?php
namespace App\Services;

use App\Repositories\MasterDataRepository;

class MasterDataService
{
    protected $masterDataRepository;

    public function __construct(MasterDataRepository $masterDataRepository)
    {
        $this->masterDataRepository = $masterDataRepository;
    }

    public function createData($data)
    {
        return $this->masterDataRepository->save($data);
    }
}