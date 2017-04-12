<?php

namespace App\Models\Db;

use App\Modules\CalendarAvailability\Traits\CalendarAvailable;

class UserAvailability extends Model
{
    use CalendarAvailable;

    /**
     * {@inheritdoc}
     */
    protected $table = 'user_availability';

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'user_id',
        'day',
        'time_start',
        'time_stop',
        'available',
        'description',
        'company_id',
    ];

    /**
     * {inheritdoc}.
     */
    public $timestamps = false;

    /**
     * Availability is assigned to specific user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Availability is assigned to company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
