<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\InvalidCastException;
use Illuminate\Database\Eloquent\JsonEncodingException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
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
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        $env = env('APP_ENV');
        $message = null;
        $codeHttp = null;

        if(
            $exception instanceof Exception || 
            $exception instanceof InvalidCastException ||
            $exception instanceof QueryException || 
            $exception instanceof JsonEncodingException
        ) {
            $message = $env === 'local' ? $exception->getMessage() : 'Internal server error';
            $codeHttp = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        if(
            $exception instanceof ModelNotFoundException || 
            $exception instanceof NotFoundHttpException || 
            $exception instanceof RelationNotFoundException
        ) {
            $message = $env === 'local' ? $exception->getMessage() : 'No results found';
            $codeHttp = Response::HTTP_NOT_FOUND;
        }

        if(!empty($message) && !empty($codeHttp)) {
            return response()->json(['message' => $message], $codeHttp);
        }
    }
}
