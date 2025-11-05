<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Realign the indicators.id sequence with the current MAX(id)
        DB::statement(
            "SELECT setval(pg_get_serial_sequence('indicators','id'), COALESCE((SELECT MAX(id) FROM indicators), 0) + 1, false)"
        );
    }

    public function down(): void
    {
        // No-op: sequence alignment does not need to be reverted
    }
};

