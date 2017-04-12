<?php

namespace App\Models\Db;

class Ticket extends Model
{
    public function files()
    {
        return $this->morphToMany(File::class, 'fileable');
    }
}
