<?php

namespace App\Exceptions;

use Exception;

class InvalidSerialException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
