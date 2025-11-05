<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class DepartmentsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $departments = [
            'หน่วยจัดการงานทั่วไป',
            'หน่วยทรัพยากรบุคคล',
            'หน่วยยุทธศาสตร์และพัฒนาคุณภาพ',
            'หน่วยเทคโนโลยีเพื่อการบริหารองค์กร',
            'หน่วยอาคารสถานที่และยานพาหนะ',
            'หอพักนักศึกษา',
            'หน่วยจัดการศึกษาปริญญาตรี',
            'หน่วยการต่างประเทศ',
            'หน่วยพัฒนานักศึกษาและศิษย์เก่าสัมพันธ์',
            'หน่วยเทคโนโลยีเพื่อการศึกษา',
            'หน่วยห้องปฏิบัติการพยาบาล',
            'หน่วยห้องสมุด',
            'หน่วยวิจัยและบริการวิชาการ (วิจัย)',
            'หน่วยวิจัยและบริการวิชาการ (บริการวิชาการ)',
            'หน่วยจัดการศึกษาปริญญาตรีฯ',
        ];

        foreach ($departments as $departmentName) {
            Department::firstOrCreate(
                ['name' => $departmentName],
                [
                    'created_at' => '2025-09-15 12:52:10',
                    'updated_at' => '2025-09-15 12:52:10',
                ]
            );
        }

        // Reset sequence สำหรับ PostgreSQL
        $maxId = DB::table('departments')->max('id');
        if ($maxId) {
            DB::statement("SELECT setval('departments_id_seq', {$maxId})");
        }
    }
}