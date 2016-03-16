<?php

namespace App\Policies;

use App\Models\User;

class CalendarAvailabilityControllerPolicy extends BasePolicy
{
    protected $group = 'calendar';

    /**
     * Check whether current user can access requested user's data.
     *
     * @param  User   $user
     * @param  User   $displayedUser
     * @param  string $date
     * @return bool
     */
    public function show(User $user, User $displayedUser, $date)
    {
        return (bool) User::allowed($user)->find($displayedUser->id);
    }
}
