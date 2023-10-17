<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use App\Models\Route;
use App\Traits\ApiResponsesTrait;
use App\Traits\HelpersTrait;
use Symfony\Component\HttpFoundation\Response;

class RouteMiddleware
{
    use ApiResponsesTrait;
    use HelpersTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()->getName();
        $routes = Route::where('route', $routeName)->first();

        $userLists = $this->getUserLists();

        if (blank($routes) || (bool) $routes->status) {
            foreach($userLists as $user) {
                $target = $request->user("$user-api");

                if ($target !== null) {
                    return ($target->can($routes->permission_name))
                        ? $next($request)
                        : $this->respondUnAuthenticated("You do not have access to this route!");
                }    
            }
        }

        return $this->respondUnAuthenticated("Unauthenticated");
    }
}
