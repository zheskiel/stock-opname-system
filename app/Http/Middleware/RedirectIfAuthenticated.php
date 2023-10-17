<?php
namespace App\Http\Middleware;

use Closure;
use App\Traits\ApiResponsesTrait;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    use ApiResponsesTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (
                ($guard == "admin" || $guard == "manager" || $guard == "staff") && 
                Auth::guard($guard . "-api")->check()
            ) {
                return $next($request);
            }
        }

        return $this->respondUnAuthenticated("Not Authorized");
    }
}
