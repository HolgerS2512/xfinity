<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        ModelNotFoundException::class => 'warning',
        ValidationException::class => 'info',
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
        $this->reportable(function (Throwable $e) {
            if ($e instanceof ModelNotFoundException) {
                Log::warning('Model not found: ' . $e->getMessage(), ['exception' => $e]);
            } elseif ($e instanceof QueryException) {
                Log::error('Database Query Exception', [
                    'message' => $e->getMessage(),
                    'sql' => $e->getSql(),
                    'bindings' => $e->getBindings(),
                    'exception' => $e,
                ]);
            } elseif ($e instanceof HttpException) {
                // If possible, include request data 
                $requestData = app('request')->all();
                Log::error('HTTP Exception', [
                    'message' => $e->getMessage(),
                    'status_code' => $e->getStatusCode(),
                    'request_data' => $requestData,
                    'exception' => $e
                ]);
            } elseif ($e instanceof \Exception) {
                Log::error('General Exception', [
                    'message' => $e->getMessage(),
                    'exception' => $e,
                ]);
            } else {
                Log::error('An unexpected error occurred: ' . $e->getMessage(), ['exception' => $e]);
            }
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        // Custom response for ModelNotFoundException
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'status' => false,
            ], 404);
        }

        // Custom response for HttpResponseException
        if ($exception instanceof HttpResponseException) {
            return response()->json([
                'status' => false,
            ], $exception->getResponse()->getStatusCode());
        }

        if ($exception instanceof QueryException) {
            return response()->json([
                'status' => false,
            ], 500);
        }

        // Default response for other exceptions
        return parent::render($request, $exception);
    }
}
