<?php

namespace App\Modules\CalendarAvailability\Http\Requests;

use App\Http\Requests\Request;

class CalendarAvailabilityIndex extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'from' => ['required','date'],
            'limit' => ['int','min:1','max:31'],
        ];

        return $rules;
    }
}
