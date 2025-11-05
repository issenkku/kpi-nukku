<?php

namespace Tests\Unit;

use App\Models\Indicator;
use App\Models\User;
use App\Policies\IndicatorPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class IndicatorPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function super_admin_can_create_indicator(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');
        $policy = new IndicatorPolicy();
        $this->assertTrue($policy->create($user));
    }

    #[Test]
    public function normal_user_cannot_create_indicator(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        $policy = new IndicatorPolicy();
        $this->assertFalse($policy->create($user));
    }
}

