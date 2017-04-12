<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $dates = ['deleted_at'];

    /**
     * Project can be assigned to multiple users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps()
            ->withPivot(['user_id', 'project_id', 'role_id']);
    }

    /**
     * Project is assigned to company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function isOpened()
    {
        return is_null($this->closed_at) ? true : false;
    }

    /**
     * Check if user can access this project.
     *
     * @param User $user
     *
     * @return bool
     */
    public function isAccessible(User $user)
    {
        // Check if this project belong to selected company
        if ($this->company_id != $user->getSelectedCompanyId()) {
            return false;
        }
        if ($user->isAdmin() || $user->isOwner()) {
            return true;
        }
        // Check if user is attached to this project
        if ($this->users()->find($user->id)) {
            return true;
        }

        return false;
    }
}
