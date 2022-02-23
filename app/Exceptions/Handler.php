<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
        $this->renderable(function (NotFoundHttpException $e) {
            return exception_response(
                exception: $e,
                message: 'Route Not Found',
                status: $e->getStatusCode()
            );
        });

        $this->renderable(function (ValidationException $e) {
            return exception_response($e, 'Validation failed. Check `errors` for more information.', $e->errors(), $e->status);
        });

        $this->renderable(function (HttpException $e) {
            return exception_response(
                exception: $e,
                status: $e->getStatusCode()
            );
        });

        $this->renderable(function (Throwable $e) {
            if (config('app.env') == 'production') {
                return exception_response(exception: $e);
            }
        });
    }
}
