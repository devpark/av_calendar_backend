<?php

namespace App\Models\Filesystem;

class UserConfig
{
    /**
     * @param array $users
     *
     * @return array
     */
    public function get($users)
    {
        return array_unique($users);
    }
}
