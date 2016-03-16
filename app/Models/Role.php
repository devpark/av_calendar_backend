<?php

namespace App\Models;

class Role extends Model
{
    /**
     * {inheritdoc}
     */
    public $timestamps = false;

    /**
     * {inheritdoc}
     */
    protected $fillable = ['name'];
}
