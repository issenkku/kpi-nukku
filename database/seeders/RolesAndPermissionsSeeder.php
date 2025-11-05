<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure fresh cache state
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Seed roles first, then permissions + role assignments
        $this->call([
            RolesTableSeeder::class,
            PermissionsTableSeeder::class,
        ]);

        // Clear again after changes
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}

