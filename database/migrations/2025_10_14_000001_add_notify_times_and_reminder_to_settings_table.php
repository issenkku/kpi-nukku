<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('notify_time1', 5)->nullable()->after('notify_date1');
            $table->string('notify_time2', 5)->nullable()->after('notify_date2');

            // Optional generic reminder config for future rounds
            $table->string('remind_days', 50)->nullable()->after('message'); // e.g. "7,3,1"
            $table->string('remind_time', 5)->nullable()->after('remind_days'); // e.g. "09:00"
            $table->boolean('remind_enabled')->default(false)->after('remind_time');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['notify_time1','notify_time2','remind_days','remind_time','remind_enabled']);
        });
    }
};

