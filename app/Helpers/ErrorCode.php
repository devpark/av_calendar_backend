<?php

namespace App\Helpers;

class ErrorCode
{
    // GENERAL
    const VALIDATION_FAILED = 'general.validation_failed';
    const REQUESTS_RATE_EXCEEDED = 'general.request_rate_exceeded';
    const NO_PERMISSION = 'general.no_action_permission';
    const RESOURCE_NOT_FOUND = 'general.no_resource_found';
    const API_ERROR = 'general.api_error';
    const NOT_FOUND = 'general.invalid_action_or_method';
    const DATABASE_ERROR = 'general.database_error';
    
    // AUTH
    const AUTH_INVALID_LOGIN_DATA = 'auth.invalid_login_data';
    const AUTH_CANNOT_CREATE_TOKEN = 'auth.cannot_create_token';
    const AUTH_INVALID_TOKEN = 'auth.invalid_token';
    const AUTH_EXPIRED_TOKEN = 'auth.expired_token';
    const AUTH_USER_NOT_FOUND = 'auth.user_not_found';
    const AUTH_ALREADY_LOGGED = 'auth.user_already_logged';

    // PASSWORD
    const PASSWORD_NO_USER_FOUND = 'password.no_user_found';
    const PASSWORD_INVALID_PASSWORD = 'password.invalid_password';
    const PASSWORD_INVALID_TOKEN = 'password.invalid_token';
}
