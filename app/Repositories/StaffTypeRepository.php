<?php
namespace App\Repositories;

use App\Models\StaffType;

class StaffTypeRepository extends BaseRepository
{
    protected $staffType;

    public function __construct(StaffType $staffType)
    {
        $this->staffType = $staffType;
    }

    public function saveSeeder($params)
    {
        list($type, $supervisor) = $params;

        $typeTitle = $type['title'];
        $typeSlug = $this->processTitleSlug($typeTitle);

        $typeSlug = $typeSlug."_".$supervisor->slug;

        $data = $this->staffType
            ->firstOrCreate(
                ['slug' => $typeSlug],
                [
                    'name' => $typeTitle,
                    'slug'  => $typeSlug,
                    'supervisor_id' => $supervisor->id
                ]
        );

        return $data;
    }
}