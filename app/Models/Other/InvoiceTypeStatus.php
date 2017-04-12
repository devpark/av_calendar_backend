<?php

namespace App\Models\Other;

class InvoiceTypeStatus
{
    /**
     * Regular invoice type.
     */
    const VAT = 'vat';

    /**
     * Correction invoice type.
     */
    const CORRECTION = 'correction';

    /**
     * Get all available statuses for invoice type.
     *
     * @return array
     */
    public static function all()
    {
        return [
            self::VAT,
            self::CORRECTION,
        ];
    }
}
