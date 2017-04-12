<?php

namespace App\Models\Db;

class File extends Model
{
    protected $guarded = [];

    public function ticket()
    {
        return $this->morphedByMany(Ticket::class, 'fileable');
    }

    public function story()
    {
        return $this->morphedByMany(Story::class, 'fileable');
    }

    public function page()
    {
        return $this->morphedByMany(Page::class, 'fileable');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
