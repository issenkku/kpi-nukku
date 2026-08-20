<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DeliverySecurityTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function development_pdf_routes_are_not_exposed(): void
    {
        $this->get('/test')->assertNotFound();
        $this->get('/test-html')->assertNotFound();
    }

    #[Test]
    public function guest_cannot_trigger_indicator_notifications(): void
    {
        $this->post('/indicators/1/notify')->assertRedirect(route('login'));
    }
}
