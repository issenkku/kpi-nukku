<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NavbarVisibilityByRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    #[Test]
    public function super_admin_sees_admin_menus(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $response = $this->actingAs($user, 'sanctum')->get('/users');

        $response->assertOk();
        $response->assertSeeText('หน้าหลัก');
        $response->assertSeeText('จัดการตัวบ่งชี้');
        $response->assertSeeText('ตั้งค่าระบบ');
        $response->assertSeeText('จัดการผู้ใช้งาน');
        $response->assertSeeText('จัดการหลักฐาน');
    }

    #[Test]
    public function system_admin_sees_settings_menu(): void
    {
        $user = User::factory()->create();
        $user->assignRole('system_admin');

        $response = $this->actingAs($user, 'sanctum')->get('/users');

        $response->assertOk();
        $response->assertSeeText('ตั้งค่าระบบ');
        $response->assertSeeText('จัดการผู้ใช้งาน');
        $response->assertSeeText('จัดการหน่วยงาน');
        $response->assertSeeText('จัดการหลักฐาน');
    }

    #[Test]
    public function qa_admin_sees_review_kpi_but_no_settings_menu(): void
    {
        $user = User::factory()->create();
        $user->assignRole('qa_admin');

        $response = $this->actingAs($user, 'sanctum')->get('/indicator');

        $response->assertOk();
        $response->assertSeeText('จัดการตัวบ่งชี้');
        $response->assertSeeText('ตรวจสอบตัวบ่งชี้');
        $response->assertDontSeeText('ตั้งค่าระบบ');
    }

    #[Test]
    public function administration_admin_cannot_see_settings_menu(): void
    {
        $user = User::factory()->create();
        $user->assignRole('administration_admin');

        $response = $this->actingAs($user, 'sanctum')->get('/indicator');

        $response->assertOk();
        $response->assertSeeText('จัดการตัวบ่งชี้');
        $response->assertDontSeeText('ตั้งค่าระบบ');
    }

    #[Test]
    public function user_sees_user_dashboard_and_my_evidences_only(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user, 'sanctum')->get('/evidences');

        $response->assertOk();
        $response->assertSeeText('หน้าหลัก');
        $response->assertSeeText('หลักฐานของฉัน');
        $response->assertDontSeeText('จัดการผู้ใช้งาน');
        $response->assertDontSeeText('ตั้งค่าระบบ');
        $response->assertDontSeeText('จัดการตัวบ่งชี้');
    }
}
