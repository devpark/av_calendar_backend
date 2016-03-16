<?php

/**
 * Those users will be created if they don't exist in application. Main admin
 * user is defined in .env file
 */
return [
    [
        'email' => 't1@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::DEVELOPER,
        'first_name' => 'test',
        'last_name' => 'user',
    ],
    [
        'email' => 't2@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::ADMIN,
        'first_name' => 't2 ',
        'last_name' => 'user',
    ],
];
