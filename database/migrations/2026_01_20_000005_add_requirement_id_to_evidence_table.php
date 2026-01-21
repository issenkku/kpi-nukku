<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evidence', function (Blueprint $table) {
            $table->foreignId('criteria_evidence_requirement_id')
                ->nullable()
                ->constrained('criteria_evidence_requirements')
                ->nullOnDelete()
                ->after('criteria_id');
        });
    }

    public function down(): void
    {
        Schema::table('evidence', function (Blueprint $table) {
            $table->dropConstrainedForeignId('criteria_evidence_requirement_id');
        });
    }
};
