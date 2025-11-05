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
        $u = User::factory()->create();
        $u->assignRole('super_admin');

        // Use a page that renders the full layout
        $resp = $this->actingAs($u, 'sanctum')->get('/users');
        $resp->assertOk();

        $resp->assertSee('Dashboard', false);
        $resp->assertSee('จัดการตัวชี้วัด', false);
        $resp->assertSee('ตั้งค่าระบบ', false);
        $resp->assertSee('จัดการผู้ใช้งาน', false);
        $resp->assertSee('จัดการหลักฐาน', false);
    }

    #[Test]
    public function system_admin_sees_settings_menu(): void
    {
        $u = User::factory()->create();
        $u->assignRole('system_admin');
        $resp = $this->actingAs($u, 'sanctum')->get('/users');
        $resp->assertOk();

        $resp->assertSee('ตั้งค่าระบบ', false);
        $resp->assertSee('จัดการผู้ใช้งาน', false);
        $resp->assertSee('จัดการหน่วยงาน', false);
        $resp->assertSee('จัดการหลักฐาน', false);
    }

    #[Test]
    public function qa_admin_sees_review_kpi_but_no_settings_menu(): void
    {
        $u = User::factory()->create();
        $u->assignRole('qa_admin');
        $resp = $this->actingAs($u, 'sanctum')->get('/indicator');
        $resp->assertOk();

        $resp->assertSee('จัดการตัวชี้วัด', false);
        $resp->assertSee('ตรวจสอบตัวชี้วัด', false);
        $resp->assertDontSee('ตั้งค่าระบบ', false);
    }

    #[Test]
    public function administration_admin_cannot_see_settings_menu(): void
    {
        $u = User::factory()->create();
        $u->assignRole('administration_admin');
        $resp = $this->actingAs($u, 'sanctum')->get('/indicator');
        $resp->assertOk();

        $resp->assertSee('จัดการตัวชี้วัด', false);
        $resp->assertDontSee('ตั้งค่าระบบ', false);
    }

    #[Test]
    public function user_sees_user_dashboard_and_my_evidences_only(): void
    {
        $u = User::factory()->create();
        $u->assignRole('user');
        // evidences is allowed for user
        $resp = $this->actingAs($u, 'sanctum')->get('/evidences');
        $resp->assertOk();

        $resp->assertSee('Dashboard ผู้ใช้งาน', false);
        $resp->assertSee('หลักฐานของฉัน', false);
        $resp->assertDontSee('จัดการผู้ใช้งาน', false);
        $resp->assertDontSee('ตั้งค่าระบบ', false);
        $resp->assertDontSee('จัดการตัวชี้วัด', false);
    }
}

