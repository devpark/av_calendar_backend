<?php

namespace App\Modules\User\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Helpers\ErrorCode;
use App\Http\Controllers\Controller;
use App\Modules\User\Http\Requests\AuthLogin;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;

class AuthController extends Controller
{
    /**
     * @var JWTAuth
     */
    protected $auth;

    /**
     * @var Guard
     */
    protected $guard;

    public function __construct(JWTAuth $auth, Guard $guard)
    {
        $this->auth = $auth;
        $this->guard = $guard;
    }

    /**
     * Log in user
     *
     * @param AuthLogin $request
     *
     * @return Response
     */
    public function login(AuthLogin $request)
    {
        // we allow to log in only users that are not deleted
        $credentials = array_merge(
            $request->only('email', 'password'),
            ['deleted' => 0]);

        // invalid user
        if (!$this->guard->attempt($credentials)) {
            return ApiResponse::responseError(ErrorCode::AUTH_INVALID_LOGIN_DATA,
                401);
        }

        // get user
        $user = $this->guard->user();

        // create user token
        try {
            $token = $this->auth->fromUser($user);
        } catch (JWTException $e) {
            return ApiResponse::responseError(ErrorCode::AUTH_CANNOT_CREATE_TOKEN,
                500);
        }

        return ApiResponse::responseOk(['token' => $token], 201);
    }

    /**
     * Log out user
     *
     * @return Response
     */
    public function logout()
    {
        $this->auth->invalidate();

        return ApiResponse::responseOk([], 204);
    }
}
