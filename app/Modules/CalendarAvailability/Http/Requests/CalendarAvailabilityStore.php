<?php

namespace App\Modules\CalendarAvailability\Http\Requests;

use App\Http\Requests\Request;
use App\Models\UserCompanyStatus;

class CalendarAvailabilityStore extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'availabilities' => ['array'],
            'availabilities.*.time_start' => ['date_format:H:i:s'],
            'availabilities.*.time_stop' => ['date_format:H:i:s'],
            'availabilities.*.available' => ['required', 'boolean'],
            'availabilities.*.description' => ['max:50'],
            'user' => ['required'],
            'day' => ['required', 'date', 'after:yesterday'],
            'selected_company_id' => [
                'required',
                'exists:user_company,company_id,user_id,' . auth()->user()->id . ',status,' .
                UserCompanyStatus::APPROVED,
            ],
        ];

        if (auth()->user()->isAdmin() || auth()->user()->isOwner()) {
            // make sure they add availabilities only for users assigned to selected company
            $data = $this->all();
            if (isset($data['selected_company_id'])) {
                $rules['user'][] = 'exists:user_company,user_id,company_id,' .
                    $data['selected_company_id'] . ',status,' . UserCompanyStatus::APPROVED;
            }
        } else {
            // non-admin users can add only own availabilities
            $rules['user'][] = 'in:' . auth()->user()->id;
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
