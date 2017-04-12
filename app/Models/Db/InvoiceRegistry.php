<?php

namespace App\Models\Db;

class InvoiceRegistry extends Model
{
    protected $fillable = [
        'invoice_format_id',
        'name',
        'company_id',
        'editor_id',
    ];

    public function invoiceFormat()
    {
        return $this->belongsTo(InvoiceFormat::class);
    }
}
