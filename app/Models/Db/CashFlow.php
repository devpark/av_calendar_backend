<?php

namespace App\Models\Db;

use App\Modules\SaleReport\Traits\PriceNormalize;

class CashFlow extends Model
{
    use PriceNormalize;

    const DIRECTION_INITIAL = 'initial';
    const DIRECTION_IN = 'in';
    const DIRECTION_OUT = 'out';
    const DIRECTION_FINAL = 'final';

    protected $fillable = [
        'company_id',
        'user_id',
        'receipt_id',
        'invoice_id',
        'amount',
        'direction',
        'description',
        'flow_date',
        'cashless',
    ];

    public static function directions()
    {
        return [
            static::DIRECTION_INITIAL,
            static::DIRECTION_IN,
            static::DIRECTION_OUT,
            static::DIRECTION_FINAL,
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receipt()
    {
        return $this->belongsTo(Receipt::class, 'receipt_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
