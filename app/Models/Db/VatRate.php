<?php

namespace App\Models\Db;

class VatRate extends Model
{
    public function findByName($name)
    {
        return  $this->where('name', trim($name))->firstOrFail();
    }
}
