<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email' => 'super@example.com',
                'phone' => '0800000001',
                'status' => true,
                'department_id' => 1,
                // 'remember_token' => Str::random(10),
                'role' => 'super_admin',
            ],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'email' => 'system@example.com',
                'phone' => '0800000002',
                'status' => true,
                'department_id' => 1,
                // 'remember_token' => Str::random(10),
                'role' => 'system_admin',
            ],
            [
                'name' => 'QA Admin',
                // 'password' => Hash::make('password'),
                'email' => 'qa@example.com',
                'phone' => '0800000003',
                'status' => true,
                'department_id' => 2,
                // 'remember_token' => Str::random(10),
                'role' => 'qa_admin',
            ],
            [
                'name' => 'Administration Admin',
                'password' => Hash::make('password'),
                'email' => 'admin@example.com',
                'phone' => '0800000004',
                'status' => true,
                'department_id' => 3,
                // 'remember_token' => Str::random(10),
                'role' => 'administration_admin',
            ],
            [
                'name' => 'User',
                'password' => Hash::make('password'),
                'email' => 'user@example.com',
                'phone' => '0800000005',
                'status' => true,
                'department_id' => 4,
                // 'remember_token' => Str::random(10),
                'role' => 'user',
            ],
        ];

        // foreach ($users as $userData) {
        //     $role = $userData['role'];
        //     unset($userData['role']); // ลบ key 'role' ก่อน insert

        //     $user = User::updateOrCreate(
        //         ['email' => $userData['email']], // ค้นหาตาม email
        //         $userData // update หรือ create ด้วยข้อมูลนี้
        //     );
            
        //     // ลบ role เก่าก่อน (ถ้ามี) แล้วกำหนด role ใหม่
        //     $user->syncRoles([$role]);
            
        //     echo "Created user: {$userData['email']} with role: {$role}\n";
        // }
    }
}