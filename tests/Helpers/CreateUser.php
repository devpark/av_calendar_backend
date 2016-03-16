<?php

namespace Tests\Helpers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

trait CreateUser
{
    /**
     * Testing user e-mail
     *
     * @var string
     */
    protected $userEmail;

    /**
     * Testing user password
     *
     * @var string
     */
    protected $userPassword;

    /**
     * User
     *
     * @var User|null
     */
    protected $user;

    /**
     * Creates user for tests
     *
     * @param int $deleted
     *
     * @return $this
     */
    protected function createUser($deleted = 0)
    {
        $this->userEmail = 'useremail@example.com';
        $this->userPassword = 'testpassword';

        $this->user = factory(User::class, 1)->create([
            'email' => $this->userEmail,
            'password' => $this->userPassword,
            'deleted' => $deleted,
        ]);

        return $this;
    }

    /**
     * Sets user given role
     *
     * @param string $roleType
     *
     * @return $this
     */
    protected function setRole($roleType)
    {
        $this->user->role_id = Role::where('name', $roleType)->first()->id;
        $this->user->save();

        return $this;
    }

    /**
     * Format single user into array
     *
     * @param User $user
     *
     * @return array
     */
    protected function formatUser(User $user)
    {
        $user = $user->toArray();
        $user = array_intersect_key($user, array_flip([
            'id',
            'email',
            'first_name',
            'last_name',
            'role_id',
            'avatar',
            'deleted',
        ]));

        $user['deleted'] = (bool)$user['deleted'];
        if (!isset($user['avatar'])) {
            $user['avatar'] = '';
        }

        return $user;
    }

    /**
     * Format collection of users into array
     *
     * @param Collection $users
     *
     * @return array
     */
    protected function formatUsers(Collection $users)
    {
        $result = [];
        foreach ($users as $user) {
            $result[] = $this->formatUser($user);
        }

        return $result;
    }
}
