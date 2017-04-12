<?php

namespace App\Models\Db;

class InvoiceFormat extends Model
{
    protected $fillable = [
        'name',
        'format',
        'example',
    ];

    public function findByFormat($format)
    {
        return $this->where('format', 'like', '%' . trim($format) . '%')->firstOrFail();
    }
}
