<?php

namespace App\Modules\User\Database\Seeds;

use App\Models\Role;
use App\Models\RoleType;
use Illuminate\Database\Seeder;

class RoleDatabaseSeeder extends Seeder
{
    /**
     * Run seeds for User
     */
    public function run()
    {
        if (!Role::count()) {
            foreach (RoleType::all() as $type) {
                Role::create(['name' => $type]);
                $this->command->info("Role '{$type}' has been created");
            }
        }
    }
}
