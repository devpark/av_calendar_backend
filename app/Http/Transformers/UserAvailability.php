<?php

namespace App\Http\Transformers;

use App\Models\UserAvailability as Availability;

class UserAvailability extends AbstractTransformer
{
    /**
     * Transform UserAvailability object into array
     *
     * @param Availability $availability
     *
     * @return array
     */
    public function transform(Availability $availability)
    {
        return [
            'day' => $availability->day,
            'time_start' => $availability->time_start,
            'time_stop' => $availability->time_stop,
            'available' => (bool)$availability->available,
            'description' => $availability->description,
        ];
    }
}
