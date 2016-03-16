<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \App\Http\Middleware\Cors::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
        ],
        
        // for guests only - login, password remind
        'api_guest' => [
            'throttle:5,1',
            'guest',
        ],
        
        // when logout (without token refresh and permission checking)
        'api_logout' => [
            'throttle:60,1',
            'auth',
        ],
        
        // standard api authorized user with permission checking
        'api_authorized' => [
            'throttle:60,1',
            'auth',
            'refresh.token',
            'authorize',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,

        // user module middlewares
        'auth' => \App\Modules\User\Http\Middleware\Authenticate::class,
        'guest' => \App\Modules\User\Http\Middleware\VerifyIfAuthenticated::class,
        'throttle' => \App\Modules\User\Http\Middleware\ThrottleRequests::class,
        'refresh.token' => \App\Modules\User\Http\Middleware\RefreshToken::class,

        'authorize' => \App\Http\Middleware\Authorize::class,
    ];
}
