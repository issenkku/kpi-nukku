<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class FileExportTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_export_excel()
    {
       $user = User::factory()->create();
$user->assignRole('super_admin'); 

        $response = $this->actingAs($user)->get('/export/indicators');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
