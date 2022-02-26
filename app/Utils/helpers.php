<?php

use Illuminate\Http\Response;

if (! function_exists('exception_response')) {
    function exception_response(Exception $exception, ?string $message = null, ?array $errors = null, $status = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        return response()->json([
            'type' => str(get_class($exception))->explode('\\')->last(),
            'message' => $message ?? $exception->getMessage(),
            'errors' => $errors,
        ], $status);
    }
}
