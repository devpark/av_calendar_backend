<?php

namespace App\Models\Db;

class Page extends Model
{
    public function files()
    {
        return $this->morphToMany(File::class, 'fileable');
    }
}
