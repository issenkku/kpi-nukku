<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesTableSeeder extends Seeder
{
    /**
     * Seed roles idempotently (safe to rerun).
     */
    public function run(): void
    {
        // Ensure permission/role cache is cleared before seeding
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = [
            'super_admin',
            'system_admin',
            'qa_admin',
            'administration_admin',
            'user',
        ];

        foreach ($roles as $name) {
            // Creates the role if it doesn't exist; otherwise no-op
            Role::findOrCreate($name, 'web');
        }
    }
}

