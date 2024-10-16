<?php

namespace App\Exceptions;

// Routing and HTTP Exceptions
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException as SymRouteNotFoundException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
// Authentication and authorization
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
// Database and ORM (Eloquent) Exceptions
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Doctrine\DBAL\Exception\ConnectionException as ExceptionConnectionException;
use ErrorException;
use Illuminate\Database\DeadlockException;
// File and storage exceptions
use Illuminate\Contracts\Filesystem\FileNotFoundException;
// Session and Cookie Exceptions
use Illuminate\Session\TokenMismatchException;
// Queue Exceptions
use Illuminate\Queue\MaxAttemptsExceededException;
use Illuminate\Queue\InvalidPayloadException;
// Mail & Notification Exceptions
use Symfony\Component\Mailer\Exception\TransportException;
// Broadcasting Exceptions
use Illuminate\Broadcasting\BroadcastException;
// Cache Exceptions
use Illuminate\Contracts\Cache\LockTimeoutException;
// Encryption and hashing exceptions
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Contracts\Encryption\DecryptException;
// More Symfony Exceptions
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
// Kernel
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
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
                Log::info('Model not found: ' . $e->getMessage(), ['exception' => $e]);
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
        /*
        |--------------------------------------------------------------------------
        | Routing and HTTP Exceptions
        |--------------------------------------------------------------------------
        */

        if ($exception instanceof AccessDeniedHttpException) {
            return response()->json([
                'status' => false,
            ], 403);
        }

        if (
            $exception instanceof NotFoundHttpException ||
            $exception instanceof SymRouteNotFoundException
        ) {
            return response()->json([
                'status' => false,
            ], 404);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'status' => false,
            ], 405);
        }

        if (
            $exception instanceof UrlGenerationException ||
            $exception instanceof HttpException ||
            $exception instanceof HttpResponseException
        ) {
            return response()->json([
                'status' => false,
            ], 500);
        }

        /*
        |--------------------------------------------------------------------------
        | Authentication and authorization
        |--------------------------------------------------------------------------
        */

        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'status' => false,
            ], 401);
        }

        if ($exception instanceof AuthorizationException) {
            return response()->json([
                'status' => false,
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Database and ORM (Eloquent) Exceptions
        |--------------------------------------------------------------------------
        */

        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'status' => false,
            ], 404);
        }

        if (
            $exception instanceof QueryException ||
            $exception instanceof RelationNotFoundException ||
            $exception instanceof ExceptionConnectionException ||
            $exception instanceof DeadlockException
        ) {
            return response()->json([
                'status' => false,
            ], 500);
        }

        /*
        |--------------------------------------------------------------------------
        | Validation Exceptions ---> \App\Http\Requests\JsonResponseRequest
        |--------------------------------------------------------------------------
        */

        /*
        |--------------------------------------------------------------------------
        | File and storage exceptions
        |--------------------------------------------------------------------------
        */

        if ($exception instanceof FileNotFoundException) {
            return response()->json([
                'status' => false,
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Session and Cookie Exceptions
        |--------------------------------------------------------------------------
        */

        if ($exception instanceof TokenMismatchException) {
            return response()->json([
                'status' => false,
                'message' => 'csrf_missmatch',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Queue Exceptions
        |--------------------------------------------------------------------------
        */

        if ($exception instanceof InvalidPayloadException) {
            return response()->json([
                'status' => false,
            ], 400);
        }

        if ($exception instanceof MaxAttemptsExceededException) {
            return response()->json([
                'status' => false,
            ], 429);
        }

        /*
        |--------------------------------------------------------------------------
        | Mail & Notification Exceptions
        |--------------------------------------------------------------------------
        */

        if ($exception instanceof TransportException) {
            return response()->json([
                'status' => false,
            ], 500);
        }

        /*
        |--------------------------------------------------------------------------
        | Broadcasting Exceptions
        |--------------------------------------------------------------------------
        */

        if ($exception instanceof BroadcastException) {
            return response()->json([
                'status' => false,
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Cache Exceptions
        |--------------------------------------------------------------------------
        */

        if ($exception instanceof LockTimeoutException) {
            return response()->json([
                'status' => false,
            ], 500);
        }

        /*
        |--------------------------------------------------------------------------
        | Encryption and hashing exceptions
        |--------------------------------------------------------------------------
        */

        if (
            $exception instanceof EncryptException ||
            $exception instanceof DecryptException
        ) {
            return response()->json([
                'status' => false,
            ], 500);
        }

        /*
        |--------------------------------------------------------------------------
        | More Symfony Exceptions
        |--------------------------------------------------------------------------
        */

        if ($exception instanceof BadRequestHttpException) {
            return response()->json([
                'status' => false,
            ], 400);
        }

        if ($exception instanceof UnauthorizedHttpException) {
            return response()->json([
                'status' => false,
            ], 403);
        }

        if ($exception instanceof ConflictHttpException) {
            return response()->json([
                'status' => false,
            ], 409);
        }

        /*
        |--------------------------------------------------------------------------
        | Generally Exceptions
        |--------------------------------------------------------------------------
        */

        if ($exception instanceof ErrorException) {
            return response()->json([
                'status' => false,
            ], 500);
        }

        return parent::render($request, $exception);
    }
}
