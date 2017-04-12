<?php

namespace App\Models\Db;

class CommunicationChannelType extends Model
{
    const EMAIL = 'mail';
    const SLACK = 'slack';
    const SKYPE = 'skype';
    const HIPCHAT = 'hipchat';

    /**
     * {inheritdoc}.
     */
    protected $fillable = ['name', 'notifications_enabled'];

    /**
     * Find communication channel type by name.
     *
     * @param string $name
     *
     * @return CommunicationChannelType|null
     */
    public static function findByName($name)
    {
        return self::where('name', $name)->first();
    }
}
