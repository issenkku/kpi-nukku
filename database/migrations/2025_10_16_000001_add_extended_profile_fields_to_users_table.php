<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'title')) {
                $table->string('title')->nullable();
            }
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable();
            }

            if (!Schema::hasColumn('users', 'positype')) {
                $table->string('positype')->nullable();
            }
            if (!Schema::hasColumn('users', 'workline')) {
                $table->string('workline')->nullable();
            }
            if (!Schema::hasColumn('users', 'posi')) {
                $table->string('posi')->nullable();
            }
            if (!Schema::hasColumn('users', 'level')) {
                $table->string('level')->nullable();
            }

            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable();
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->boolean('status')->default(true);
            }

            if (!Schema::hasColumn('users', 'department_id')) {
                $table->foreignId('department_id')->constrained('departments')->restrictOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'department_id')) {
                // Drop FK before column (DB driver will enforce this)
                try {
                    $table->dropConstrainedForeignId('department_id');
                } catch (\Throwable $e) {
                    // Fallback for drivers without dropConstrainedForeignId name resolution
                    try { $table->dropForeign(['department_id']); } catch (\Throwable $e2) {}
                    try { $table->dropColumn('department_id'); } catch (\Throwable $e3) {}
                }
            }

            foreach (['status','phone','level','posi','workline','positype','last_name','first_name','title'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    try { $table->dropColumn($col); } catch (\Throwable $e) {}
                }
            }
        });
    }
};

