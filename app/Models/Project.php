<?php

namespace App\Models;

class Project extends Model
{
    /**
     * Project can be assigned to multiple users
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
