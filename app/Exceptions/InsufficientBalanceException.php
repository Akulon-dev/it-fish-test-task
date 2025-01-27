<?php

namespace App\Exceptions;

use Exception;

// Недостаточно средств на балансе
class InsufficientBalanceException extends Exception
{
    public function __construct($message = "Insufficient balance!", $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render($request)
    {
        return response()->json(['error' => 'Insufficient balance'], 400);
    }
}

