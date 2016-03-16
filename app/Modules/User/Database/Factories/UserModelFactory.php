<?php

$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    return [
        'email' => $faker->unique()->safeEmail,
        'password' => $faker->password,
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'role_id' => $faker->randomElement(\App\Models\Role::all()->pluck('id')
            ->all()),
        'deleted' => 0,
    ];
});
