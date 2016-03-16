<?php

Route::group(['middleware' => 'api_authorized'], function () {
    Route::get('users/{user}/availabilities/{day}',
        'CalendarAvailabilityController@show');
    Route::post('users/{user}/availabilities/{day}',
        'CalendarAvailabilityController@store');
    Route::get('users/availabilities/', 'CalendarAvailabilityController@index');
});
