<?php

namespace App\Modules\User\Services;

use App\Modules\User\Events\UserWasCreated;
use Illuminate\Contracts\Events\Dispatcher as Event;
use Illuminate\Http\Request;
use DB;
use App\Models\User as UserModel;

class User
{
    /**
     * @var Event
     */
    protected $event;

    /**
     * @var User
     */
    protected $user;

    /**
     * User constructor.
     *
     * @param Event $event
     * @param UserModel $user
     */
    public function __construct(Event $event, UserModel $user)
    {
        $this->event = $event;
        $this->user = $user;
    }

    /**
     * Create new user.
     *
     * @param Request $request
     *
     * @return UserModel
     */
    public function create(Request $request)
    {
        return DB::transaction(function () use ($request) {
            // create user with activation hash
            $user =
                $this->user->fill($request->only('email', 'password', 'first_name', 'last_name'));
            $user->activated = false;
            $user->save();
            $user->activate_hash = $user->id . '_' . time() . str_random(40);
            $user->save();

            // fire user created event
            $this->event->fire(new UserWasCreated($user, $request->input('url')));

            // return full user object
            return $user->fresh();
        });
    }

    /**
     * Check correct password for or get true if user is super user.
     *
     * @param UserModel $user
     * @param Request $request
     *
     * @return bool
     */
    public function checkPassword(UserModel $user, Request $request)
    {
        $credentials = [
            'email' => $user->email,
            'password' => $request->input('old_password'),
        ];

        return auth()->user()->isSystemAdmin() || auth()->validate($credentials);
    }

    /**
     * Update data for user.
     *
     * @param UserModel $user
     * @param Request $request
     *
     * @return UserModel
     */
    public function updateData(UserModel $user, Request $request)
    {
        if ($request->input('first_name')) {
            $user->first_name = trim($request->input('first_name'));
        }
        if ($request->input('last_name')) {
            $user->last_name = trim($request->input('last_name'));
        }
        if ($request->input('password')) {
            $user->password = $request->input('password');
        }
        $user->save();

        return $user;
    }
}
