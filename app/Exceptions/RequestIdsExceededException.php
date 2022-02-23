<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class RequestIdsExceededException extends UnprocessableEntityHttpException
{
    public function __construct(string $message = 'Too many IDs for one time request.')
    {
        parent::__construct($message);
    }
}
