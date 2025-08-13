<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    protected $dontReport = [
        AuthenticationException::class,
        ValidationException::class,
        BookingValidationException::class,
        TimeSlotNotAvailableException::class,
    ];

    public function register(): void
    {
        $this->renderable(function (Throwable $e) {
            if (request()->expectsJson()) {
                return match(true) {
                    $e instanceof AuthenticationException => 
                        $this->error('Unauthenticated', 401),
                    
                    $e instanceof ValidationException => 
                        $this->error('Validation failed', 422, $e->errors()),
                    
                    $e instanceof ModelNotFoundException || 
                    $e instanceof NotFoundHttpException => 
                        $this->error('Resource not found', 404),
                    
                    $e instanceof BookingValidationException => 
                        $this->error($e->getMessage(), $e->getCode()),
                    
                    $e instanceof TimeSlotNotAvailableException => 
                        $this->error($e->getMessage(), $e->getCode()),
                    
                    default => $this->handleUnexpectedError($e)
                };
            }

            return parent::render($request, $e);
        });
    }

    private function handleUnexpectedError(Throwable $e)
    {
        if (config('app.debug')) {
            return $this->error($e->getMessage(), 500, [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => collect($e->getTrace())->take(5)->toArray()
            ]);
        }

        return $this->error('Server Error', 500);
    }
}