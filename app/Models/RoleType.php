<?php

namespace App\Models;

class RoleType
{
    const ADMIN = 'admin';
    const DEALER = 'dealer';
    const DEVELOPER = 'developer';
    const CLIENT = 'client';

    /**
     * Get all available role types
     *
     * @return array
     */
    public static function all()
    {
        return [
            self::ADMIN,
            self::DEALER,
            self::DEVELOPER,
            self::CLIENT,
        ];
    }

    /**
     * Get default user role
     *
     * @return string
     */
    public static function default()
    {
        return self::CLIENT;
    }
}
