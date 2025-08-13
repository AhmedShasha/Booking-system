<?php

namespace App\Exceptions;

use Exception;

class BookingValidationException extends Exception
{
    protected $code = 422;
}