<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
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
            return $this->formatError(
                exception: $e,
                message: 'Route Not Found',
                status: $e->getStatusCode()
            );
        });

        $this->renderable(function (ValidationException $e) {
            return $this->formatError($e, $e->getMessage(), $e->errors(), $e->status);
        });

        $this->renderable(function (Throwable $e) {
            return $this->formatError(exception: $e);
        });
    }
 
    private function formatError(Exception $exception, ?string $message = null, ?array $errors = null, $status = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        return response()->json([
            'type' => str(get_class($exception))->explode('\\')->last(),
            'message' => $message ?? $exception->getMessage(),
            'errors' => $errors
        ], $status);
    }
}
