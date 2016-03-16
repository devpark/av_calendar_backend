<?php

namespace App\Modules\CalendarAvailability\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAvailability;
use App\Modules\CalendarAvailability\Http\Requests\CalendarAvailabilityIndex;
use App\Modules\CalendarAvailability\Http\Requests\CalendarAvailabilityShow;
use App\Modules\CalendarAvailability\Http\Requests\CalendarAvailabilityStore;
use App\Modules\CalendarAvailability\Contracts\CalendarAvailability as CalendarService;
use Carbon\Carbon;
use Illuminate\Http\Response;

class CalendarAvailabilityController extends Controller
{
    /**
     * Display list of calendar availabilities
     *
     * @param CalendarAvailabilityIndex $request
     * @param CalendarService $service
     *
     * @return Response
     */
    public function index(
        CalendarAvailabilityIndex $request,
        CalendarService $service
    ) {
        $startDate = Carbon::parse($request->input('from'))->startOfWeek();
        $endDate = with(clone ($startDate))
            ->addDays($request->input('limit', 10) - 1);

        $users = $service->find($startDate, $endDate);

        return ApiResponse::responseOk($users, 200,
            [
                'date_start' => $startDate->format('Y-m-d'),
                'date_end' => $endDate->format('Y-m-d'),
            ]);
    }

    /**
     * Set user availability for given day. Removes any existing entries for
     * this user in this day
     *
     * @param CalendarAvailabilityStore $request
     * @param User $user
     * @param $day
     * @param UserAvailability $userAv
     *
     * @return Response
     */
    public function store(
        CalendarAvailabilityStore $request,
        User $user,
        $day,
        UserAvailability $userAv
    ) {
        $userAv::add($user->id, $day, $request->input('availabilities', []));

        return ApiResponse::responseOk(
            UserAvailability::getForObjectsAndDays($user->id, $day), 201);
    }

    /**
     * Get calendar availability for selected user in selected day
     *
     * @param CalendarAvailabilityShow $request
     * @param User $user
     * @param $day
     *
     * @return Response
     * @internal param int $id
     *
     */
    public function show(CalendarAvailabilityShow $request, User $user, $day)
    {
        return ApiResponse::responseOk(
            UserAvailability::getForObjectsAndDays($user->id, $day), 200);
    }
}
