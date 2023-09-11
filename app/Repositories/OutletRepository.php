<?php
namespace App\Repositories;

use App\Models\Outlet;

class OutletRepository extends BaseRepository
{
    protected $outlet;

    public function __construct(Outlet $outlet)
    {
        $this->outlet = $outlet;
    }

    public function saveSeeder($parameters)
    {
        list($outItem, $location) = $parameters;

        $outletName = $outItem['name'];
        $outletSlug = $this->processTitleSlug($outletName);

        $outlet = $this->outlet->create([
                'name' => $outletName,
                'slug' => $outletSlug,
                'location_id' => $location->id
            ]);
        
        return $outlet;
    }

    public function updateByParams($params)
    {
        return $this->update($params);
    }

    public function update($params)
    {
        return $this->outlet->update($params);
    }
}