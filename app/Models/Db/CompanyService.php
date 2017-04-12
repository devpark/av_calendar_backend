<?php

namespace App\Models\Db;

class CompanyService extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'pkwiu',
        'vat_rate_id',
        'creator_id',
        'editor_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function vatRate()
    {
        return $this->belongsTo(VatRate::class);
    }
}
