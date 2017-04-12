<?php

namespace App\Modules\User\Http\Requests;

use App\Http\Requests\Request;

class CreateUser extends Request
{
    public function rules()
    {
        $rules = [
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:6'],
            'first_name' => ['required', 'max:255'],
            'last_name' => ['required', 'max:255'],
            'url' => ['required'],
        ];

        return $rules;
    }
}
