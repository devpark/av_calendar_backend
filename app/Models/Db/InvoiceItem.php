<?php

namespace App\Models\Db;

use App\Modules\SaleInvoice\Traits\PriceNormalize;

class InvoiceItem extends Model
{
    use PriceNormalize;

    protected $fillable = [
        'invoice_id',
        'company_service_id',
        'name',
        'custom_name',
        'price_net',
        'price_net_sum',
        'price_gross',
        'price_gross_sum',
        'vat_rate',
        'vat_rate_id',
        'vat_sum',
        'quantity',
        'base_document_id',
        'creator_id',
        'is_correction',
        'position_corrected_id',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function positionCorrected()
    {
        return $this->belongsTo(self::class, 'position_corrected_id');
    }

    public function vatRate()
    {
        return $this->belongsTo(VatRate::class);
    }

    public function getPrintNameAttribute()
    {
        return $this->custom_name ?? $this->name;
    }
}
