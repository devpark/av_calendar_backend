<?php

namespace App\Modules\CalendarAvailability\Http\Requests;

use App\Http\Requests\Request;

class CalendarAvailabilityStore extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules =  [
            'availabilities' => ['array'],
            'availabilities.*.time_start' =>  ['date_format:H:i:s'],
            'availabilities.*.time_stop' => ['date_format:H:i:s'],
            'availabilities.*.available' => ['required','boolean'],
            'availabilities.*.description' => ['max:50'],
            'user' => ['required'],
            'day' => ['required','date', 'after:yesterday'],
        ];
        
        // non-admin users can add only own availabilities
        if (!auth()->user()->isAdmin()) {
            $rules['user'][] = 'in:'.auth()->user()->id;
        }
        
        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $data = parent::all();
        // add extra data that should be validated
        $data['day'] = $this->route('day');
        $data['user'] = ($user = $this->route('user')) ? $user->id : null;
        
        return $data;
    }
}
