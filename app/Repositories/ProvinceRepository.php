<?php
namespace App\Repositories;

use App\Models\Province;

class ProvinceRepository extends BaseRepository
{
    protected $province;

    public function __construct(Province $province)
    {
        $this->province = $province;
    }

    public function saveSeeder($parameters)
    {
        list($provItem, $brand) = $parameters;

        $provinceName = $provItem['name'];
        $provinceSlug  = $this->processTitleSlug($provinceName);

        $province = $this->province->create([
            'name'     => $provinceName,
            'slug'     => $provinceSlug,
            'brand_id' => $brand->id
        ]);
        
        return $province;
    }
}