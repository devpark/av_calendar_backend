<?php

namespace App\Models\Db;

use Illuminate\Database\Eloquent\SoftDeletes;

class Contractor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'main_address_street',
        'main_address_number',
        'main_address_zip_code',
        'main_address_city',
        'main_address_country',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Contractor can be assigned to multiple invoices.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
