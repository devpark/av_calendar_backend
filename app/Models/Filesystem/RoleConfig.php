<?php

namespace App\Models\Filesystem;

class RoleConfig
{
    /**
     * Function previously used to add additional roles (owner, admin),
     * currently not used. Adding roles to files is not definitively finished yet.
     *
     * @return array
     */
    private function extraRoles()
    {
        return [
            Role::findByName(RoleType::OWNER)->id,
            Role::findByName(RoleType::ADMIN)->id,
        ];
    }

    /**
     * @param array $roles
     *
     * @return array
     */
    public function get($roles)
    {
        return array_unique($roles);
    }
}
