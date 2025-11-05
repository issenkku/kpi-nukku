<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sar_reports', function (Blueprint $table) {
            // Add new column
            $table->string('title')->nullable();

            // Drop unwanted columns
            $table->dropColumn([
                'performance_result',
                'performance_report',
                'self_score',
                'comment',
                'submitted_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('sar_reports', function (Blueprint $table) {
            // Re-add dropped columns
            $table->text('performance_result')->nullable();
            $table->text('performance_report')->nullable();
            $table->decimal('self_score', 5, 2)->nullable();
            $table->text('comment')->nullable();
            $table->timestamp('submitted_at')->nullable();

            // Remove newly added column
            $table->dropColumn('title');
        });
    }
};
