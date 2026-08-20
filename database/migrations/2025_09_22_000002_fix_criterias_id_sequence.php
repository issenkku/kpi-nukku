<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        // Realign the criterias.id sequence with the current MAX(id)
        DB::statement(
            "SELECT setval(pg_get_serial_sequence('criterias','id'), COALESCE((SELECT MAX(id) FROM criterias), 0) + 1, false)"
        );
    }

    public function down(): void
    {
        // No-op: sequence alignment does not need to be reverted
    }
};
