<?php

namespace App\Modules\User\Database\Seeds;

use App\Models\Role;
use App\Models\RoleType;
use App\Models\User;
use Exception;
use Illuminate\Database\Seeder;

class UserDatabaseSeeder extends Seeder
{
    /**
     * Run seeds for User
     */
    public function run()
    {
        $this->call(RoleDatabaseSeeder::class);
        
        $roles = Role::all();

        if (!User::count()) {
            $adminEmail = env('ADMIN_EMAIL');
            $adminPassword = env('ADMIN_PASSWORD');

            if (empty($adminEmail) || empty($adminPassword)) {
                throw new Exception('Please fill Admin user data in .env file!');
            }

            // creating admin user
            $user = new User();
            $user->email = $adminEmail;
            $user->password = $adminPassword;

            $user->role_id =
                $roles->where('name', RoleType::ADMIN)->first()->id;
            $user->save();
            $this->command->info("Created user for e-mail {$user->email} with role '" .
                RoleType::ADMIN . "'");
        }

        // here extra users table - we will create new ones from this table if
        // they don't exist (but we won't delete if any exists and are not
        // in this array present)
        $users = config('app_users', []);

        foreach ($users as $user) {
            $u = User::where('email', $user['email'])->first();
            if ($u) {
                $this->command->comment("User for email {$u->email} already exists, skipping");
                continue;
            }

            $u = new User();
            $u->email = $user['email'];
            $u->password = $user['password'];
            $u->first_name = $user['first_name'];
            $u->last_name = $user['last_name'];
            $role = $roles->where('name', $user['role'])->first();
            if (!$role) {
                throw new Exception("Invalid role '{$user['role']}' given for user {$u->email}");
            }

            $u->role_id = $role->id;

            $u->save();
            $this->command->info("Created user for e-mail {$u->email} with role '{$user['role']}'");
        }
    }
}
