<?php

namespace App\Services;

use App\Models\Route;
use Illuminate\Http\Request;

class RouteService
{
    public function create(Request $request): Route
    {
        return Route::create(array_merge(
            $request->validated(),
            [
                'status' => !blank($request->status) ? true : false
            ]
        ));
    }

    public function update(Request $request, Route $route)
    {
        return $route->update(array_merge(
            $request->validated(),
            [
                'status' => !blank($request->status) ? true : false
            ]
        ));
    }
}