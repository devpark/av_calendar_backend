<?php

namespace App\Models\Db;

class InvoiceType extends Model
{
    public static function findBySlug($slug)
    {
        return self::where('slug', trim($slug))->firstOrFail();
    }
}
