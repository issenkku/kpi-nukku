<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('criteria_evidence_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('criteria_id')
                ->constrained('criterias')
                ->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('required_count')->default(1);
            $table->unsignedInteger('sequence')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criteria_evidence_requirements');
    }
};
