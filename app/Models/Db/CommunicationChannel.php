<?php

namespace App\Models\Db;

class CommunicationChannel extends Model
{
    /**
     * {inheritdoc}.
     */
    protected $fillable = [
        'company_id',
        'project_id',
        'communication_channel_type_id',
        'notifications_enabled',
        'value',
    ];
}
