<?php
namespace App\Exceptions;

use JWTAuth;
use Exception;
use App\Traits\ApiResponsesTrait;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class Handler extends ExceptionHandler
{
    use ApiResponsesTrait;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // try {

        //     JWTAuth::parseToken()->authenticate();

        // } catch (TokenExpiredException $e) {
        //     return $this
        //         ->respondUnAuthenticated('Token Expired');

        // } catch (TokenInvalidException $e) {
        //     return $this
        //         ->respondUnAuthenticated('Token Invalid');

        // } catch (JWTException $e) {
        //     return $this
        //         ->respondUnAuthenticated('Token not provided');

        // } catch (Exception $e) {
        //     return $this
        //         ->respondError($e->getMessage());

        // }

        return parent::render($request, $exception);
    }
}
