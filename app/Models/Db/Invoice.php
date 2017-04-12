<?php

namespace App\Models\Db;

use App\Modules\SaleOther\Traits\PriceNormalize;
use Mnabialek\LaravelEloquentFilter\Traits\Filterable;

class Invoice extends Model
{
    use Filterable;
    use PriceNormalize;

    const TYPE_VAT = 'vat';
    const TYPE_CORRECTION = 'correction';

    protected $fillable = [
        'number',
        'order_number',
        'invoice_registry_id',
        'drawer_id',
        'company_id',
        'contractor_id',
        'corrected_invoice_id',
        'correction_type',
        'sale_date',
        'issue_date',
        'invoice_type_id',
        'price_net',
        'price_gross',
        'vat_sum',
        'payment_left',
        'payment_term_days',
        'payment_method_id',
        'paid_at',
        'gross_counted',
    ];

    public function scopePaidLate($query)
    {
        return $query->whereRaw('paid_at IS NOT NULL AND (DATE(DATE_ADD(issue_date, INTERVAL payment_term_days DAY)) < DATE(paid_at))');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function receipts()
    {
        return $this->belongsToMany(Receipt::class, 'invoice_receipt')->withTimestamps();
    }

    public function onlineSales()
    {
        return $this->belongsToMany(OnlineSale::class, 'invoice_online_sale')->withTimestamps();
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function drawer()
    {
        return $this->belongsTo(User::class);
    }

    public function invoiceCompany()
    {
        return $this->hasOne(InvoiceCompany::class);
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function parentInvoices()
    {
        return $this->belongsToMany(self::class, null, 'node_id', 'parent_id')->withTimestamps();
    }

    public function nodeInvoices()
    {
        return $this->belongsToMany(self::class, null, 'parent_id', 'node_id')->withTimestamps();
    }

    public function invoiceContractor()
    {
        return $this->hasOne(InvoiceContractor::class);
    }

    public function correctedInvoice()
    {
        return $this->belongsTo(self::class, 'corrected_invoice_id');
    }

    public function taxes()
    {
        return $this->hasMany(InvoiceTaxReport::class);
    }

    public function taxesRate($vat_rate_id)
    {
        return $this->hasMany('taxes')->where('vat_rate_id', $vat_rate_id);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function payments()
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function cashFlows()
    {
        return $this->hasMany(CashFlow::class);
    }

    public function correctionInvoice()
    {
        return $this->hasMany(self::class, 'corrected_invoice_id');
    }

    /**
     * Get all related invoices. If using for multiple records you should eager load parentInvoices
     * and nodeInvoices relationships first to lower number of database queries.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getInvoicesAttribute()
    {
        return $this->parentInvoices->merge($this->nodeInvoices);
    }

    public function isCollective()
    {
        if ($this->receipts()->count() > 1 || $this->onlineSales()->count() > 1) {
            return true;
        }

        return false;
    }
}
