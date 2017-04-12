<?php

namespace App\Models\Db;

class UserCompany extends Model
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'user_company';

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'user_id',
        'company_id',
        'role_id',
        'status',
    ];

    /**
     * Single record belongs to single user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Single record belongs to single company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Single record belongs to single role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
