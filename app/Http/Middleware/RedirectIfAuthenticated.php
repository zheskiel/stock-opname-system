<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
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
            if ($guard == "admin" && Auth::guard($guard)->check()) {
                return redirect('/admin/dashboard');
            }

            if ($guard == "manager" && Auth::guard($guard)->check()) {
                return redirect('/manager/dashboard');
            }

            if (Auth::guard($guard)->check()) {
                return redirect('/home');
            }
        }

        return $next($request);
    }
}
