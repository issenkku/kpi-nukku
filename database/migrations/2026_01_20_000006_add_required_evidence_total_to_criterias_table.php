<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('criterias', function (Blueprint $table) {
            $table->unsignedInteger('required_evidence_total')
                ->nullable()
                ->after('report');
        });
    }

    public function down(): void
    {
        Schema::table('criterias', function (Blueprint $table) {
            $table->dropColumn('required_evidence_total');
        });
    }
};
