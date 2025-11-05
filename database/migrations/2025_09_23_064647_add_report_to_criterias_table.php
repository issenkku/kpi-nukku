<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('criterias', function (Blueprint $table) {
            $table->text('report')->nullable()->after('status'); 
            // ✅ เก็บรายงานผลการดำเนินงาน
        });
    }

    public function down(): void
    {
        Schema::table('criterias', function (Blueprint $table) {
            $table->dropColumn('report');
        });
    }
};
