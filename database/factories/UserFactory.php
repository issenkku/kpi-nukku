<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();
        
        return [
            // ข้อมูลส่วนตัว
            'title'             => fake()->randomElement(['นาย', 'นาง', 'นางสาว', 'ดร.', 'ผศ.ดร.', 'รศ.ดร.', 'ศ.ดร.']),
            'first_name'        => $firstName,
            'last_name'         => $lastName,
            
            // ข้อมูลการทำงาน
            'positype'          => fake()->randomElement(['ข้าราชการ', 'พนักงานมหาวิทยาลัย', 'ลูกจ้าง', 'พนักงานประจำตามสัญญา']),
            'workline'          => fake()->randomElement(['สายวิชาการ', 'สายสนับสนุน']),
            'posi'              => fake()->jobTitle(),
            'level'             => fake()->randomElement(['ปฏิบัติการ', 'ชำนาญการ', 'ชำนาญการพิเศษ', 'เชี่ยวชาญ']),
            
            // ข้อมูลติดต่อ
            'email'             => fake()->unique()->safeEmail(),
            'phone'             => fake()->numerify('0########'),
            
            // ระบบ
            'email_verified_at' => now(),
            'password'          => static::$password ??= Hash::make('password'),
            'remember_token'    => Str::random(10),
            'status'            => true,
            'department_id'     => Department::factory(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user should be inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => false,
        ]);
    }

    /**
     * Indicate that the user should have a specific department.
     */
    public function forDepartment(Department|int $department): static
    {
        return $this->state(fn(array $attributes) => [
            'department_id' => $department instanceof Department ? $department->id : $department,
        ]);
    }

    /**
     * Indicate that the user should be academic staff.
     */
    public function academicStaff(): static
    {
        return $this->state(fn(array $attributes) => [
            'positype' => 'ข้าราชการ',
            'workline' => 'สายวิชาการ',
            'title' => fake()->randomElement(['ผศ.ดร.', 'รศ.ดร.', 'ศ.ดร.', 'ดร.']),
        ]);
    }

    /**
     * Indicate that the user should be support staff.
     */
    public function supportStaff(): static
    {
        return $this->state(fn(array $attributes) => [
            'positype' => fake()->randomElement(['ข้าราชการ', 'พนักงานมหาวิทยาลัย', 'ลูกจ้าง']),
            'workline' => 'สายสนับสนุน',
            'title' => fake()->randomElement(['นาย', 'นาง', 'นางสาว']),
        ]);
    }

    /**
     * Indicate that the user should have a specific password.
     */
    public function withPassword(string $password): static
    {
        return $this->state(fn(array $attributes) => [
            'password' => Hash::make($password),
        ]);
    }

    /**
     * Create a user without department.
     */
    public function withoutDepartment(): static
    {
        return $this->state(fn(array $attributes) => [
            'department_id' => null,
        ]);
    }
}
