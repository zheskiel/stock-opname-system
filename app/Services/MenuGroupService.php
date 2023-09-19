<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\MenuGroup;

class MenuGroupService
{
    public function create(Request $request): MenuGroup
    {
        return MenuGroup::create(array_merge(
            $request->validated(),
            [
                'status' => !blank($request->status) ? true : false,
                'position' => MenuGroup::max('position') + 1
            ]
        ));
    }

    public function update(Request $request, MenuGroup $menuGroup)
    {
        return $menuGroup->update(array_merge(
            $request->validated(),
            [
                'status' => !blank($request->status) ? true : false
            ]
        ));
    }
}