<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Allow larger HTML payloads (e.g. pasted images as base64).
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE evidence MODIFY detail MEDIUMTEXT NULL');
        } elseif ($driver === 'pgsql') {
            // Postgres TEXT is already unlimited; keep as TEXT to avoid syntax errors.
            DB::statement('ALTER TABLE evidence ALTER COLUMN detail TYPE TEXT');
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE evidence MODIFY detail TEXT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE evidence ALTER COLUMN detail TYPE TEXT');
        }
    }
};
