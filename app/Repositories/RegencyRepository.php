<?php
namespace App\Repositories;

use App\Models\Regency;

class RegencyRepository extends BaseRepository
{
    protected $regency;

    public function __construct(Regency $regency)
    {
        $this->regency = $regency;
    }

    public function saveSeeder($parameters)
    {
        list($regItem, $province) = $parameters;

        $regencyName = $regItem['name'];
        $regencySlug  = $this->processTitleSlug($regencyName);

        $regency = $this->regency->create([
            'name'        => $regencyName,
            'slug'        => $regencySlug,
            'province_id' => $province->id
        ]);
        
        return $regency;
    }
}