<?php

namespace App\Modules\CalendarAvailability\Services;

use App\Models\Db\User;
use Carbon\Carbon;
use App\Modules\CalendarAvailability\Contracts\CalendarAvailability as CalendarAvailabilityContract;
use Illuminate\Contracts\Auth\Guard;

class CalendarAvailability implements CalendarAvailabilityContract
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var Guard
     */
    protected $auth;

    /**
     * CalendarAvailability constructor.
     *
     * @param User $user
     * @param Guard $auth
     */
    public function __construct(User $user, Guard $auth)
    {
        $this->user = $user;
        $this->auth = $auth;
    }

    /**
     * {@inheritdoc}
     */
    public function find(Carbon $startDate, Carbon $endDate)
    {
        return $this->user->newQuery()
            ->active()
            ->allowed()
            ->orderBy('id', 'asc')
            ->withAvailabilities($startDate, $endDate, $this->auth->user()->getSelectedCompanyId())
            ->get();
    }
}
