<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $table = 'assignments';
    public $timestamps = false;
    // ถ้าไม่มี primary key จริง ๆ และใช้เพื่อ "อ่าน" อย่างเดียว
    protected $primaryKey = null;
    public $incrementing = false;
    protected $fillable = [
        'indicator_id',
        'collector', // <- เก็บ user_id ไว้ในคอลัมน์ชื่อ collector
    ];

    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }

    // ความสัมพันธ์ไปยังผู้ใช้ที่เป็นผู้รวบรวมข้อมูล (rename เพื่อเลี่ยงชนกับคอลัมน์ collector)
    public function collectorUser()
    {
        return $this->belongsTo(User::class, 'collector', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'collector');
    }
}
