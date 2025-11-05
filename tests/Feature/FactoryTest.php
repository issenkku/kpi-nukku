<?php

namespace Tests\Feature;

use App\Models\Indicator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FactoryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function indicator_factory_creates_valid_record(): void
    {
        $indicator = Indicator::factory()->create();

        $this->assertNotNull($indicator->deadline);
        $this->assertNotNull($indicator->categorie_id);
        $this->assertDatabaseHas('indicators', [
            'id' => $indicator->id,
            'code' => $indicator->code,
        ]);
    }
}

