<?php

namespace App\Providers;

use App\Modules\CalendarAvailability\Http\Controllers\CalendarAvailabilityController;
use App\Modules\User\Http\Controllers\RoleController;
use App\Modules\User\Http\Controllers\UserController;
use App\Policies\CalendarAvailabilityControllerPolicy;
use App\Policies\RoleControllerPolicy;
use App\Policies\UserControllerPolicy;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        UserController::class => UserControllerPolicy::class,
        RoleController::class => RoleControllerPolicy::class,
        CalendarAvailabilityController::class => CalendarAvailabilityControllerPolicy::class,
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);
    }
}
