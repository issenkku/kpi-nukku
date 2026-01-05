<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'first_name' => 'System',
                'last_name' => 'Admin',
                'password' => '$2y$12$DIpJl8PXcr9YydLoficaCuBKXDY2C5DasyoWWZjWv8O0wBXIeXi.i',
                'phone' => '0800000002',
                'status' => true,
                'email' => 'system@example.com',
                'department_id' => 1,
            ],
            [
                'first_name' => 'Administration',
                'last_name' => 'Admin',
                'password' => '$2y$12$Q9eJbM6/bG2BhZGCukQ7rOKCrDhXL1ysbyHzR9LuiopgA3YDju/F2',
                'phone' => '0800000004',
                'status' => true,
                'email' => 'admin@example.com',
                'department_id' => 3,
            ],
            [
                'first_name' => 'QA',
                'last_name' => 'Admin',
                'password' => '$2y$12$39n/FwYxx1I0io32gSMBlOuwkKDXw1.6FZw7ncj.SnUomQe73jqPm',
                'phone' => '0800000003',
                'status' => true,
                'email' => 'qa@example.com',
                'department_id' => 2,
            ],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'password' => '$2y$12$ZHMUmt8XnB9mjaIVBIohhutFIYCRm/8B2rsICOEPE6oBofB0Tznz6',
                'phone' => '0800000001',
                'status' => true,
                'email' => 'super@example.com',
                'department_id' => 1,
            ],
            [
                'first_name' => 'Test',
                'last_name' => 'User',
                'password' => '$2y$12$LIVUpFK8uh3Gwfl.FdN4.OGqh6.M6D4J0VQnrgFXU8vLK6KO3fKom',
                'phone' => '0800000005',
                'status' => true,
                'email' => 'user@example.com',
                'department_id' => 4,
            ],
        ];

        // Upsert to avoid duplicate email errors when seeding repeatedly.
        DB::table('users')->upsert(
            $users,
            ['email'],
            ['first_name', 'last_name', 'password', 'phone', 'status', 'department_id']
        );

        // Assign roles using Spatie Permission
        $map = [
            'super@example.com' => 'super_admin',
            'system@example.com' => 'system_admin',
            'qa@example.com' => 'qa_admin',
            'admin@example.com' => 'administration_admin',
            'user@example.com' => 'user',
        ];
        foreach ($map as $email => $role) {
            $u = User::where('email', $email)->first();
            if ($u && ! $u->hasRole($role)) {
                $u->assignRole($role);
            }
        }
    }
}
