<?php

namespace App\Models\Db;

class Story extends Model
{
    public function files()
    {
        return $this->morphToMany(File::class, 'fileable');
    }
}
