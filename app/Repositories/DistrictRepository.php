<?php
namespace App\Repositories;

use App\Models\District;

class DistrictRepository extends BaseRepository
{
    protected $district;

    public function __construct(District $district)
    {
        $this->district = $district;
    }

    public function saveSeeder($parameters)
    {
        list($disItem, $regency) = $parameters;

        $districtName  = $disItem['name'];
        $districtSlug  = $this->processTitleSlug($districtName);

        $district = $this->district->create([
            'name'       => $districtName,
            'slug'       => $districtSlug,
            'regency_id' => $regency->id
        ]);
        
        return $district;
    }
}