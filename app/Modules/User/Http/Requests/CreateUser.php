<?php

namespace App\Modules\User\Http\Requests;

use App\Http\Requests\Request;

class CreateUser extends Request
{
    public function rules()
    {
        $rules = [
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['confirmed', 'min:6'],
            'first_name' => ['max:255'],
            'last_name' => ['max:255'],
            'role_id' => ['required', 'exists:roles,id'],
            'send_user_notification' => ['boolean'],
            'url' => ['required_if:send_user_notification,true'],
        ];
        
        // if user wants to register himself, we use different rules
        if (!auth()->check()) {
            // we don't allow to set role - we will use default one
            unset($rules['role_id']);
            // password has to be filled in this case
            $rules['password'][] = 'required';
            // we might need to require different fields
            $rules['first_name'][] = 'required';
            $rules['last_name'][] = 'required';
        }
        
        return $rules;
    }
}
