<?php

namespace App\Models;

use App\Modules\User\Traits\Active;
use App\Modules\User\Traits\Allowed;
use App\Modules\User\Traits\Fillable;
use App\Modules\User\Traits\Removeable;
use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Mnabialek\LaravelAuthorize\Contracts\Roleable as RoleableContract;
use Mnabialek\LaravelAuthorize\Traits\Roleable;

class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract,
    RoleableContract
{
    use Authenticatable, Authorizable, CanResetPassword;

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
        'role_id',
        'avatar',
        'deleted',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    // relationships

    /**
     * User has single role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * User can be assigned to multiple projects
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }

    /**
     * User can declare multiple availabilities
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function availabilities()
    {
        return $this->hasMany(UserAvailability::class)
            ->orderBy('user_id', 'ASC')
            ->orderBy('day', 'ASC')
            ->orderBy('time_start', 'ASC');
    }

    /**
     * Loading availabilities relationship with date constraints
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function scopeWithAvailabilities(
        $query,
        Carbon $startDate,
        Carbon $endDate
    ) {
        return $query->with([
            'availabilities' => function ($q) use ($startDate, $endDate) {
                $q->where('day', '>=', $startDate->format('Y-m-d'))
                    ->where('day', '<=', $endDate->format('Y-m-d'));
            },
        ]);
    }

    // scopes

    // accessors, mutators

    // functions

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->role ? [$this->role->name] : [];
    }

    /**
     * Verify if user is admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->hasRole(RoleType::ADMIN);
    }
}
