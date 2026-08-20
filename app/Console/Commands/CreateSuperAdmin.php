<?php

namespace App\Console\Commands;

use App\Models\Department;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class CreateSuperAdmin extends Command
{
    protected $signature = 'app:create-super-admin
        {--email= : Administrator email}
        {--first-name= : First name}
        {--last-name= : Last name}
        {--department=System Administration : Department name}';

    protected $description = 'Create or update the initial super administrator account';

    public function handle(): int
    {
        $email = (string) ($this->option('email') ?: $this->ask('Email'));
        $firstName = (string) ($this->option('first-name') ?: $this->ask('First name'));
        $lastName = (string) ($this->option('last-name') ?: $this->ask('Last name'));
        $departmentName = (string) $this->option('department');
        $password = (string) $this->secret('Password (minimum 12 characters)');
        $passwordConfirmation = (string) $this->secret('Confirm password');

        $validation = Validator::make([
            'email' => $email,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
        ], [
            'email' => ['required', 'email'],
            'firstName' => ['required', 'string', 'max:100'],
            'lastName' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        if ($validation->fails()) {
            foreach ($validation->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $department = Department::firstOrCreate(['name' => $departmentName]);
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'password' => Hash::make($password),
                'status' => true,
                'department_id' => $department->id,
            ],
        );

        Role::findOrCreate('super_admin', 'web');
        $user->syncRoles(['super_admin']);

        $this->info("Super administrator {$email} is ready.");

        return self::SUCCESS;
    }
}
