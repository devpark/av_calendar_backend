<?php

namespace App\Models\Db;

use App\Interfaces\CompanyInterface;
use App\Models\Other\RoleType;
use App\Models\Other\UserCompanyStatus;
use App\Modules\User\Traits\Active;
use App\Modules\User\Traits\Allowed;
use App\Modules\User\Traits\Fillable;
use App\Modules\User\Traits\Removeable;
use App\Notifications\ResetPassword as ResetPasswordNotification;
use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Notifications\Notifiable;
use Mnabialek\LaravelAuthorize\Contracts\Roleable as RoleableContract;
use Mnabialek\LaravelAuthorize\Traits\Roleable;

class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract,
    RoleableContract,
    CompanyInterface
{
    use Authenticatable, Authorizable, CanResetPassword, Notifiable;

    use Allowed, Fillable, Removeable, Active;

    use Roleable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'first_name',
        'last_name',
        'avatar',
        'deleted',
    ];

    public function getCompanyId()
    {
        return $this->getSelectedCompanyId();
    }

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * User system role.
     *
     * @var null
     */
    protected $system_role = null;

    /**
     * User selected company id for current request.
     *
     * @var null
     */
    protected $selected_company_id = null;

    /**
     * Selected role for selected company.
     *
     * @var Role|null
     */
    protected $selected_company_role = null;

    // relationships

    /**
     * User can be assigned to multiple projects.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class)
            ->withTimestamps()
            ->withPivot(['user_id', 'project_id', 'role_id']);
    }

    /**
     * User can declare multiple availabilities.
     *
     * @return HasMany
     */
    public function availabilities()
    {
        return $this->hasMany(UserAvailability::class)
            ->orderBy('user_id', 'ASC')
            ->orderBy('day', 'ASC')
            ->orderBy('time_start', 'ASC');
    }

    /**
     * User can be assigned to multiple companies.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'user_company', 'user_id',
            'company_id')->withPivot(['role_id', 'status']);
    }

    /**
     * User can own multiple companies.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function ownedCompanies()
    {
        return $this->companies()
            ->where('role_id', Role::findByName(RoleType::OWNER)->id);
    }

    // scopes

    /**
     * Loading availabilities relationship with date constraints.
     *
     * @param $query
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param $companyId
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function scopeWithAvailabilities(
        $query,
        Carbon $startDate,
        Carbon $endDate,
        $companyId
    ) {
        return $query->with([
            'availabilities' => function ($q) use ($startDate, $endDate, $companyId) {
                $q->companyId((int) $companyId)
                    ->where('day', '>=', $startDate->format('Y-m-d'))
                    ->where('day', '<=', $endDate->format('Y-m-d'));
            },
        ]);
    }

    // accessors, mutators

    // functions

    /**
     * Set user's system role.
     */
    public function setSystemRole()
    {
        if ($this->is_superadmin) {
            $this->system_role = RoleType::SYSTEM_ADMIN;
        } else {
            $this->system_role = RoleType::SYSTEM_USER;
        }
    }

    /**
     * Get role attribute (system role).
     *
     * @return string
     */
    public function getRoleAttribute()
    {
        return $this->system_role;
    }

    /**
     * Set selected company id for user.
     *
     * @param int $companyId
     * @param Role|null $role
     */
    public function setSelectedCompany($companyId, Role $role = null)
    {
        $this->selected_company_id = $companyId;
        $this->selected_company_role = $role;
    }

    /**
     * Get selected company id.
     *
     * @return null|int
     */
    public function getSelectedCompanyId()
    {
        return (int) $this->selected_company_id;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        $roles = [$this->role];

        // we only set any additional role in case user is assigned to selected company with approved
        // status
        if ($this->selectedUserCompany) {
            if ($this->hasCustomApiRoleForRequest()) {
                // if custom role was used, we will use this role
                $roles[] = $this->selected_company_role->name;
            } elseif ($this->selectedUserCompany->role) {
                // otherwise valid company role is assigned to user
                $roles[] = $this->selectedUserCompany->role->name;
            }
        }

        return $roles;
    }

    protected function hasCustomApiRoleForRequest()
    {
        return $this->selected_company_role && collect([
                RoleType::API_USER,
                RoleType::API_COMPANY,
            ])->containsStrict($this->selected_company_role->name);
    }

    /**
     * User has assigned multiple user companies (no matter of status).
     *
     * @return HasMany
     */
    public function userCompanies()
    {
        return $this->hasMany(UserCompany::class, 'user_id');
    }

    /**
     * User has one selected user company at one time (only approved status).
     *
     * @return HasOne
     */
    public function selectedUserCompany()
    {
        return $this->hasOne(UserCompany::class, 'user_id')
            ->inCompany($this)
            ->where('status', UserCompanyStatus::APPROVED);
    }

    /**
     * Verify if user is admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->hasRole(RoleType::ADMIN);
    }

    /**
     * Verify if user is admin.
     *
     * @return bool
     */
    public function isOwner()
    {
        return (bool) in_array(RoleType::OWNER, $this->getRoles());
    }

    /**
     * Verify if user is admin or owner.
     *
     * @return bool
     */
    public function isOwnerOrAdmin()
    {
        return collect($this->getRoles())->intersect([RoleType::OWNER, RoleType::ADMIN])
            ->isNotEmpty();
    }

    /**
     * Verify if user is admin.
     *
     * @return bool
     */
    public function isSystemAdmin()
    {
        return (bool) in_array(RoleType::SYSTEM_ADMIN, $this->getRoles());
    }

    /**
     * Find user by e-mail.
     *
     * @param string $email
     * @param bool $soft
     *
     * @return User|null
     */
    public static function findByEmail($email, $soft = true)
    {
        $query = self::where('email', $email);
        if ($soft) {
            return $query->first();
        }

        return $query->firstOrFail();
    }

    /**
     * Send the password reset notification.
     *
     * @param  string $token
     *
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * User has many different communication channels.
     *
     * @return HasMany
     */
    public function communicationChannels()
    {
        return $this->hasMany(CommunicationChannel::class, 'user_id');
    }

    /**
     * User has many communication channels for himself only.
     *
     * @return HasMany
     */
    public function selfCommunicationChannels()
    {
        return $this->communicationChannels()->whereNull('company_id');
    }

    /**
     * User has many notification types for himself that are active.
     *
     * @return mixed
     */
    public function selfActiveCommunicationChannelTypes()
    {
        return $this->belongsToMany(CommunicationChannelType::class, 'communication_channels',
            'user_id')
            ->where('communication_channels.notifications_enabled', 1)
            ->withPivot('company_id', 'notifications_enabled', 'value')
            ->whereNull('company_id')
            ->wherePivot('notifications_enabled', 1)
            ->wherePivot('value', '<>', '');
    }

    /**
     * Get notification channel types for user (for self only).
     *
     * @return array
     */
    public function getActiveNotificationChannelsAttribute()
    {
        $channels = $this->selfActiveCommunicationChannelTypes->pluck('name')->all();

        // if no e-mail is set, we will add it (e-mail should be always enabled)
        if (! in_array(CommunicationChannelType::EMAIL, $channels)) {
            $channels[] = CommunicationChannelType::EMAIL;
        }

        return $channels;
    }

    /**
     * Get e-mail for self mail notification channel.
     *
     * @return string
     */
    public function routeNotificationForMail()
    {
        $communicationChannel = $this->selfActiveCommunicationChannelTypes()
            ->where('name', CommunicationChannelType::EMAIL)->first();

        if ($communicationChannel && $communicationChannel->pivot->value) {
            return $communicationChannel->pivot->value;
        }

        return $this->email;
    }

    /**
     * Get Slack channel for self Slack notification channel.
     *
     * @return string
     */
    public function routeNotificationForSlack()
    {
        $communicationChannel = $this->selfActiveCommunicationChannelTypes()
            ->where('name', CommunicationChannelType::SLACK)->first();

        return $communicationChannel->pivot->value;
    }

    /**
     * Verify whether user is activated.
     *
     * @return bool
     */
    public function isActivated()
    {
        return (bool) $this->activated;
    }

    /**
     * Activate user account.
     */
    public function activate()
    {
        $this->activated = true;
        $this->save();
    }

    public function files()
    {
        return $this->belongsToMany(File::class);
    }

    /**
     * Get user role ID in project.
     *
     * @param Project $project
     *
     * @return int
     */
    public function getRoleInProject($project)
    {
        return $this->projects->find($project->id)->pivot->role_id;
    }
}
