<?php

namespace App\Models\Other;

class PaymentMethodType
{
    /**
     * Bank transfer payment method.
     */
    const BANK_TRANSFER = 'przelew';

    /**
     * Cash payment method.
     */
    const CASH = 'gotowka';

    /**
     * Debit card payment method.
     */
    const DEBIT_CARD = 'karta';

    /**
     * Prepaid payment method.
     */
    const PREPAID = 'przedplata';

    /**
     * Other payment method.
     */
    const OTHER = 'inne';

    /**
     * Mix payment method (cash and card).
     */
    const CASH_CARD = 'gotowka_karta';

    /**
     * Get all available payment method types.
     *
     * @return array
     */
    public static function all()
    {
        return [
            self::BANK_TRANSFER,
            self::CASH,
            self::DEBIT_CARD,
            self::PREPAID,
            self::OTHER,
            self::CASH_CARD,
        ];
    }
}
