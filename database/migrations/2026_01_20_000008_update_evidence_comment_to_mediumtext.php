<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'pgsql') {
            // PostgreSQL: TEXT is unbounded
            DB::statement('ALTER TABLE criterias ALTER COLUMN evidence_comment TYPE TEXT');
        } elseif ($driver === 'mysql') {
            DB::statement('ALTER TABLE criterias MODIFY evidence_comment MEDIUMTEXT NULL');
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE criterias ALTER COLUMN evidence_comment TYPE TEXT');
        } elseif ($driver === 'mysql') {
            DB::statement('ALTER TABLE criterias MODIFY evidence_comment TEXT NULL');
        }
    }
};
