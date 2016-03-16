<?php

namespace App\Modules\User\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Helpers\ErrorCode;
use App\Http\Controllers\Controller;
use App\Modules\User\Http\Requests\ResetPassword;
use App\Modules\User\Http\Requests\SendResetEmail;
use Illuminate\Mail\Message;
use Password;

class PasswordController extends Controller
{
    /**
     * Send reset e-mail
     *
     * @param SendResetEmail $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(SendResetEmail $request)
    {
        // send reset link in e-mail
        $response = Password::broker()->sendResetLink(
            array_merge($request->only('email'), ['deleted' => 0]),
            function (Message $message) {
                $message->subject(trans('emails.password_reset.subject'));
            });

        // return valid response depending on Password broker response
        switch ($response) {
            case Password::RESET_LINK_SENT:
                return ApiResponse::responseOk([], 201);

            case Password::INVALID_USER:
            default:
                return ApiResponse::responseError(
                    ErrorCode::PASSWORD_NO_USER_FOUND, 404);
        }
    }

    /**
     * Reset user password
     *
     * @param ResetPassword $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset(ResetPassword $request)
    {
        $credentials = array_merge($request->only('email', 'password',
            'password_confirmation', 'token'), ['deleted' => 0]);

        // try to change user password 
        $response = Password::broker()
            ->reset($credentials, function ($user, $password) {
                $user->password = $password;
                $user->save();
            });

        // return valid response based on Password broker response
        switch ($response) {
            case Password::PASSWORD_RESET:
                return ApiResponse::responseOk();
            case Password::INVALID_PASSWORD:
                return ApiResponse::responseError(
                    ErrorCode::PASSWORD_INVALID_PASSWORD, 422);
            case Password::INVALID_TOKEN:
                return ApiResponse::responseError(
                    ErrorCode::PASSWORD_INVALID_TOKEN, 422);
            case Password::INVALID_USER:
            default:
                return ApiResponse::responseError(
                    ErrorCode::PASSWORD_NO_USER_FOUND, 404);
        }
    }
}
