<?php

namespace App\Modules\User\Http\Middleware;

use App\Helpers\ApiResponse;
use App\Helpers\ErrorCode;
use Closure;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use JWTAuth;

class RefreshToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     * @internal param null|string $guard
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // if we refreshed token in Authenticate middleware, we attach to
        // response same header that we put in request
        if ($request->header('JWTRefreshed', null) == 1) {
            $response->headers->set('Authorization',
                $request->header('Authorization'));
        }

        return $response;
    }
}
