<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExportGuestTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_cannot_export_indicators(): void
    {
        $this->get('/export/indicators')->assertStatus(302);
    }
}

