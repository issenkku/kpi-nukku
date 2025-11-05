<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class VariablesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('variables')->delete();
        
        \DB::table('variables')->insert(array (
            0 => 
            array (
                'id' => 212,
                'variable_name' => 'input_1',
                'label_name' => 'จำนวนบันฑิตที่มีคุณลักษณะที่พึงประสงค์ ครบตามที่กำหนดไว้ในหลักสูตร',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 55,
                'created_at' => '2025-09-30 03:27:46',
                'updated_at' => '2025-09-30 03:27:46',
            ),
            1 => 
            array (
                'id' => 213,
                'variable_name' => 'input_2',
                'label_name' => 'จำนวนบันฑิตทั้งหมดในปีการศึกษานั้น',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 55,
                'created_at' => '2025-09-30 03:27:46',
                'updated_at' => '2025-09-30 03:27:46',
            ),
            2 => 
            array (
                'id' => 214,
                'variable_name' => 'defined_1',
                'label_name' => 'ตัวคูณ',
                'type' => 'defined',
                'value' => 100.0,
                'indicator_id' => 55,
                'created_at' => '2025-09-30 03:27:46',
                'updated_at' => '2025-09-30 03:27:46',
            ),
            3 => 
            array (
                'id' => 89,
                'variable_name' => 'input_1',
                'label_name' => 'จำนวนค่าใช้จ่าย',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 40,
                'created_at' => '2025-09-16 06:12:27',
                'updated_at' => '2025-09-16 06:12:27',
            ),
            4 => 
            array (
                'id' => 90,
                'variable_name' => 'input_2',
                'label_name' => 'จำนวนเงินที่จัดสรรไว้ในแผนปฏิบัติการประจำปี',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 40,
                'created_at' => '2025-09-16 06:12:27',
                'updated_at' => '2025-09-16 06:12:27',
            ),
            5 => 
            array (
                'id' => 77,
                'variable_name' => 'input_1',
                'label_name' => 'FTES',
                'type' => 'input',
                'value' => 1.0,
                'indicator_id' => 6,
                'created_at' => '2025-09-16 03:40:32',
                'updated_at' => '2025-09-16 03:40:48',
            ),
            6 => 
            array (
                'id' => 91,
                'variable_name' => 'defined_1',
                'label_name' => 'ตัวคูณ',
                'type' => 'defined',
                'value' => 100.0,
                'indicator_id' => 40,
                'created_at' => '2025-09-16 06:12:27',
                'updated_at' => '2025-09-16 06:12:27',
            ),
            7 => 
            array (
                'id' => 92,
                'variable_name' => 'input_1',
                'label_name' => 'จำนวนอาจารย์เมื่อสิ้นปีการศึกษา',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 41,
                'created_at' => '2025-09-16 06:15:18',
                'updated_at' => '2025-09-16 06:15:18',
            ),
            8 => 
            array (
                'id' => 93,
                'variable_name' => 'input_2',
                'label_name' => 'จำนวนอาจารย์เมื่อเริ่มต้นปีการศึกษา',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 41,
                'created_at' => '2025-09-16 06:15:18',
                'updated_at' => '2025-09-16 06:15:18',
            ),
            9 => 
            array (
                'id' => 78,
                'variable_name' => 'input_2',
                'label_name' => 'จำนวนอาจารย์ประจำ',
                'type' => 'input',
                'value' => 9.0,
                'indicator_id' => 6,
                'created_at' => '2025-09-16 03:40:32',
                'updated_at' => '2025-09-16 03:41:08',
            ),
            10 => 
            array (
                'id' => 94,
                'variable_name' => 'defined_1',
                'label_name' => 'ตัวคูณ',
                'type' => 'defined',
                'value' => 100.0,
                'indicator_id' => 41,
                'created_at' => '2025-09-16 06:15:18',
                'updated_at' => '2025-09-16 06:15:18',
            ),
            11 => 
            array (
                'id' => 95,
                'variable_name' => 'input_1',
                'label_name' => 'เมื่อสิ้นปีการศึกษา',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 42,
                'created_at' => '2025-09-16 06:18:44',
                'updated_at' => '2025-09-16 06:18:44',
            ),
            12 => 
            array (
                'id' => 96,
                'variable_name' => 'input_2',
                'label_name' => 'จำนวนบุคลากรสายสนับสนุน',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 42,
                'created_at' => '2025-09-16 06:18:44',
                'updated_at' => '2025-09-16 06:18:44',
            ),
            13 => 
            array (
                'id' => 97,
                'variable_name' => 'defined_1',
                'label_name' => 'ตัวคูณ',
                'type' => 'defined',
                'value' => 100.0,
                'indicator_id' => 42,
                'created_at' => '2025-09-16 06:18:44',
                'updated_at' => '2025-09-16 06:18:44',
            ),
            14 => 
            array (
                'id' => 98,
                'variable_name' => 'input_1',
                'label_name' => 'จำนวนนักศึกษาชั้นปีที่สุดท้ายที่มีผลลัพธ์การเรียนรู้ครบตามที่กำหนดไว้ในหลักสูตร',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 44,
                'created_at' => '2025-09-16 06:31:19',
                'updated_at' => '2025-09-16 06:31:19',
            ),
            15 => 
            array (
                'id' => 99,
                'variable_name' => 'input_2',
                'label_name' => 'จำนวนนักศึกษาชั้นปีสุดท้ายทั้งหมด',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 44,
                'created_at' => '2025-09-16 06:31:19',
                'updated_at' => '2025-09-16 06:31:19',
            ),
            16 => 
            array (
                'id' => 100,
                'variable_name' => 'defined_1',
                'label_name' => 'ตัวคูณ',
                'type' => 'defined',
                'value' => 100.0,
                'indicator_id' => 44,
                'created_at' => '2025-09-16 06:31:19',
                'updated_at' => '2025-09-16 06:31:19',
            ),
            17 => 
            array (
                'id' => 101,
                'variable_name' => 'input_1',
                'label_name' => 'จำนวนรวมของผู้สอบผ่านในครั้งแรก ย้อนหลัง 3 ปี',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 45,
                'created_at' => '2025-09-16 06:36:00',
                'updated_at' => '2025-09-16 06:36:00',
            ),
            18 => 
            array (
                'id' => 102,
                'variable_name' => 'input_2',
                'label_name' => 'จำนวนรวมของผู้สำเร็จการศึกษาในปีการศึกษานั้นย้อนหลัง 3 ปี',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 45,
                'created_at' => '2025-09-16 06:36:00',
                'updated_at' => '2025-09-16 06:36:00',
            ),
            19 => 
            array (
                'id' => 103,
                'variable_name' => 'defined_1',
                'label_name' => 'ตัวคูณ',
                'type' => 'defined',
                'value' => 100.0,
                'indicator_id' => 45,
                'created_at' => '2025-09-16 06:36:00',
                'updated_at' => '2025-09-16 06:36:00',
            ),
            20 => 
            array (
                'id' => 110,
                'variable_name' => 'input_1',
                'label_name' => 'สาขาหลักทางการพยาบาล',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 50,
                'created_at' => '2025-09-16 06:51:18',
                'updated_at' => '2025-09-16 06:51:18',
            ),
            21 => 
            array (
                'id' => 111,
                'variable_name' => 'input_1',
                'label_name' => 'จำนวนรวมของอาจารย์พยาบาลประจำที่ปฏิบัติการพยาบาลย้านหลัง 3 ปี',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 53,
                'created_at' => '2025-09-16 06:59:33',
                'updated_at' => '2025-09-16 06:59:33',
            ),
            22 => 
            array (
                'id' => 112,
                'variable_name' => 'input_2',
                'label_name' => 'จำนวนรวมของอาจารย์พยาบาลประจำทั้งหมดย้อนหลัง 3 ปี',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 53,
                'created_at' => '2025-09-16 06:59:33',
                'updated_at' => '2025-09-16 06:59:33',
            ),
            23 => 
            array (
                'id' => 113,
                'variable_name' => 'defined_1',
                'label_name' => 'ตัวคูณ',
                'type' => 'defined',
                'value' => 100.0,
                'indicator_id' => 53,
                'created_at' => '2025-09-16 06:59:33',
                'updated_at' => '2025-09-16 06:59:33',
            ),
            24 => 
            array (
                'id' => 153,
                'variable_name' => 'input_2',
                'label_name' => 'จำนวนอาจารย์ประจำทั้งหมด',
                'type' => 'input',
                'value' => 0.0,
                'indicator_id' => 5,
                'created_at' => '2025-09-16 08:11:41',
                'updated_at' => '2025-09-16 08:22:34',
            ),
            25 => 
            array (
                'id' => 154,
                'variable_name' => 'input_1',
                'label_name' => 'จำนวนอาจารย์ประจำหลักสูตร',
                'type' => 'input',
                'value' => 0.0,
                'indicator_id' => 5,
                'created_at' => '2025-09-16 08:11:41',
                'updated_at' => '2025-09-16 08:22:34',
            ),
            26 => 
            array (
                'id' => 182,
                'variable_name' => 'defined_1',
                'label_name' => 'ตัวคูณ',
                'type' => 'defined',
                'value' => 100.0,
                'indicator_id' => 3,
                'created_at' => '2025-09-29 09:39:31',
                'updated_at' => '2025-09-29 09:39:31',
            ),
            27 => 
            array (
                'id' => 183,
                'variable_name' => 'input_2',
                'label_name' => 'จำนวนอาจารย์ประจำทั้งหมดของสถายบันรวมทุกคุณวุฒิการศึกษา',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 3,
                'created_at' => '2025-09-29 09:39:31',
                'updated_at' => '2025-09-29 09:39:31',
            ),
            28 => 
            array (
                'id' => 184,
                'variable_name' => 'input_1',
                'label_name' => 'จำนวนอาจารย์พยาบาลประจำที่มีคุณสมบัติตามเก็ณฑ์',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 3,
                'created_at' => '2025-09-29 09:39:31',
                'updated_at' => '2025-09-29 09:39:31',
            ),
            29 => 
            array (
                'id' => 189,
                'variable_name' => 'input_1',
                'label_name' => 'จำนวนอาจารย์ประจำที่มีคุณวุฒิปริญญาเอก',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 2,
                'created_at' => '2025-09-30 02:37:59',
                'updated_at' => '2025-09-30 02:42:40',
            ),
            30 => 
            array (
                'id' => 190,
                'variable_name' => 'input_2',
                'label_name' => 'จำนวนอาจารย์ประจำทั้งหมด',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 2,
                'created_at' => '2025-09-30 02:37:59',
                'updated_at' => '2025-09-30 02:42:40',
            ),
            31 => 
            array (
                'id' => 191,
                'variable_name' => 'input_1',
                'label_name' => 'จำนวนรายวิชาที่มีอาจารย์พยาบาลประจำวิชาพยาบาลวิชาชีพสอนภาคปฏิบัติ ไม่เกิน 1:8',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 7,
                'created_at' => '2025-09-30 02:51:41',
                'updated_at' => '2025-09-30 02:51:41',
            ),
            32 => 
            array (
                'id' => 192,
                'variable_name' => 'input_2',
                'label_name' => 'จำนวนรายวิชาภาคปฏิบัติที่เปิดสอนในรายปีการศึกษาที่ครบวาระการรับรองสถาบัน',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 7,
                'created_at' => '2025-09-30 02:51:41',
                'updated_at' => '2025-09-30 02:51:41',
            ),
            33 => 
            array (
                'id' => 193,
                'variable_name' => 'defined_1',
                'label_name' => 'ตัวคูณ',
                'type' => 'defined',
                'value' => 100.0,
                'indicator_id' => 7,
                'created_at' => '2025-09-30 02:51:41',
                'updated_at' => '2025-09-30 02:51:41',
            ),
            34 => 
            array (
                'id' => 195,
                'variable_name' => 'input_1',
                'label_name' => 'ค่าเฉลี่ยของของการประเมินคณะผู้บริหาร',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 37,
                'created_at' => '2025-09-30 03:00:07',
                'updated_at' => '2025-09-30 03:00:07',
            ),
            35 => 
            array (
                'id' => 196,
                'variable_name' => 'input_1',
                'label_name' => 'จำนวนโครงการ/กิจกรรมที่มีผลการดำเนินงานเป็นไปตามเป้าหมายที่กำหนดไว้ในแผลกลยุทธ์/ยุทธศาสตร์',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 39,
                'created_at' => '2025-09-30 03:02:00',
                'updated_at' => '2025-09-30 03:02:00',
            ),
            36 => 
            array (
                'id' => 197,
                'variable_name' => 'input_2',
                'label_name' => 'จำนวนโครงการ/กิจกรรททั้งหมดในแต่ละปีงบประมาณ',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 39,
                'created_at' => '2025-09-30 03:02:00',
                'updated_at' => '2025-09-30 03:02:00',
            ),
            37 => 
            array (
                'id' => 198,
                'variable_name' => 'defined_1',
                'label_name' => 'ตัวคูณ',
                'type' => 'defined',
                'value' => 100.0,
                'indicator_id' => 39,
                'created_at' => '2025-09-30 03:02:00',
                'updated_at' => '2025-09-30 03:02:00',
            ),
            38 => 
            array (
                'id' => 199,
                'variable_name' => 'input_1',
                'label_name' => 'จำนวนรวมของผู้สอบผ่านในการสอบครั้งแรกย้อนหลัง 3 ปี',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 46,
                'created_at' => '2025-09-30 03:03:01',
                'updated_at' => '2025-09-30 03:03:01',
            ),
            39 => 
            array (
                'id' => 200,
                'variable_name' => 'input_2',
                'label_name' => 'จำนวนรวมของผู้สำเร็จการศึกษาในปีการศึกษานั้นๆย้อนหลัง 3 ปี',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 46,
                'created_at' => '2025-09-30 03:03:01',
                'updated_at' => '2025-09-30 03:03:01',
            ),
            40 => 
            array (
                'id' => 201,
                'variable_name' => 'defined_1',
                'label_name' => 'ตัวคูณ',
                'type' => 'defined',
                'value' => 100.0,
                'indicator_id' => 46,
                'created_at' => '2025-09-30 03:03:01',
                'updated_at' => '2025-09-30 03:03:01',
            ),
            41 => 
            array (
                'id' => 204,
                'variable_name' => 'input_2',
            'label_name' => 'จำนวนอาจารย์ประจำทั้งหมด ( ปี1+ปี2+ปี3+ปี4+ปี5 )',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 49,
                'created_at' => '2025-09-30 03:23:18',
                'updated_at' => '2025-09-30 03:23:40',
            ),
            42 => 
            array (
                'id' => 205,
                'variable_name' => 'input_1',
            'label_name' => 'ผลรวมถ่วงน้ำหนักของบทความวิจัยและผลงานวิชาการที่ตีพิมพ์เผยแพร่ ( ปี1+ปี2+ปี3+ปี4+ปี5 )',
                'type' => 'input',
                'value' => NULL,
                'indicator_id' => 49,
                'created_at' => '2025-09-30 03:23:18',
                'updated_at' => '2025-09-30 03:23:40',
            ),
        ));

        $sequence = \DB::selectOne("SELECT pg_get_serial_sequence(?, 'id') AS seq", ['variables']);
        if ($sequence && !empty($sequence->seq)) {
            $maxId = \DB::table('variables')->max('id');
            $value = $maxId ?? 0;
            $isCalled = $maxId !== null;

            \DB::select("SELECT setval(?, ?, ?)", [$sequence->seq, $value, $isCalled]);
        }


    }
}
