<?php
namespace App\Repositories;

use App\Models\Brand;

class BrandRepository extends BaseRepository
{
    protected $brand;

    public function __construct(Brand $brand)
    {
        $this->brand = $brand;
    }

    public function saveSeeder($parameters)
    {
        list($brandBase, $admin) = $parameters;

        $brandName  = $brandBase['name'];
        $brandSlug  = $this->processTitleSlug($brandName);

        $brand = $this->brand->create([
            'name'     => $brandName,
            'slug'     => $brandSlug,
            'admin_id' => $admin->id
        ]);
        
        return $brand;
    }
}