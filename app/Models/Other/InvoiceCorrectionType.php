<?php

namespace App\Models\Other;

class InvoiceCorrectionType
{
    /**
     * Tax correction type.
     */
    const TAX = 'tax';

    /**
     * Price correction type.
     */
    const PRICE = 'price';

    /**
     * Quantity correction type.
     */
    const QUANTITY = 'quantity';

    public static function all()
    {
        return [
            self::TAX => 'Vat',
            self::PRICE => 'Cena',
            self::QUANTITY => 'Ilość',
        ];
    }
}
