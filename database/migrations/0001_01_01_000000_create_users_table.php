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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            // ข้อมูลส่วนตัว (ภาษาไทย)
            $table->string('title')->nullable(); // คำนำหน้าชื่อ: นาย, นาง, นางสาว
            $table->string('first_name'); // ชื่อจริง
            $table->string('last_name'); // นามสกุล
            // Note: ชื่อเต็มจะใช้ Accessor ใน Model แทน (display_name, full_name)
           
            // ข้อมูลการทำงาน
            $table->string('positype')->nullable(); // ประเภทบุคลากร: พนักงานมหาวิทยาลัย, ข้าราชการ
            $table->string('workline')->nullable(); // สายงาน: สนับสนุน, วิชาการ
            $table->string('posi')->nullable(); // ตำแหน่ง
            $table->string('level')->nullable(); // ระดับ: ชำนาญการ, เชี่ยวชาญ
            
            // ข้อมูลติดต่อ
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable();
            
            // ระบบ
            $table->string('password');
            $table->boolean('status')->default(true); // true = Active, false = Inactive
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->foreignId('department_id')->constrained('departments')->restrictOnDelete();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('departments');
    }
};
