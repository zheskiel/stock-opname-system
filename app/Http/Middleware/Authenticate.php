<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponsesTrait;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;

class Authenticate extends Middleware
{
    use  ApiResponsesTrait;

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        if ($request->is('api/*')) {
            throw new HttpResponseException(
                $this->respondUnAuthenticated("Unauthenticated")
            );
        }

        if (!$request->expectsJson()) {
            return route('index');
        }
    }
}
