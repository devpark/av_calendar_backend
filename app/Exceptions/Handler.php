<?php

namespace App\Exceptions;

use App\Helpers\ApiResponse;
use App\Helpers\ErrorCode;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exception\HttpResponseException;
use PDOException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [

    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $e
     *
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $e
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        // if debugging is set to true, we will return standard error response
        // to easier detect error and solve it by developer
        if (config('app.debug', false)) {
            return parent::render($request, $e);
        }

        // in case of validation errors, we want to just return response
        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        }

        // otherwise we will return custom API response

        switch (get_class($e)) {
            case ModelNotFoundException::class:
                $errorCode = ErrorCode::RESOURCE_NOT_FOUND;
                $responseCode = 404;
                break;
            case NotFoundHttpException::class:
            case MethodNotAllowedHttpException::class:
                $errorCode = ErrorCode::NOT_FOUND;
                $responseCode = 404;
                break;
            case PDOException::class:
                $errorCode = ErrorCode::DATABASE_ERROR;
                $responseCode = 500;
                break;
            default:
                $errorCode = ErrorCode::API_ERROR;
                $responseCode = 500;
        }

        return ApiResponse::responseError($errorCode, $responseCode);
    }
}
