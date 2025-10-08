<?php

namespace App\Exceptions;

use Exception;

class KedrApiException extends Exception
{
    protected array $context;

    public function __construct(string $message, array $context = [], int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    public function context(): array
    {
        return $this->context;
    }
}
