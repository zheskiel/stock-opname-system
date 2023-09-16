<?php
namespace App\Services;

use App\Repositories\SupervisorTypeRepository;

class SupervisorTypeService extends BaseService
{
    protected $supervisorTypeRepository;

    public function __construct(SupervisorTypeRepository $supervisorTypeRepository)
    {
        $this->supervisorTypeRepository = $supervisorTypeRepository;
    }

    public function getFirstItemByQuery($level)
    {
        $levelTitle = $level['title'];
        $levelSlug = $this->processTitleSlug($levelTitle);

        return $this->supervisorTypeRepository->getFirstItemByQuery('slug', $levelSlug);
    }

    private function getDutyType($slug)
    {
        $kitchenArr = ['leader-kitchen', 'head-production'];
        // $outletArr = ['outlet-supervisor', 'central-kitchen-supervisor'];

        $dutyTypeArr = ['production', 'serve'];

        return in_array($slug, $kitchenArr) ? $dutyTypeArr[0] : $dutyTypeArr[1];
    }

    public function createSeederData($data)
    {
        $name = $data[0];
        $slug = $this->processTitleSlug($name);
        $duty = $this->getDutyType($slug);

        $parameters = [
            'name' => $name,
            'slug' => $slug,
            'duty' => $duty
        ];

        return $this->supervisorTypeRepository->saveSeeder($parameters);
    }
}