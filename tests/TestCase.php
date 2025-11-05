<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;

abstract class TestCase extends BaseTestCase
{
    use WithFaker;
  protected function setUp(): void
{
    parent::setUp();

    // ให้ role ถูกสร้างทุกครั้ง
    foreach (['super_admin', 'system_admin', 'qa_admin', 'administration_admin', 'user'] as $role) {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
    }
}

}
