<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponse;
use App\Helpers\ErrorCode;
use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{
    /**
     * By default we authorize all requests
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * If validation fails we return error response
     *
     * @param array $errors
     *
     * @return \Symfony\Component\HttpFoundation\Response|void
     */
    public function response(array $errors)
    {
        return ApiResponse::responseError(ErrorCode::VALIDATION_FAILED, 422,
            $errors);
    }
}
