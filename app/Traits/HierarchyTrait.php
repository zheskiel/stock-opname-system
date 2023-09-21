<?php
namespace App\Traits;

trait HierarchyTrait
{
    private function loadOnlyWithParam($param)
    {
        return [
            $param => function($query) {
                $query->orderBy('name');
            }
        ];
    }

    public function loadProvinceOnly($param = 'province')
    {
        return $this->loadOnlyWithParam($param);
    }

    public function loadRegencyOnly($param = 'regency')
    {
        return $this->loadOnlyWithParam($param);
    }

    public function loadDistrictOnly($param = 'district')
    {
        return $this->loadOnlyWithParam($param);
    }

    public function loadLocationOnly($param = 'location')
    {
        return $this->loadOnlyWithParam($param);
    }

    public function loadOutletOnly($param = 'outlet')
    {
        return $this->loadOnlyWithParam($param);
    }

    public function loadManagerOnly($param = 'manager')
    {
        return $this->loadOnlyWithParam($param);
    }

    public function loadSupervisorOnly($param = 'supervisor')
    {
        return $this->loadOnlyWithParam($param);
    }

    public function loadProvinceWithRegency()
    {
        return [
            'province' => function($query) {
                $query->with($this->loadRegencyWithDistrict())->orderBy('name');
            }
        ];
    }

    public function loadRegencyWithDistrict()
    {
        return [
            'regency' => function($query) {
                $query->with($this->loadDistrictWithLocation())->orderBy('name');
            }
        ];
    }

    public function loadDistrictWithLocation()
    {
        return [
            'district' => function($query) {
                $query->with($this->loadLocationWithOutlet())->orderBy('name');
            }
        ];
    }

    public function loadLocationWithOutlet()
    {
        return [
            'location' => function($query) {
                $query->with($this->loadOutletWithManager())->orderBy('name');
            }
        ];
    }

    public function loadOutletWithManager()
    {
        return [
            'outlet' => function($query) {
                $query->with($this->loadManagerWithSupervisor())->orderBy('name');
            }
        ];
    }

    public function loadManagerWithSupervisor()
    {
        return [
            'manager' => function($query) {
                $query->with($this->loadSupervisorWithSupervisorPicAndType())->orderBy('name');
            }
        ];
    }

    public function loadSupervisorWithSupervisorPicAndType()
    {
        return [
            'supervisor' => function($query) {
                $query->with([
                    'supervisor_pic',
                    $this->loadSupervisorTypeWithStaff()
                ])->orderBy('name')->first();
            }
        ];
    }

    public function loadSupervisorTypeWithStaff()
    {
        return 'type';
    }
}