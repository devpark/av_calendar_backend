<?php

namespace App\Models\Db;

class PaymentMethod extends Model
{
    const CASH = 'gotowka';
    const CARD = 'karta';
    const CASH_CARD = 'gotowka_karta';

    protected $fillable = [
        'name',
        'slug',
        'invoice_restrict',
    ];

    /**
     * Get payment method by slug.
     *
     * @param string $slug
     * @param bool $soft
     *
     * @return mixed
     */
    public static function findBySlug($slug, $soft = false)
    {
        $query = self::where('slug', $slug);

        return $soft ? $query->first() : $query->firstOrFail();
    }

    public static function paymentInAdvance($payment_method_id)
    {
        return self::where('id', $payment_method_id)
            ->where(function ($query) {
                $query->where('slug', self::CASH)
                   ->orWhere('slug', self::CARD)
                   ->orWhere('slug', self::CASH_CARD);
            })->count() > 0;
    }
}
