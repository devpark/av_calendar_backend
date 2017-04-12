<?php

namespace App\Modules\User\Traits;

use App\Models\Model;
use App\Models\User;

trait Allowed
{
    /**
     * Choose only users that are allowed to be displayed for given user (or
     * current user if none user given).
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param Model|int|null $user
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeAllowed($query, $user = null)
    {
        // get user by id or use object - if not passed any, we use current
        // user
        if (! $user) {
            $user = auth()->user();
        } elseif (! $user instanceof Model) {
            $user = self::find($user);
        }
/** @var User $user */

        // user has not been found or no company selected - return no results
        if (! $user || ! $user->getSelectedCompanyId()) {
            return $query->whereRaw('1 = 0');
        }

        // we always choose users from currently selected company only
        $query->whereHas('companies', function ($q) use ($user) {
            $q->where('companies.id', $user->getSelectedCompanyId());
        });

        // for admins and owners we don't limit results further
        if ($user->isAdmin() || $user->isOwner() || $user->isSystemAdmin()) {
            return $query;
        }

        // for others we will choose only users assigned to same projects
        return $query->where(function ($q) use ($user) {
            $q->where('id', $user->id)
                ->orWhereHas('projects', function ($q) use ($user) {
                    $q->where('company_id', $user->getSelectedCompanyId())
                        ->whereHas('users', function ($q) use ($user) {
                            $q->where('project_user.user_id', $user->id);
                        });
                });
        });
    }
}
