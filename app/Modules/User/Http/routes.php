<?php
// api guest - throttling and verify if not logged
Route::group(['middleware' => 'api_guest'], function () {
    // log in
    Route::post('auth', 'AuthController@login')->name('auth.store');
    // password reset
    Route::post('password/reset', 'PasswordController@sendResetLinkEmail')
        ->name('password.reset.post');
    Route::put('password/reset', 'PasswordController@reset')
        ->name('password.reset.put');
});

// logout - throttling, auth (without refreshing token)
Route::group(['middleware' => 'api_logout'], function () {
    // log out
    Route::delete('auth', 'AuthController@logout')->name('auth.delete');
});

// standard authorized - throttling, auth, token refreshing, permission verification
Route::group(['middleware' => 'api_authorized'], function () {
    // roles
    Route::get('roles', 'RoleController@index')->name('roles.index');
    // users
    Route::get('users', 'UserController@index')->name('users.index');
    Route::get('users/current', 'UserController@current')
        ->name('users.current');
    Route::post('users', 'UserController@store')->name('users.store');
});
