<?php

namespace App\Exceptions;

use Exception;

class TimeSlotNotAvailableException extends Exception
{
    protected $code = 409;
}