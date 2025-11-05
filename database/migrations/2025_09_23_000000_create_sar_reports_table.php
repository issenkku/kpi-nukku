<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SAR Reports (รายงานการประเมิน SAR)
        Schema::create('sar_reports', function (Blueprint $table) {
            $table->id();

            $table->integer('year'); // ปีการประเมิน
            $table->foreignId('standard_id')->constrained('standards')->cascadeOnDelete();
            $table->foreignId('indicator_id')->constrained('indicators')->cascadeOnDelete();
            $table->foreignId('criteria_id')->constrained('criterias')->cascadeOnDelete();

            // ส่วนที่ 1, 2, 4 (Rich Text)
            $table->longText('section1')->nullable();
            $table->longText('section2')->nullable();
            $table->longText('section4')->nullable();

            // ผลการดำเนินงาน
            $table->text('performance_result')->nullable();
            $table->text('performance_report')->nullable();
            $table->decimal('self_score', 5, 2)->nullable();
            $table->text('comment')->nullable();

            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });

        // Pivot: SAR Report ↔ Evidence
        Schema::create('sar_report_evidence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sar_report_id')->constrained('sar_reports')->cascadeOnDelete();
            $table->foreignId('evidence_id')->constrained('evidence')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sar_report_evidence');
        Schema::dropIfExists('sar_reports');
    }
};
