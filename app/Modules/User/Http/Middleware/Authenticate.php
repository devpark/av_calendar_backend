<?php

namespace App\Modules\User\Http\Middleware;

use App\Helpers\ApiResponse;
use App\Helpers\ErrorCode;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Container\Container;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\JWTAuth;

class Authenticate
{
    /**
     * @var JWTAuth
     */
    protected $tokenAuth;
    
    /**
     * @var Container
     */
    protected $app;
    
    /**
     * @var Guard
     */
    protected $guard;

    /**
     * Authenticate constructor.
     *
     * @param JWTAuth $tokenAuth
     * @param Container $app
     * @param Guard $guard
     */
    public function __construct(JWTAuth $tokenAuth, Container $app, Guard $guard)
    {
        $this->tokenAuth = $tokenAuth;
        $this->app = $app;
        $this->guard = $guard;
    }
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $tokenExpired = false;
        
        // in case it's testing environment and user is already logged
        // we won't do anything more
        if ($this->app->environment('testing') && $this->guard->user()) {
            return $next($request);
        }
        
        // otherwise we will try to authenticate user via token

        try {
            /** @var User $user */
            $user = $this->tokenAuth->setRequest($request)->parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            $tokenExpired = true;
        } catch (\Exception $e) {
            return ApiResponse::responseError(ErrorCode::AUTH_INVALID_TOKEN,
                401);
        }

        // token was expired, we will try to refresh it
        if ($tokenExpired) {
            try {
                $user = $this->refreshToken($request);
            } catch (TokenExpiredException $e) {
                return ApiResponse::responseError(ErrorCode::AUTH_EXPIRED_TOKEN,
                    400);
            } catch (\Exception $e) {
                return ApiResponse::responseError(ErrorCode::AUTH_INVALID_TOKEN,
                    401);
            }
        }

        // we allow authenticate only users that are not deleted
        if (!$user || $user->isDeleted()) {
            return ApiResponse::responseError(ErrorCode::AUTH_USER_NOT_FOUND,
                404);
        }

        return $next($request);
    }

    /**
     * Refreshes token when it's expired
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return User
     */
    protected function refreshToken($request)
    {
        $newToken = $this->tokenAuth->refresh();
        $request->headers->set('JWTRefreshed', '1', true);
        $request->headers->set('Authorization', 'Bearer ' . $newToken, true);

        return  $this->tokenAuth->authenticate();
    }
}
