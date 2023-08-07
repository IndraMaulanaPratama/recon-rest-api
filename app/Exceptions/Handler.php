<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (NotFoundHttpException $e, $requset) {
            if ($requset->wantsJson()) {
                return response()
                ->json([
                    'result_code' => 404,
                    'result_message' => 'Page not found'
                ], 404)

                ->header('Accept', 'application/json');
            }
        });

        $this->renderable(function (AuthenticationException $e, $requset) {
            if ($requset->wantsJson()) {
                return response()
                ->json([
                    'result_code' => 401,
                    'result_message' => 'Client Unauthorized'
                ], 401)

                ->header('Accept', 'application/json');
            }
        });

        $this->renderable(function (QueryException $e, $requset) {
            if ($requset->wantsJson()) {
                return response()
                ->json([
                    'error' => 'Query Exception',
                    'message' => $e
                ], 400)

                ->header('Accept', 'application/json');
            }
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
