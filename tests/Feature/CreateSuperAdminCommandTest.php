<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateSuperAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_the_initial_super_administrator(): void
    {
        $this->artisan('app:create-super-admin', [
            '--email' => 'admin@example.test',
            '--first-name' => 'System',
            '--last-name' => 'Administrator',
        ])
            ->expectsQuestion('Password (minimum 12 characters)', 'a-strong-password')
            ->expectsQuestion('Confirm password', 'a-strong-password')
            ->assertSuccessful();

        $user = User::where('email', 'admin@example.test')->firstOrFail();

        $this->assertTrue($user->status);
        $this->assertTrue($user->hasRole('super_admin'));
    }
}
