<?php

namespace App\Models\Db;

class Role extends Model
{
    /**
     * {inheritdoc}.
     */
    public $timestamps = false;

    /**
     * {inheritdoc}.
     */
    protected $fillable = ['name'];

    /**
     * Get role by name.
     *
     * @param string $name
     * @param bool $soft
     *
     * @return mixed
     */
    public static function findByName($name, $soft = false)
    {
        $query = self::where('name', $name);

        return $soft ? $query->first() : $query->firstOrFail();
    }

    // relationships
    /**
     * Role can be assigned to multiple companies.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }

    public function files()
    {
        return $this->belongsToMany(File::class)->withTimestamps();
    }
}
