<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Category;
use App\Models\Criteria;
use App\Models\Indicator;
use App\Models\Standard;
use App\Models\User;
use App\Models\Variable;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DashboardKpiAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    #[Test]
    public function assigned_user_can_only_update_models_belonging_to_the_assigned_indicator(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        [$indicator, $criteria, $variable] = $this->makeIndicator('AUTH-001');
        Assignment::create(['indicator_id' => $indicator->id, 'collector' => $user->id]);

        $response = $this->actingAs($user)->put(route('dashboardkpi.user.saveVariables', $indicator), [
            'variables' => [$variable->id => '7.5'],
            'criterias' => [
                $criteria->id => [
                    'evidence_comment' => '<b>accepted</b><script>alert(1)</script>',
                ],
            ],
            'status' => 2,
        ]);

        $response->assertRedirect(route('dashboardkpi.user.show', $indicator));
        $this->assertDatabaseHas('variables', ['id' => $variable->id, 'value' => 7.5]);
        $this->assertSame('<b>accepted</b>', $criteria->fresh()->evidence_comment);
    }

    #[Test]
    public function unassigned_user_cannot_write_to_an_indicator(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        [$indicator, $criteria, $variable] = $this->makeIndicator('AUTH-002');

        $this->actingAs($user)
            ->put(route('dashboardkpi.user.saveVariables', $indicator), [
                'variables' => [$variable->id => 9],
                'criterias' => [$criteria->id => ['evidence_comment' => 'blocked']],
            ])
            ->assertForbidden();

        $this->assertNull($variable->fresh()->value);
        $this->assertNull($criteria->fresh()->evidence_comment);
    }

    #[Test]
    public function assigned_user_cannot_update_a_variable_from_another_indicator(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        [$indicator] = $this->makeIndicator('AUTH-003');
        [, , $otherVariable] = $this->makeIndicator('AUTH-004');
        Assignment::create(['indicator_id' => $indicator->id, 'collector' => $user->id]);

        $this->actingAs($user)
            ->put(route('dashboardkpi.user.saveVariables', $indicator), [
                'variables' => [$otherVariable->id => 10],
            ])
            ->assertNotFound();

        $this->assertNull($otherVariable->fresh()->value);
    }

    private function makeIndicator(string $code): array
    {
        $standard = Standard::create(['name' => 'Standard '.$code]);
        $category = Category::create([
            'name' => 'Category '.$code,
            'standard_id' => $standard->id,
            'max_score' => 10,
        ]);
        $indicator = Indicator::create([
            'name' => 'Indicator '.$code,
            'code' => $code,
            'year' => 2026,
            'max_score' => 10,
            'score_acc' => 0,
            'status' => 1,
            'deadline' => now()->addDay()->toDateString(),
            'categorie_id' => $category->id,
        ]);
        $criteria = Criteria::create([
            'name' => 'Criteria '.$code,
            'sequence' => 1,
            'indicator_id' => $indicator->id,
        ]);
        $variable = Variable::create([
            'variable_name' => 'value_'.str_replace('-', '_', strtolower($code)),
            'label_name' => 'Value',
            'type' => 'input',
            'value' => null,
            'indicator_id' => $indicator->id,
        ]);

        return [$indicator, $criteria, $variable];
    }
}
