<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id(); // PK, Auto Increment
            $table->string('title', 255)->nullable(); // ชื่อการแจ้งเตือน
            // เก็บวันที่แจ้งเตือนจริง 2 รอบ
            $table->date('notify_date1')->nullable()->comment('วันที่แจ้งเตือนรอบที่ 1');
            $table->date('notify_date2')->nullable()->comment('วันที่แจ้งเตือนรอบที่ 2');
            $table->string('message', 500)->nullable(); // ข้อความแจ้งเตือน
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
