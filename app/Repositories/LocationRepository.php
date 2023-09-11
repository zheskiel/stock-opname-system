<?php
namespace App\Repositories;

use App\Models\Location;

class LocationRepository extends BaseRepository
{
    protected $location;

    public function __construct(Location $location)
    {
        $this->location = $location;
    }

    public function saveSeeder($parameters)
    {
        list($locItem, $district) = $parameters;

        $locationName  = $locItem['name'];
        $locationAlias = $locItem['alias'];
        $locationSlug  = $this->processTitleSlug($locationName);

        $location = $this->location->create([
            'name'  => $locationName,
            'alias' => $locationAlias,
            'slug'  => $locationSlug,
            'district_id' => $district->id
        ]);
        
        return $location;
    }
}