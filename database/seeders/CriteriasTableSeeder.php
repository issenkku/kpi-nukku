<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CriteriasTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('criterias')->delete();
        
        $rows = array (
            0 => 
            array (
                'id' => 9,
                'name' => 'ร้อยละของอาจารย์ประจําหลักสูตรต่ออาจารย์ประจําทั้งหมด ร้อยละ 100',
                'description' => 'หลักฐาน/เอกสารแสดงรายชื่ออาจารย์ประจําทั้งหมด คุณวุฒิทางการศึกษา ตําแหน่งทางวิชาการ และผลงานทางวิชาการที่ไม่ใช่ส่วนหนึ่งของการศึกษาเพื่อรับปริญญา และเป็นผลงานทางวิชาการที่ได้รับการเผยแพร่ตามหลักเกณฑ์ที่กําหนดในการพิจารณาแต่งตั้งให้บุคคลดํารงตําแหน่งทางวิชาการในรอบ 5 ปีย้อนหลัง',
                'sequence' => 1,
                'indicator_id' => 5,
                'status' => 0,
                'report' => NULL,
            ),
            1 => 
            array (
                'id' => 12,
            'name' => '2) จํานวนพยาบาลวิชาชีพ ที่ทําหน้าที่สอนภาคปฏิบัติขณะที่ปฏิบัติงาน หรือไม่ปฏิบัติงานประจําไม่มากกว่าร้อยละ 40 ของความต้องการจํานวนอาจารย์ประจําของแต่ละรายวิชา',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 7,
            'status' => 0,
            'report' => NULL,
        ),
        2 => 
        array (
            'id' => 17,
            'name' => 'สถานที่และสิ่งแวดล้อม สะอาด สวยงาม ถูกสุขอนามัย ปลอดภัย',
            'description' => NULL,
            'sequence' => 5,
            'indicator_id' => 8,
            'status' => 0,
            'report' => NULL,
        ),
        3 => 
        array (
            'id' => 10,
            'name' => 'อัตราส่วนจํานวนอาจารย์ประจําต่อนิสิต/นักศึกษาเต็มเวลาเทียบเท่าไม่เกิน 1:6',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 6,
            'status' => 0,
            'report' => NULL,
        ),
        4 => 
        array (
            'id' => 20,
            'name' => 'มีระบบสารสนเทศเพื่อการตัดสินใจ ด้านการบริหาร',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 9,
            'status' => 0,
            'report' => NULL,
        ),
        5 => 
        array (
            'id' => 13,
            'name' => 'ห้องปฏิบัติงานเหมาะสมต่อการปฏิบัติงานของอาจารย์และบุคลากร',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 8,
            'status' => 0,
            'report' => NULL,
        ),
        6 => 
        array (
            'id' => 21,
            'name' => 'มีระบบสารสนเทศเพื่อการตัดสินใจ ด้านการเรียนการสอน',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 9,
            'status' => 0,
            'report' => NULL,
        ),
        7 => 
        array (
            'id' => 22,
            'name' => 'มีระบบสารสนเทศเพื่อการตัดสินใจ ด้านการวิจัย',
            'description' => NULL,
            'sequence' => 3,
            'indicator_id' => 9,
            'status' => 0,
            'report' => NULL,
        ),
        8 => 
        array (
            'id' => 15,
            'name' => 'ห้องปฏิบัติการวิทยาศาสตร์และวิทยาศาสตร์สุขภาพหรือในกรณีความร่วมมือทางวิชาการโดยใช้ห้องปฏิบัติการวิทยาศาสตร์และวิทยาศาสตร์สุขภาพของสถาบันการศึกษาอื่น ต้องมีอุปกรณ์ที่ได้มาตรฐานเพียงพอ พร้อมใช้ เอื้อต่อการจัดการเรียนการสอน',
            'description' => NULL,
            'sequence' => 3,
            'indicator_id' => 8,
            'status' => 0,
            'report' => NULL,
        ),
        9 => 
        array (
            'id' => 14,
            'name' => 'ห้องเรียนมีจำนวนและขนาดเหมาะสมกับจำนวนผู้เรียน มีโสตทัศนูปกรณ์ที่ได้มาตรฐาน เพียงพอและพร้อมใช้สำหรับผู้เรียนและผู้สอน',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 8,
            'status' => 0,
            'report' => NULL,
        ),
        10 => 
        array (
            'id' => 23,
            'name' => 'มีระบบสารสนเทศเพื่อการตัดสินใจ ด้านการบริการวิชาการ',
            'description' => NULL,
            'sequence' => 4,
            'indicator_id' => 9,
            'status' => 0,
            'report' => NULL,
        ),
        11 => 
        array (
            'id' => 25,
            'name' => 'มีผู้รับผิดชอบและบุคลากรประจำศูนย์การเรียนรู้การปฏิบัติการพยาบาล',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 10,
            'status' => 0,
            'report' => NULL,
        ),
        12 => 
        array (
            'id' => 26,
            'name' => 'มีห้องปฏิบัติการพยาบาลและอุปกรณ์การศึกษา เพียงพอ เหมาะสม และพร้อมใช้ ให้นักศึกษาฝึกปฏิบัติหัตถการทางการพยาบาล',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 10,
            'status' => 0,
            'report' => NULL,
        ),
        13 => 
        array (
            'id' => 30,
            'name' => 'มีหนังสือหรือตำราหลักทางการพยาบาลที่ไม่เกิน 10 ปี จำนวน 5 ชื่อเรื่องต่อสาขาวิชา ใน 5 สาขาวิชาหลัก ครอบคลุมทั้งภาษาไทยและภาษาต่างประเทศ ที่มีการบริหารจัดการให้ผู้เรียนทุกคนสามารถเข้าถึงได้อย่างเพียงพอ โดยระบุไว้ในรายละเอียดของรายวิชา และต้องมีจำนวนหนังสือ/ตำราหลักไม่น้อยกว่า 5 เล่มต่อชื่อเรื่อง',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 11,
            'status' => 0,
            'report' => NULL,
        ),
        14 => 
        array (
            'id' => 31,
        'name' => 'มีหนังสือหรือตำราทางการพยาบาลและวิทยาศาสตร์สุขภาพที่ไม่เกิน 10 ปี ไม่น้อยกว่า 50 เล่มต่อนิสิต/นักศึกษา 1 คน หรือให้มีจำนวนไม่น้อยกว่า 20 เล่มต่อนิสิต/นักศึกษา ในกรณีที่มีฐานข้อมูลหนังสืออิเล็กทรอนิกส์ที่มีความต่อเนื่องไม่น้อยกว่า 1 ปี ที่มีผู้เข้าใช้งาน(user)ได้พร้อมกันทั้งสถาบัน',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 11,
            'status' => 0,
            'report' => NULL,
        ),
        15 => 
        array (
            'id' => 35,
            'name' => 'มีผู้รับผิดชอบพันธกิจการผลิตงานวิจัยและนวัตกรรม',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 12,
            'status' => 0,
            'report' => NULL,
        ),
        16 => 
        array (
            'id' => 37,
            'name' => 'มีงบประมาณสนับสนุนการผลิตงานวิจัยและนวัตกรรม',
            'description' => NULL,
            'sequence' => 3,
            'indicator_id' => 12,
            'status' => 0,
            'report' => NULL,
        ),
        17 => 
        array (
            'id' => 38,
            'name' => 'มีผู้รับผิดชอบพันธกิจการบริการวิชาการ/วิชาชีพ/สังคม',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 13,
            'status' => 0,
            'report' => NULL,
        ),
        18 => 
        array (
            'id' => 39,
            'name' => 'มีหน่วยงานที่สนับสนุนการบริการวิชาการ/วิชาชีพ/สังคม',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 13,
            'status' => 0,
            'report' => NULL,
        ),
        19 => 
        array (
            'id' => 40,
            'name' => 'มีงบประมาณสนับสนุนการบริการวิชาการ/วิชาชีพ/สังคม',
            'description' => NULL,
            'sequence' => 3,
            'indicator_id' => 13,
            'status' => 0,
            'report' => NULL,
        ),
        20 => 
        array (
            'id' => 41,
            'name' => 'มีผู้รับผิดชอบพันธกิจการทำนุบำรุงศิลปะและวัฒนธรรม',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 14,
            'status' => 0,
            'report' => NULL,
        ),
        21 => 
        array (
            'id' => 42,
            'name' => 'มีหน่วยงานที่สนับสนุนการทำนุบำรุงศิลปะและวัฒนธรรม',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 14,
            'status' => 0,
            'report' => NULL,
        ),
        22 => 
        array (
            'id' => 43,
            'name' => 'มีงบประมาณสนับสนุนการทำนุบำรุงศิลปะและวัฒนธรรม',
            'description' => NULL,
            'sequence' => 3,
            'indicator_id' => 14,
            'status' => 0,
            'report' => NULL,
        ),
        23 => 
        array (
            'id' => 44,
            'name' => 'มีผู้รับผิดชอบพันธกิจการพัฒนานิสิต/นักศึกษา/ศิษย์เก่า',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 15,
            'status' => 0,
            'report' => NULL,
        ),
        24 => 
        array (
            'id' => 45,
            'name' => 'มีหน่วยงานที่สนับสนุนการพัฒนานิสิต/นักศึกษา/ศิษย์เก่า',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 15,
            'status' => 0,
            'report' => NULL,
        ),
        25 => 
        array (
            'id' => 46,
            'name' => 'มีงบประมาณสนับสนุนการพัฒนานิสิต/นักศึกษา/ศิษย์เก่า',
            'description' => NULL,
            'sequence' => 3,
            'indicator_id' => 15,
            'status' => 0,
            'report' => NULL,
        ),
        26 => 
        array (
            'id' => 51,
            'name' => 'การจัดทำกลยุทธ์/ยุทธศาสตร์ แผนปฏิบัติการ และแผนจัดสรรงบประมาณ ที่สอดคล้องกับวิสัยทัศน์',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 17,
            'status' => 0,
            'report' => NULL,
        ),
        27 => 
        array (
            'id' => 52,
            'name' => 'การถ่ายทอดกลยุทธ์/ยุทธศาสตร์และแผนปฏิบัติการไปยังบุคลากร',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 17,
            'status' => 0,
            'report' => NULL,
        ),
        28 => 
        array (
            'id' => 53,
            'name' => 'การประเมินแผนกลยุทธ์/ยุทธศาสตร์ และแผนปฏิบัติการ รวมถึงแผนการจัดสรรงบประมาณ',
            'description' => NULL,
            'sequence' => 3,
            'indicator_id' => 17,
            'status' => 0,
            'report' => NULL,
        ),
        29 => 
        array (
            'id' => 54,
            'name' => 'ทบทวนกระบวนการจัดทำแผนกลยุทธ์/ยุทธศาสตร์ แผนปฏิบัติการ แผนจัดสรรงบประมาณ และ ปรับปรุงกระบวนการในข้อ 1 และข้อ 2',
            'description' => NULL,
            'sequence' => 4,
            'indicator_id' => 17,
            'status' => 0,
            'report' => NULL,
        ),
        30 => 
        array (
            'id' => 55,
            'name' => 'ผู้นำประกาศเจตนารมย์ในการประพฤติปฏิบัติตามกฎระเบียบกฎหมายและจริยธรรม และการจัดการ ข้อร้องเรียน อุทธรณ์ ร้องทุกข์',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 18,
            'status' => 0,
            'report' => NULL,
        ),
        31 => 
        array (
            'id' => 56,
            'name' => 'ผู้นำมีการกำกับดูแลให้บุคลากรมีการประพฤติตามกฎหมายและจริยธรรม',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 18,
            'status' => 0,
            'report' => NULL,
        ),
        32 => 
        array (
            'id' => 58,
            'name' => 'ทบทวนกระบวนการกำกับให้องค์กรมีการประพฤติ ปฏิบัติตามกฎระเบียบ กฎหมายและจริยธรรม ของผู้นำและบุคลากร การจัดการข้อร้องเรียน อุทธรณ์ ร้องทุกข์และปรับปรุงกระบวนการในข้อ 1 2 และข้อ 3',
            'description' => NULL,
            'sequence' => 4,
            'indicator_id' => 18,
            'status' => 0,
            'report' => NULL,
        ),
        33 => 
        array (
            'id' => 59,
            'name' => 'ระบบการจัดเก็บ การประมวลผล การวิเคราะห์ข้อมูล ครอบคลุมทุกพันธกิจที่สถาบันกำหนดไว้',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 19,
            'status' => 0,
            'report' => NULL,
        ),
        34 => 
        array (
            'id' => 60,
            'name' => 'กำกับติดตามให้มีข้อมูล/สารสนเทศที่เป็นปัจจุบัน ถูกต้อง พร้อมใช้ เข้าถึงได้',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 19,
            'status' => 0,
            'report' => NULL,
        ),
        35 => 
        array (
            'id' => 61,
            'name' => 'นำข้อมูล/สารสนเทศไปใช้ในการติดตามผลการดำเนินงานตามพันธกิจ/ ใช้ในการตัดสินใจ ปรับกลยุทธ์/ ยุทธศาสตร์/แผนปฏิบัติการประจำปี และระบบปฏิบัติการ',
            'description' => NULL,
            'sequence' => 3,
            'indicator_id' => 19,
            'status' => 0,
            'report' => NULL,
        ),
        36 => 
        array (
            'id' => 62,
            'name' => 'ทบทวนกระบวนการในข้อ 1 ถึง 3 และนำมาปรับปรุงกระบวนการใช้ข้อมูล/สารสนเทศเพื่อการตัดสินใจ',
            'description' => NULL,
            'sequence' => 4,
            'indicator_id' => 19,
            'status' => 0,
            'report' => NULL,
        ),
        37 => 
        array (
            'id' => 63,
            'name' => 'การจัดทำแผนการบริหารความเสี่ยงที่สอดคล้องกับยุทธศาสตร์ของสถาบัน',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 20,
            'status' => 0,
            'report' => NULL,
        ),
        38 => 
        array (
            'id' => 64,
        'name' => 'การจัดทำแผนเตรียมความพร้อมต่อภาวะฉุกเฉินและแผนความต่อเนื่องทางธุรกิจ (Business Continuity Plan: BCP)',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 20,
            'status' => 0,
            'report' => NULL,
        ),
        39 => 
        array (
            'id' => 65,
            'name' => 'การนำแผนความเสี่ยง ไปใช้ในการควบคุมภายใน/จัดการ และมีการติดตาม ประเมินผลการดำเนินการ ตามแผน',
            'description' => NULL,
            'sequence' => 3,
            'indicator_id' => 20,
            'status' => 0,
            'report' => NULL,
        ),
        40 => 
        array (
            'id' => 66,
            'name' => 'การซ้อมแผนการเตรียมความพร้อมต่อภาวะฉุกเฉินและแผนความต่อเนื่องทางธุรกิจ',
            'description' => NULL,
            'sequence' => 4,
            'indicator_id' => 20,
            'status' => 0,
            'report' => NULL,
        ),
        41 => 
        array (
            'id' => 67,
            'name' => 'ทบทวนกระบวนการในข้อ 1 และ 2 และปรับปรุงแผนการบริหารความเสี่ยง แผนการเตรียมความพร้อม ต่อภาวะฉุกเฉิน และแผนความต่อเนื่องทางธุรกิจทุกปี',
            'description' => NULL,
            'sequence' => 5,
            'indicator_id' => 20,
            'status' => 0,
            'report' => NULL,
        ),
        42 => 
        array (
            'id' => 122,
            'name' => 'การจัดการผลลัพธ์ที่เกิดจากการปฏิบัติการพยาบาลของอาจารย์',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 33,
            'status' => 0,
            'report' => NULL,
        ),
        43 => 
        array (
            'id' => 69,
            'name' => 'การดำเนินการตามแผนให้เป็นไปตามความร่วมมือ',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 21,
            'status' => 0,
            'report' => NULL,
        ),
        44 => 
        array (
            'id' => 72,
            'name' => 'กำหนดขีดความสามารถ และการวางแผนด้านอัตรากำลังทั้งสายวิชาการและสายสนับสนุนที่สามารถตอบสนองต่อกลยุทธ์/ยุทธศาสตร์ และพันธกิจสำคัญที่สถาบันกำหนด',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 22,
            'status' => 0,
            'report' => NULL,
        ),
        45 => 
        array (
            'id' => 73,
            'name' => 'สรรหา และจัดสรรบุคลากรทั้งสายวิชาการและสายสนับสนุนให้เหมาะกับงานและสอดคล้องต่อความต้องการและความจำเป็นของสถาบัน',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 22,
            'status' => 0,
            'report' => NULL,
        ),
        46 => 
        array (
            'id' => 74,
            'name' => 'มีระบบการดูแลบุคลากรใหม่',
            'description' => NULL,
            'sequence' => 3,
            'indicator_id' => 22,
            'status' => 0,
            'report' => NULL,
        ),
        47 => 
        array (
            'id' => 77,
            'name' => 'การมอบหมายงานที่สอดคล้องกับพันธกิจและวิสัยทัศน์ของสถาบันทุกระดับ',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 23,
            'status' => 0,
            'report' => NULL,
        ),
        48 => 
        array (
            'id' => 78,
        'name' => 'การจัดทำข้อตกลงการปฏิบัติงาน(Performance Agreement)',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 23,
            'status' => 0,
            'report' => NULL,
        ),
        49 => 
        array (
            'id' => 79,
            'name' => 'การส่งเสริมความก้าวหน้าและความสำเร็จในงานโดยการประเมินผลการปฏิบัติงาน เพื่อใช้ในกระบวนการกำหนดค่าตอบแทน การให้รางวัลและการยกย่องชมเชย',
            'description' => NULL,
            'sequence' => 3,
            'indicator_id' => 23,
            'status' => 0,
            'report' => NULL,
        ),
        50 => 
        array (
            'id' => 80,
            'name' => 'การกำกับติดตามให้มีการดำเนินงานในข้อ 1 2 และ3',
            'description' => NULL,
            'sequence' => 4,
            'indicator_id' => 23,
            'status' => 0,
            'report' => NULL,
        ),
        51 => 
        array (
            'id' => 81,
            'name' => 'ทบทวนกระบวนการในข้อ 1 2 และ 3 และปรับปรุงระบบบริหารงานบุคคล',
            'description' => NULL,
            'sequence' => 5,
            'indicator_id' => 23,
            'status' => 0,
            'report' => NULL,
        ),
        52 => 
        array (
            'id' => 82,
            'name' => 'แผนการพัฒนาสมรรรถนะและประสิทธิภาพการจัดการเรียนการสอน การวิจัยและบริการวิชาการของอาจารย์',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 24,
            'status' => 0,
            'report' => NULL,
        ),
        53 => 
        array (
            'id' => 83,
            'name' => 'การพัฒนาบุคลากรตามขีดความสามารถ สอดคล้องกับความต้องการพัฒนาตนเองของบุคลากรและความจำเป็นของสถาบัน',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 24,
            'status' => 0,
            'report' => NULL,
        ),
        54 => 
        array (
            'id' => 84,
            'name' => 'แผนการพัฒนาผู้บริหาร',
            'description' => NULL,
            'sequence' => 3,
            'indicator_id' => 24,
            'status' => 0,
            'report' => NULL,
        ),
        55 => 
        array (
            'id' => 85,
            'name' => 'การกำกับติดตามให้มีการดำเนินงานในข้อ 1 2 และ 3',
            'description' => NULL,
            'sequence' => 4,
            'indicator_id' => 24,
            'status' => 0,
            'report' => NULL,
        ),
        56 => 
        array (
            'id' => 86,
            'name' => 'ทบทวนกระบวนการในข้อ 1 2 3 และ 4 และปรับปรุงกระบวนการพัฒนาบุคลากร',
            'description' => NULL,
            'sequence' => 5,
            'indicator_id' => 24,
            'status' => 0,
            'report' => NULL,
        ),
        57 => 
        array (
            'id' => 87,
            'name' => 'มีการกำหนดปัจจัยขับเคลื่อนความผูกพันของบุคลากร',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 25,
            'status' => 0,
            'report' => NULL,
        ),
        58 => 
        array (
            'id' => 88,
            'name' => 'มีการสร้างความผูกพันในบุคลากร',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 25,
            'status' => 0,
            'report' => NULL,
        ),
        59 => 
        array (
            'id' => 89,
            'name' => 'มีการกำหนดนโยบายด้านสวัสดิการ สิทธิประโยชน์ และสภาพแวดล้อมในการทำงาน',
            'description' => NULL,
            'sequence' => 3,
            'indicator_id' => 25,
            'status' => 0,
            'report' => NULL,
        ),
        60 => 
        array (
            'id' => 90,
            'name' => 'มีการกำกับติดตามให้มีการดำเนินงานในข้อ 1 ถึง 3',
            'description' => NULL,
            'sequence' => 4,
            'indicator_id' => 25,
            'status' => 0,
            'report' => NULL,
        ),
        61 => 
        array (
            'id' => 91,
            'name' => 'ทบทวนกระบวนการในข้อ 1 ถึง 3 ปรับปรุงกระบวนการสร้างและจัดการความผูกพัน',
            'description' => NULL,
            'sequence' => 5,
            'indicator_id' => 25,
            'status' => 0,
            'report' => NULL,
        ),
        62 => 
        array (
            'id' => 92,
            'name' => 'ใช้หลักสูตรที่ผ่านความเห็นชอบจากสภาการพยาบาล และ การรับรองจากกระทรวงการอุดมศึกษา วิทยาศาสตร์ วิจัยและนวัตกรรม',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 26,
            'status' => 0,
            'report' => NULL,
        ),
        63 => 
        array (
            'id' => 93,
            'name' => 'มีการเผยแพร่หลักสูตร รวมทั้งผลลัพธ์การเรียนรู้ของหลักสูตรไปยัง อาจารย์ นักศึกษา และผู้มีส่วนได้เสีย',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 26,
            'status' => 0,
            'report' => NULL,
        ),
        64 => 
        array (
            'id' => 94,
            'name' => 'มีระบบและกลไกในการบริหารหลักสูตรที่สอดคล้องกับการประกันคุณภาพของหลักสูตร',
            'description' => NULL,
            'sequence' => 3,
            'indicator_id' => 26,
            'status' => 0,
            'report' => NULL,
        ),
        65 => 
        array (
            'id' => 95,
            'name' => 'มีการกำกับการบริหารหลักสูตรให้มีการดำเนินงานตามที่หลักสูตรกำหนด',
            'description' => NULL,
            'sequence' => 4,
            'indicator_id' => 26,
            'status' => 0,
            'report' => NULL,
        ),
        66 => 
        array (
            'id' => 97,
            'name' => 'การจัดกระบวนการเรียนรู้ทั้งภาคทฤษฎีและภาคปฏิบัติสอดคล้องกับที่ได้ออกแบบไว้ในหลักสูตร',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 27,
            'status' => 0,
            'report' => NULL,
        ),
        67 => 
        array (
            'id' => 103,
            'name' => 'การกำกับดูแลให้เป็นไปตามข้อกำหนดของกระบวนการรับเข้าและแผนการรับนักศึกษา',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 28,
            'status' => 0,
            'report' => NULL,
        ),
        68 => 
        array (
            'id' => 104,
            'name' => 'แผนการเตรียมความพร้อมของนักศึกษาใหม่และการกำกับให้เป็นไปตามแผน',
            'description' => NULL,
            'sequence' => 3,
            'indicator_id' => 28,
            'status' => 0,
            'report' => NULL,
        ),
        69 => 
        array (
            'id' => 105,
            'name' => 'ทบทวนกระบวนการในข้อ 1 ถึง 3 และนำมาปรับปรุงกระบวนการรับนักศึกษาและการเตรียมความพร้อม',
            'description' => NULL,
            'sequence' => 4,
            'indicator_id' => 28,
            'status' => 0,
            'report' => NULL,
        ),
        70 => 
        array (
            'id' => 106,
            'name' => 'มีแผนการจัดการให้มีสิ่งสนับสนุนการเรียนรู้ที่ทันสมัย มีมาตรฐาน เพียงพอ พร้อมใช้ สอดคล้องกับความต้องการของผู้เรียนและอาจารย์ ครอบคลุม ห้องเรียน ห้องปฏิบัติการ ห้องสมุด เทคโนโลยีดิจิทัล เป็นอย่างน้อย',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 29,
            'status' => 0,
            'report' => NULL,
        ),
        71 => 
        array (
            'id' => 107,
            'name' => 'มีการกำกับดูแลจัดการสิ่งสนับสนุนการเรียนรู้ให้เป็นไปตามแผน',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 29,
            'status' => 0,
            'report' => NULL,
        ),
        72 => 
        array (
            'id' => 108,
            'name' => 'มีการทบทวนการดำเนินงานในข้อ 1 และ 2 และปรับปรุงกระบวนการจัดสิ่งสนับสนุนการเรียนรู้',
            'description' => NULL,
            'sequence' => 3,
            'indicator_id' => 29,
            'status' => 0,
            'report' => NULL,
        ),
        73 => 
        array (
            'id' => 102,
            'name' => 'แผนการรับและแนวทางการคัดเลือกนักศึกษา',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 28,
            'status' => 0,
            'report' => NULL,
        ),
        74 => 
        array (
            'id' => 109,
            'name' => 'แผนการพัฒนางานวิจัยและ/หรือนวัตกรรมที่สอดคล้องกับวิสัยทัศน์และความเชี่ยวชาญของสถาบัน รวมถึงแผนการพัฒนาบุคลากรให้มีศักยภาพในการสร้างงานวิจัย/นวัตกรรม การจัดสรรภารกิจและเวลาการสนับสนุนงบประมาณ การจัดบุคลากรช่วยสนับสนุนการวิจัยหรือการจัดที่ปรึกษา',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 30,
            'status' => 0,
            'report' => NULL,
        ),
        75 => 
        array (
            'id' => 110,
            'name' => 'ระบบและกลไกการสนับสนุนงานวิจัยและนวัตกรรม',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 30,
            'status' => 0,
            'report' => NULL,
        ),
        76 => 
        array (
            'id' => 111,
            'name' => 'การกำกับดูแลจริยธรรมและจรรยาบรรณนักวิจัย/นักวิชาการ',
            'description' => NULL,
            'sequence' => 3,
            'indicator_id' => 30,
            'status' => 0,
            'report' => NULL,
        ),
        77 => 
        array (
            'id' => 112,
            'name' => 'การกำกับดูแลและส่งเสริมให้มีการผลิตงานวิจัยและนวัตกรรม รวมถึงการนำไปใช้ประโยชน์',
            'description' => NULL,
            'sequence' => 4,
            'indicator_id' => 30,
            'status' => 0,
            'report' => NULL,
        ),
        78 => 
        array (
            'id' => 114,
            'name' => 'มีแผนการส่งเสริมการเผยแพร่งานวิจัยและ/หรือการจดทรัพย์สินทางปัญญาในระดับชาติ/นานาชาติ',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 31,
            'status' => 0,
            'report' => NULL,
        ),
        79 => 
        array (
            'id' => 115,
            'name' => 'มีระบบและกลไกการกำกับดูแลให้มีการเผยแพร่งานวิจัยและ/หรือการจดทรัพย์สินทางปัญญา',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 31,
            'status' => 0,
            'report' => NULL,
        ),
        80 => 
        array (
            'id' => 116,
            'name' => 'มีการทบทวนการดำเนินงานในข้อ 1 และ2 นำมาปรับปรุงแผนและกระบวนการเผยแพร่งานวิจัย และ/หรือการจดทรัพย์สินทางปัญญา',
            'description' => NULL,
            'sequence' => 3,
            'indicator_id' => 31,
            'status' => 0,
            'report' => NULL,
        ),
        81 => 
        array (
            'id' => 117,
            'name' => 'มีแผนบริการวิชาการแก่สังคมของสถาบันเพื่อสุขภาพชุมชน/สังคมที่สอดคล้องกับวิสัยทัศน์และความเชี่ยวชาญของสถาบัน และตอบสนองต่อความต้องการด้านสุขภาพของชุมชน/สังคม',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 32,
            'status' => 0,
            'report' => NULL,
        ),
        82 => 
        array (
            'id' => 118,
            'name' => 'มีระบบและกลไกการให้บริการวิชาการแก่ชุมชน/สังคม',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 32,
            'status' => 0,
            'report' => NULL,
        ),
        83 => 
        array (
            'id' => 119,
            'name' => 'มีการกำกับดูแลให้มีการบริการวิชาการแก่ชุมชน/สังคมตามแผน',
            'description' => NULL,
            'sequence' => 3,
            'indicator_id' => 32,
            'status' => 0,
            'report' => NULL,
        ),
        84 => 
        array (
            'id' => 120,
            'name' => 'มีการทบทวนการดำเนินงานในข้อ 1 ถึง 3 และนำมาปรับปรุงแผนและการดำเนินงาน',
            'description' => NULL,
            'sequence' => 4,
            'indicator_id' => 32,
            'status' => 0,
            'report' => NULL,
        ),
        85 => 
        array (
            'id' => 121,
            'name' => 'แผนการปฏิบัติการพยาบาลของอาจารย์ในสาขาที่รับผิดชอบ และการสนับสนุนให้มีการดำเนินงานตามแผน',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 33,
            'status' => 0,
            'report' => NULL,
        ),
        86 => 
        array (
            'id' => 125,
            'name' => 'มีแผนการทำนุบำรุงศิลปวัฒนธรรมที่มีโครงการ/กิจกรรมซึ่งบูรณาการกับการเรียนการสอน/บริการวิชาการ/วิจัย',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 34,
            'status' => 0,
            'report' => NULL,
        ),
        87 => 
        array (
            'id' => 128,
            'name' => 'แผนการพัฒนานักศึกษาให้มีคุณลักษณะที่สอดคล้องกับอัตลักษณ์บัณฑิต/ค่านิยมของสถาบัน',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 35,
            'status' => 0,
            'report' => NULL,
        ),
        88 => 
        array (
            'id' => 129,
            'name' => 'มีระบบและกลไกการพัฒนานักศึกษา ครอบคลุมการให้คำปรึกษา การช่วยเหลือนักศึกษา การรับและจัดการข้อร้องเรียนของนักศึกษา การสนับสนุนให้นักศึกษาประพฤติตามกฎระเบียบและหลักจริยธรรม',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 35,
            'status' => 0,
            'report' => NULL,
        ),
        89 => 
        array (
            'id' => 130,
            'name' => 'มีการดำเนินงานพัฒนานักศึกษาตามแผนที่กำหนด',
            'description' => NULL,
            'sequence' => 3,
            'indicator_id' => 35,
            'status' => 0,
            'report' => NULL,
        ),
        90 => 
        array (
            'id' => 131,
            'name' => 'ทบทวนการดำเนินงานในข้อ 1 ถึง 3 และนำมาปรับปรุงแผนและกระบวนการพัฒนานักศึกษา',
            'description' => NULL,
            'sequence' => 4,
            'indicator_id' => 35,
            'status' => 0,
            'report' => NULL,
        ),
        91 => 
        array (
            'id' => 132,
            'name' => 'แผนการจัดการความสัมพันธ์ของนักศึกษาและศิษย์เก่ากับสถาบัน',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 36,
            'status' => 0,
            'report' => NULL,
        ),
        92 => 
        array (
            'id' => 133,
            'name' => 'ระบบและกลไกการสร้างความสัมพันธ์ของนักศึกษาและศิษย์เก่ากับสถาบัน',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 36,
            'status' => 0,
            'report' => NULL,
        ),
        93 => 
        array (
            'id' => 134,
            'name' => 'มีการดำเนินการสร้างความสัมพันธ์ของนักศึกษาและศิษย์เก่ากับสถาบันตามแผน',
            'description' => NULL,
            'sequence' => 3,
            'indicator_id' => 36,
            'status' => 0,
            'report' => NULL,
        ),
        94 => 
        array (
            'id' => 135,
            'name' => 'ทบทวนการดำเนินงานในข้อ 1, 2 และ 3 และนำมาปรับปรุงแผนและกระบวนการสร้างความผูกพัน',
            'description' => NULL,
            'sequence' => 4,
            'indicator_id' => 36,
            'status' => 0,
            'report' => NULL,
        ),
        95 => 
        array (
            'id' => 136,
            'name' => 'ผลการประเมินการบริหารงานด้วยหลักธรรมาภิบาลหรือการดำเนินการตามหลักคุณธรรมและ ความโปร่งใสของการดำเนินงานอย่างครบถ้วนทั้ง 10 ประการ',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 37,
            'status' => 0,
            'report' => NULL,
        ),
        96 => 
        array (
            'id' => 137,
            'name' => 'มีผลลัพธ์ของความสำเร็จของความร่วมมือฯระดับชาติหรือนานาชาติ',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 38,
            'status' => 0,
            'report' => NULL,
        ),
        97 => 
        array (
            'id' => 138,
            'name' => 'มีผลลัพธ์ของความสำเร็จของความร่วมมือฯระดับชาติและนานาชาติ',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 38,
            'status' => 0,
            'report' => NULL,
        ),
        98 => 
        array (
            'id' => 139,
            'name' => 'มีการประเมินผลลัพธ์ความสำเร็จของการดำเนินการตามแผนกลยุทธ์/ยุทธศาสตร์',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 39,
            'status' => 0,
            'report' => NULL,
        ),
        99 => 
        array (
            'id' => 140,
            'name' => 'การประเมินผลการใช้จ่ายตามแผนการจัดสรรงบประมาณที่กำหนดไว้',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 40,
            'status' => 0,
            'report' => NULL,
        ),
        100 => 
        array (
            'id' => 141,
        'name' => 'ผลของการดำเนินงานบริหารและพัฒนาอาจารย์ มีการส่งเสริมให้อาจารย์มีความรักและผูกพันในองค์กร ส่งผลให้อาจารย์มีการปฏิบัติงานตามวิชาชีพในองค์กรอย่างต่อเนื่อง ข้อมูล 3 ปีย้อนหลัง (การสูญเสียไม่นับรวมการเสียชีวิต หรือการเกษียณอายุ หรือสถาบันประเมินให้ออก/ไม่ต่อสัญญา)',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 41,
            'status' => 0,
            'report' => NULL,
        ),
        101 => 
        array (
            'id' => 142,
        'name' => 'ผลของการดำเนินงานบริหารและพัฒนาบุคลากรสายสนับสนุน มีการส่งเสริมให้เกิดความรักและผูกพันในองค์กร ส่งผลให้มีการปฏิบัติงานในองค์กรอย่างต่อเนื่อง ข้อมูล 3 ปีย้อนหลัง (การสูญเสียไม่นับรวมการเสียชีวิต หรือการเกษียณอายุ หรือสถาบันประเมินให้ออก/ไม่ต่อสัญญา)',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 42,
            'status' => 0,
            'report' => NULL,
        ),
        102 => 
        array (
            'id' => 158,
            'name' => 'ร้อยละของอาจารย์พยาบาลประจำที่ปฏิบัติการพยาบาลในสาขาวิชาที่รับผิดชอบ อย่างน้อย 80 ชั่วโมง/ปีการศึกษา ย้อนหลัง 3 ปี โดยเริ่มตั้งแต่ปีการศึกษา 2568 เป็นต้นไป',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 53,
            'status' => 0,
            'report' => NULL,
        ),
        103 => 
        array (
            'id' => 159,
            'name' => 'มีการบูรณาการศิลปวัฒนธรรม/ ภูมิปัญญาไทยกับพันธกิจอื่นของสถาบันการศึกษา "การบูรณาการกับการเรียนการสอนในรายวิชาที่เกี่ยวข้อง"',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 54,
            'status' => 0,
            'report' => NULL,
        ),
        104 => 
        array (
            'id' => 160,
            'name' => 'มีการบูรณาการศิลปวัฒนธรรม/ ภูมิปัญญาไทยกับพันธกิจอื่นของสถาบันการศึกษา "การบูรณาการกับการเรียนการสอนในรายวิชาที่เกี่ยวข้องและการวิจัยหรือการบริการวิชาการ"',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 54,
            'status' => 0,
            'report' => NULL,
        ),
        105 => 
        array (
            'id' => 144,
        'name' => 'ร้อยละนักศึกษาชั้นปีสุดท้ายที่มีผลลัพธ์การเรียนรู้ครบตามที่กำหนดไว้ในหลักสูตรประเมินโดยนักศึกษาและอาจารย์ผู้รับผิดชอบหลักสูตรประเมิน (PLOs)',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 44,
            'status' => 0,
            'report' => NULL,
        ),
        106 => 
        array (
            'id' => 145,
            'name' => 'ร้อยละของผู้สอบความรู้ขอขึ้นทะเบียนรับใบอนุญาตประกอบวิชาชีพการพยาบาลและการผดุงครรภ์ผ่านในครั้งแรกของจำนวนผู้สำเร็จการศึกษาในปีการศึกษานั้น',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 45,
            'status' => 0,
            'report' => NULL,
        ),
        107 => 
        array (
            'id' => 146,
            'name' => 'ร้อยละของผู้สอบความรู้ขอขึ้นทะเบียนรับใบอนุญาตประกอบวิชาชีพการพยาบาลและการผดุงครรภ์ผ่านในปีแรกของจำนวนผู้สำเร็จการศึกษาในปีการศึกษานั้น',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 46,
            'status' => 0,
            'report' => NULL,
        ),
        108 => 
        array (
            'id' => 147,
            'name' => 'มีการดำเนินการประกันคุณภาพการศึกษาภายในระดับหลักสูตรตามที่สภาสถาบันอุดมศึกษากำหนด',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 47,
            'status' => 0,
            'report' => NULL,
        ),
        109 => 
        array (
            'id' => 148,
            'name' => 'มีการประกันคุณภาพการศึกษาภายในระดับหลักสูตรตามเกณฑ์มาตรฐานระดับสากลที่สภาสถาบันอุดมศึกษากำหนดโดยผู้ประเมินภายนอกมหาวิทยาลัยร่วมด้วยและได้ผลการประเมินระดับดีขึ้นไป',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 47,
            'status' => 0,
            'report' => NULL,
        ),
        110 => 
        array (
            'id' => 149,
            'name' => 'การใช้ห้องปฏิบัติการพยาบาล',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 48,
            'status' => 0,
            'report' => NULL,
        ),
        111 => 
        array (
            'id' => 150,
            'name' => 'การเข้าถึงข้อมูลทางอินเทอร์เน็ต',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 48,
            'status' => 0,
            'report' => NULL,
        ),
        112 => 
        array (
            'id' => 151,
            'name' => 'อาคาร สถานที่ และสภาพแวดล้อมเอื้อต่อการเรียนรู้ สะอาด สวยงาม ถูกสุขอนามัย ปลอดภัย',
            'description' => NULL,
            'sequence' => 3,
            'indicator_id' => 48,
            'status' => 0,
            'report' => NULL,
        ),
        113 => 
        array (
            'id' => 152,
            'name' => 'สิ่งสนับสนุนการเรียนรู้ด้วยตนเองของผู้เรียน',
            'description' => NULL,
            'sequence' => 4,
            'indicator_id' => 48,
            'status' => 0,
            'report' => NULL,
        ),
        114 => 
        array (
            'id' => 153,
            'name' => 'การให้บริการในการใช้หนังสือ ตำรา วารสาร ของผู้เรียนและอาจารย์',
            'description' => NULL,
            'sequence' => 5,
            'indicator_id' => 48,
            'status' => 0,
            'report' => NULL,
        ),
        115 => 
        array (
            'id' => 156,
            'name' => 'มีผลงานวิชาการของสถาบันการศึกษา/อาจารย์ประจำที่ได้รับการจดทะเบียน ลิขสิทธิ์  ภายใน 5 ปี ย้อนหลัง',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 51,
            'status' => 0,
            'report' => NULL,
        ),
        116 => 
        array (
            'id' => 157,
            'name' => 'การดำเนินงานด้านบริการวิชาการแก่สังคมทุกโครงการต้องมีการประเมินผลลัพธ์ของโครงการและแผนงาน เพื่อนำข้อมูลมาใช้ในการวางแผนการดำเนินงานในปีต่อไป "ผลลัพธ์บรรลุวัตถุประสงค์และเป้าหมายของโครงการ "',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 52,
            'status' => 0,
            'report' => NULL,
        ),
        117 => 
        array (
            'id' => 161,
            'name' => 'มีการบูรณาการศิลปวัฒนธรรม/ ภูมิปัญญาไทยกับพันธกิจอื่นของสถาบันการศึกษา "การบูรณาการกับการเรียนการสอนในรายวิชาที่เกี่ยวข้องและการวิจัยและการบริการวิชาการ"',
            'description' => NULL,
            'sequence' => 3,
            'indicator_id' => 54,
            'status' => 0,
            'report' => NULL,
        ),
        118 => 
        array (
            'id' => 163,
            'name' => 'ศิษย์เก่ามีส่วนร่วมในการดำเนินกิจกรรมด้านการพัฒนาวิชาการ/วิชาชีพของสถาบัน',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 56,
            'status' => 0,
            'report' => NULL,
        ),
        119 => 
        array (
            'id' => 164,
            'name' => 'มีแผนงานสร้างความสัมพันธ์กับศิษย์เก่าและดำเนินการตามแผน',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 56,
            'status' => 0,
            'report' => NULL,
        ),
        120 => 
        array (
            'id' => 165,
            'name' => 'การจัดตั้งชมรม/สมาคมศิษย์เก่าของสถาบันการศึกษา',
            'description' => NULL,
            'sequence' => 3,
            'indicator_id' => 56,
            'status' => 0,
            'report' => NULL,
        ),
        121 => 
        array (
            'id' => 24,
            'name' => 'มีระบบสารสนเทศเพื่อการตัดสินใจ ด้านการทำนุบำรุงศิลปะและวัฒนธรรม',
            'description' => NULL,
            'sequence' => 5,
            'indicator_id' => 9,
            'status' => 0,
            'report' => NULL,
        ),
        122 => 
        array (
            'id' => 168,
            'name' => 'มีประสบการณ์ด้านการสอนในสถาบันการศึกษาพยาบาลมาแล้วไม่น้อยกว่า 5 ปี',
            'description' => 'หลักฐานแสดงคุณสมบัติของผู้บริหารสูงสุดและผู้บริหารระดับรองในปีการศึกษาที่ปฏิบัติงานในช่วงเวลาของการประเมินเพื่อการรับรองสถาบัน',
            'sequence' => 3,
            'indicator_id' => 1,
            'status' => 0,
            'report' => NULL,
        ),
        123 => 
        array (
            'id' => 169,
            'name' => 'ดํารงตําแหน่งบริหารในสถาบันการศึกษาพยาบาล ไม่น้อยกว่า 2 ปี',
            'description' => 'หลักฐานแสดงคุณสมบัติของผู้บริหารสูงสุดและผู้บริหารระดับรองในปีการศึกษาที่ปฏิบัติงานในช่วงเวลาของการประเมินเพื่อการรับรองสถาบัน',
            'sequence' => 4,
            'indicator_id' => 1,
            'status' => 0,
            'report' => NULL,
        ),
        124 => 
        array (
            'id' => 166,
            'name' => 'มีใบอนุญาตเป็นผู้ประกอบวิชาชีพการพยาบาลและการผดุงครรภ์ ชั้นหนึ่ง ที่เป็นปัจจุบัน และเป็นผู้ปฏิบัติงานประจําเต็มเวลาของสถาบันการศึกษานั้น',
            'description' => 'หลักฐานแสดงคุณสมบัติของผู้บริหารสูงสุดและผู้บริหารระดับรองในปีการศึกษาที่ปฏิบัติงานในช่วงเวลาของการประเมินเพื่อการรับรองสถาบัน',
            'sequence' => 1,
            'indicator_id' => 1,
            'status' => 0,
            'report' => NULL,
        ),
        125 => 
        array (
            'id' => 171,
            'name' => 'สถาบันการศึกษามีอาจารย์ผู้รับผิดชอบหลักสูตรที่มีคุณสมบัติครบถ้วนตรงตามเกณฑ์มาตรฐานหลักสูตรครบทั้ง 5 สาขาหลัก ตลอดเวลาที่ใช้หลักสูตร',
            'description' => 'หลักฐาน/เอกสารแสดงรายชื่ออาจารย์ผู้รับผิดชอบหลักสูตรทั้งหมด คุณวุฒิทางการศึกษา ตําแหน่งทางวิชาการ และผลงานทางวิชาการที่ไม่ใช่ส่วนหนึ่งของการศึกษาเพื่อรับปริญญา และเป็นผลงานทางวิชาการที่ได้รับการเผยแพร่ตามหลักเกณฑ์ที่กําหนดในการพิจารณาแต่งตั้งให้บุคคลดํารงตําแหน่งทางวิชาการในรอบ 5 ปีย้อนหลัง',
            'sequence' => 1,
            'indicator_id' => 4,
            'status' => 0,
            'report' => NULL,
        ),
        126 => 
        array (
            'id' => 170,
            'name' => 'ผู้บริหารระดับรอง มีคุณสมบัติครบถ้วนตามข้อบังคับสภาการพยาบาลว่าด้วยหลักเกณฑ์การรับรองสถาบันฯ',
            'description' => 'หลักฐานแสดงคุณสมบัติของผู้บริหารสูงสุดและผู้บริหารระดับรองในปีการศึกษาที่ปฏิบัติงานในช่วงเวลาของการประเมินเพื่อการรับรองสถาบัน',
            'sequence' => 5,
            'indicator_id' => 1,
            'status' => 0,
            'report' => NULL,
        ),
        127 => 
        array (
            'id' => 27,
            'name' => 'มีหลักฐานแสดงการฝึกความพร้อมของนักศึกษาในการใช้อุปกรณ์/เครื่องมือทางการแพทย์ ที่จำเป็นต่อชีวิตและความปลอดภัยของผู้ป่วย',
            'description' => NULL,
            'sequence' => 3,
            'indicator_id' => 10,
            'status' => 0,
            'report' => NULL,
        ),
        128 => 
        array (
            'id' => 28,
        'name' => 'มีหุ่นจำลองผู้ป่วยเสมือนจริง(patient simulator) อย่างน้อย ได้แก่ หญิงตั้งครรภ์ หุ่นฝึกทำคลอด หุ่นเด็ก และหุ่นผู้ใหญ่ และผู้ป่วยเสมือนจริง (Standardized Patient)',
            'description' => NULL,
            'sequence' => 4,
            'indicator_id' => 10,
            'status' => 0,
            'report' => NULL,
        ),
        129 => 
        array (
            'id' => 29,
            'name' => 'มีหลักฐานการใช้สถานการณ์จำลองเสมือนจริงที่สนับสนุนทักษะการฝึกปฏิบัติการพยาบาลที่ครอบคลุม 5 สาขาวิชาหลัก และสอดรับกับผลลัพธ์การเรียนรู้',
            'description' => NULL,
            'sequence' => 5,
            'indicator_id' => 10,
            'status' => 0,
            'report' => NULL,
        ),
        130 => 
        array (
            'id' => 32,
            'name' => 'มีระบบฐานข้อมูลวารสารการพยาบาลภาษาไทยอิเล็กทรอนิกส์ฉบับเต็ม ให้นิสิต/นักศึกษาเข้าถึงระบบการสืบค้นได้อย่างสะดวก โดยระบุรหัสและรายชื่อวารสารอย่างชัดเจน',
            'description' => NULL,
            'sequence' => 3,
            'indicator_id' => 11,
            'status' => 0,
            'report' => NULL,
        ),
        131 => 
        array (
            'id' => 33,
            'name' => 'มีระบบฐานข้อมูลวารสารการพยาบาลภาษาอังกฤษอิเล็กทรอนิกส์ฉบับเต็มที่ครอบคลุมทั้ง 5 สาขาหลัก ให้นิสิต/นักศึกษาเข้าถึงระบบการสืบค้นได้อย่างสะดวก',
            'description' => NULL,
            'sequence' => 4,
            'indicator_id' => 11,
            'status' => 0,
            'report' => NULL,
        ),
        132 => 
        array (
            'id' => 34,
            'name' => 'มีระบบการสืบค้นทรัพยากรสารสนเทศของห้องสมุดที่ผู้ใช้เข้าถึงได้ง่าย',
            'description' => NULL,
            'sequence' => 5,
            'indicator_id' => 11,
            'status' => 0,
            'report' => NULL,
        ),
        133 => 
        array (
            'id' => 36,
            'name' => 'มีหน่วยงานและบุคลากรสนับสนุนการผลิตงานวิจัยและนวัตกรรม',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 12,
            'status' => 0,
            'report' => NULL,
        ),
        134 => 
        array (
            'id' => 47,
            'name' => 'การกำหนดวิสัยทัศน์ และพันธกิจของสถาบัน มอบหมายผู้รับผิดชอบตามพันธกิจ',
            'description' => NULL,
            'sequence' => 1,
            'indicator_id' => 16,
            'status' => 0,
            'report' => NULL,
        ),
        135 => 
        array (
            'id' => 48,
            'name' => 'การถ่ายทอด/สื่อสารวิสัยทัศน์ พันธกิจ ให้กับบุคลากร นิสิต/นักศึกษา',
            'description' => NULL,
            'sequence' => 2,
            'indicator_id' => 16,
            'status' => 0,
            'report' => NULL,
        ),
        136 => 
        array (
            'id' => 49,
            'name' => 'มีผลการดำเนินงานในการนำองค์กร โดยแสดงผล',
        'description' => '3.1) การรับรู้และเข้าใจของบุคลากร นิสิต/นักศึกษา ต่อวิสัยทัศน์ และพันธกิจ 
3.2) ความพึงพอใจต่อช่องทาง/วิธีการการถ่ายทอด/สื่อสาร',
    'sequence' => 3,
    'indicator_id' => 16,
    'status' => 0,
    'report' => NULL,
),
137 => 
array (
    'id' => 50,
    'name' => 'ทบทวนวิธีการกำหนดวิสัยทัศน์ วิธีการถ่ายทอด/สื่อสาร และปรับปรุงกระบวนการในข้อ 1 และ 2',
    'description' => NULL,
    'sequence' => 4,
    'indicator_id' => 16,
    'status' => 0,
    'report' => NULL,
),
138 => 
array (
    'id' => 57,
    'name' => 'ประเมินการประพฤติตามกฎระเบียบ กฎหมายและจริยธรรมของผู้นำ การกำกับดูแลให้บุคลากรใน องค์กรมีการประพฤติตามกฎหมายและจริยธรรม ครอบคลุม',
    'description' => '- ดำเนินงานตามหลักธรรมาภิบาลของการบริหารกิจการบ้านเมืองที่ดีหรือการดำเนินการตามหลัก คุณธรรมและ ความโปร่งใสของการดำเนินงาน 
- ประเมินการรับรู้ของบุคลากรในเรื่อง การประพฤติปฏิบัติตามกฎระเบียบกฎหมายและจริยธรรม ของผู้นำ 
- การจัดการข้อร้องเรียน อุทธรณ์ ร้องทุกข์',
    'sequence' => 3,
    'indicator_id' => 18,
    'status' => 0,
    'report' => NULL,
),
139 => 
array (
    'id' => 68,
    'name' => 'แผนการพัฒนาความร่วมมือด้านการศึกษา วิจัย บริการวิชาการ หรือทำนุบำรุงศิลปวัฒนธรรมร่วมกับสถาบันอื่น',
    'description' => NULL,
    'sequence' => 1,
    'indicator_id' => 21,
    'status' => 0,
    'report' => NULL,
),
140 => 
array (
    'id' => 70,
    'name' => 'มีการประเมินผลการดำเนินงาน และทบทวนกระบวนการในข้อ 1 และ 2',
    'description' => NULL,
    'sequence' => 3,
    'indicator_id' => 21,
    'status' => 0,
    'report' => NULL,
),
141 => 
array (
    'id' => 71,
    'name' => 'ปรับปรุงกระบวนการดำเนินงาน และการพัฒนาความร่วมมือกับเครือข่าย',
    'description' => NULL,
    'sequence' => 4,
    'indicator_id' => 21,
    'status' => 0,
    'report' => NULL,
),
142 => 
array (
    'id' => 75,
    'name' => 'การเตรียมบุคลากรให้พร้อมรับต่อการเปลี่ยนแปลงในสถานการณ์ปกติและสถานการณ์ที่ไม่คาดหมาย',
    'description' => NULL,
    'sequence' => 4,
    'indicator_id' => 22,
    'status' => 0,
    'report' => NULL,
),
143 => 
array (
    'id' => 76,
    'name' => 'ทบทวนกระบวนการในข้อ 1 ถึง 4 และปรับปรุงกระบวนการ',
    'description' => NULL,
    'sequence' => 5,
    'indicator_id' => 22,
    'status' => 0,
    'report' => NULL,
),
144 => 
array (
    'id' => 18,
    'name' => 'สถานที่สำหรับจัดกิจกรรมเสริมหลักสูตร นันทนาการและการกีฬา',
    'description' => NULL,
    'sequence' => 6,
    'indicator_id' => 8,
    'status' => 0,
    'report' => NULL,
),
145 => 
array (
    'id' => 19,
    'name' => 'มีอุปกรณ์และสิ่งอำนวยความสะดวก และเหมาะสมสำหรับบุคลากรและผู้เรียนที่ต้องการความช่วยเหลือพิเศษ',
    'description' => NULL,
    'sequence' => 7,
    'indicator_id' => 8,
    'status' => 0,
    'report' => NULL,
),
146 => 
array (
    'id' => 96,
    'name' => 'มีการกำกับให้นักศึกษาเกิดผลลัพธ์การเรียนรู้ตามที่หลักสูตรกำหนดร',
    'description' => NULL,
    'sequence' => 5,
    'indicator_id' => 26,
    'status' => 0,
    'report' => NULL,
),
147 => 
array (
    'id' => 98,
    'name' => 'การประเมินผลการเรียนรู้ทั้งภาคทฤษฎีและภาคปฏิบัติสอดคล้องกับที่ได้ออกแบบไว้ในหลักสูตร',
    'description' => NULL,
    'sequence' => 2,
    'indicator_id' => 27,
    'status' => 0,
    'report' => NULL,
),
148 => 
array (
    'id' => 99,
    'name' => 'การกำกับติดตามการจัดกระบวนการเรียนรู้ การประเมินผลทั้งในระดับรายวิชา รายชั้นปี ตลอดหลักสูตรที่สอดคล้องกับที่ได้ออกแบบไว้ในหลักสูตร',
    'description' => NULL,
    'sequence' => 3,
    'indicator_id' => 27,
    'status' => 0,
    'report' => NULL,
),
149 => 
array (
    'id' => 100,
    'name' => 'การดำเนินการให้เกิดนวัตกรรมการเรียนการสอนของอาจารย์ในสถาบัน ที่สอดคล้องกับผลลัพธ์การเรียนรู้ ความต้องการของผู้เรียน อาจารย์ หรือผู้ใช้บัณฑิต',
    'description' => NULL,
    'sequence' => 4,
    'indicator_id' => 27,
    'status' => 0,
    'report' => NULL,
),
150 => 
array (
    'id' => 101,
    'name' => 'การทบทวนกระบวนการในข้อ 1-4 และนำมาปรับปรุงการจัดกระบวนการเรียนรู้และการประเมินผล',
    'description' => NULL,
    'sequence' => 5,
    'indicator_id' => 27,
    'status' => 0,
    'report' => NULL,
),
151 => 
array (
    'id' => 113,
    'name' => 'มีการทบทวนการดำเนินงานในข้อ 1 ถึง 4 และนำมาปรับปรุงแผนและการดำเนินงาน',
    'description' => NULL,
    'sequence' => 5,
    'indicator_id' => 30,
    'status' => 0,
    'report' => NULL,
),
152 => 
array (
    'id' => 123,
    'name' => 'การใช้ความรู้จากการปฏิบัติการพยาบาลเพื่อพัฒนาการเรียนการสอน/การบริการวิชาการ/วิจัย',
    'description' => NULL,
    'sequence' => 3,
    'indicator_id' => 33,
    'status' => 0,
    'report' => NULL,
),
153 => 
array (
    'id' => 124,
    'name' => 'ทบทวนการดำเนินงานในข้อ 1 ถึง 3 และนำมาปรับปรุงการปฏิบัติการพยาบาลของอาจารย์(faculty practice',
        'description' => NULL,
        'sequence' => 4,
        'indicator_id' => 33,
        'status' => 0,
        'report' => NULL,
    ),
    154 => 
    array (
        'id' => 126,
        'name' => 'มีระบบและกลไกการดำเนินงานบูรณาการกับการบริการวิชาการหรือการเรียนการสอนหรือวิจัย',
        'description' => NULL,
        'sequence' => 2,
        'indicator_id' => 34,
        'status' => 0,
        'report' => NULL,
    ),
    155 => 
    array (
        'id' => 127,
        'name' => 'ทบทวนการดำเนินงานในข้อ 1 และ 2 และนำมาปรับปรุงแผนและการดำเนินงานการบูรณาการการเรียนการสอน บริการวิชาการ วิจัย และทำนุบำรุงศิลปวัฒนธรรม',
        'description' => NULL,
        'sequence' => 3,
        'indicator_id' => 34,
        'status' => 0,
        'report' => NULL,
    ),
    156 => 
    array (
        'id' => 16,
        'name' => 'มีอุปกรณ์และระบบความปลอดภัย ระบบป้องกันอัคคีภัย ช่วยชีวิตเบื้องต้น เพียงพอและพร้อมช่วยเหลือในสถานการณ์ฉุกเฉิน',
        'description' => NULL,
        'sequence' => 4,
        'indicator_id' => 8,
        'status' => 0,
        'report' => NULL,
    ),
    157 => 
    array (
        'id' => 143,
        'name' => 'ระดับที่ 1 เป็นผู้มีความรู้ความเข้าใจในศาสตร์ของตนและประยุกต์ใช้ได้ มีความรู้ความเข้าใจในศาสตร์การเรียนรู้เบื้องต้น สามารถออกแบบกิจกรรม จัดบรรยากาศ ใช้ทรัพยากรและสื่อการเรียนรู้ โดยคำนึงถึงผู้เรียนและ ปัจจัยที่ส่งผลต่อการเรียนรู้ สามารถวัดและประเมินผลการเรียนรู้ของผู้เรียนนำผลประเมินมาใช้ปรับปรุงพัฒนา การจัดการเรียนรู้ พัฒนาตนเองอย่างต่อเนื่อง เปิดใจรับฟังความคิดเห็นจากผู้ที่เกี่ยวข้อง และปฏิบัติตามจรรยาบรรณ วิชาชีพอาจารย์ขององค์กร',
        'description' => NULL,
        'sequence' => 1,
        'indicator_id' => 43,
        'status' => 0,
        'report' => NULL,
    ),
    158 => 
    array (
        'id' => 173,
        'name' => 'ระดับที่ 2 เป็นผู้มีคุณภาพการจัดการเรียนการสอนระดับที่ 1 ที่มีความรู้ลึกในศาสตร์ของตน และติดตาม ความก้าวหน้าของความรู้ในศาสตร์อย่างสม่ำเสมอ มีความรู้ความเข้าใจในศาสตร์การเรียนรู้ สามารถจัดการเรียนรู้ ที่เหมาะสมกับกลุ่มผู้เรียน กำกับดูแลและติดตามผลการเรียนรู้ของผู้เรียนอย่างเป็นระบบ ให้คำปรึกษาชี้แนะแก่เพื่อน อาจารย์ในศาสตร์ได้ และส่งเสริมให้เกิดการปฏิบัติตามจรรยาบรรณวิชาชีพอาจารย์ภายในองค์กร',
        'description' => NULL,
        'sequence' => 2,
        'indicator_id' => 43,
        'status' => 0,
        'report' => NULL,
    ),
    159 => 
    array (
        'id' => 155,
        'name' => 'จำนวนตำรา/หนังสือที่ตีพิมพ์เผยแพร่โดยอาจารย์ในสถาบันนั้น ๆ เฉลี่ย 3 ปีย้อนหลัง โดยกำหนดคุณภาพของตำรา/หนังสือ ดังนี้                ตำรา/หนังสือที่มีคุณภาพผ่านตามหลักเกณฑ์การขอตำแหน่งทางวิชาการ แต่ไม่ได้นำมาขอรับการประเมินตำแหน่งทางวิชาการ หรือ                ตำรา/หนังสือที่มีคุณภาพผ่านตามหลักเกณฑ์การขอตำแหน่งทางวิชาการ และเป็นส่วนหนึ่งที่ทำให้ได้รับตำแหน่งทางวิชาการตามที่กำหนด        ตำรา/หนังสือที่มีคุณภาพผ่านตามหลักเกณฑ์การขอตำแหน่งทางวิชาการ แต่ไม่ได้นำมาขอรับการประเมินตำแหน่งทางวิชาการ หรือ              ตำรา/หนังสือที่มีคุณภาพผ่านตามหลักเกณฑ์การขอตำแหน่งทางวิชาการ และเป็นส่วนหนึ่งที่ทำให้ได้รับตำแหน่งทางวิชาการตามที่กำหนด',
        'description' => NULL,
        'sequence' => 1,
        'indicator_id' => 50,
        'status' => 0,
        'report' => NULL,
    ),
    160 => 
    array (
        'id' => 174,
        'name' => 'มีผลงานวิชาการของสถาบันการศึกษา/อาจารย์ประจำที่ได้รับการจดทะเบียน อนุสิทธิบัตรและสิทธิบัตร ภายใน 5 ปี ย้อนหลัง',
        'description' => NULL,
        'sequence' => 2,
        'indicator_id' => 51,
        'status' => 0,
        'report' => NULL,
    ),
    161 => 
    array (
        'id' => 175,
        'name' => 'การดำเนินงานด้านบริการวิชาการแก่สังคมทุกโครงการต้องมีการประเมินผลลัพธ์ของโครงการและแผนงาน เพื่อนำข้อมูลมาใช้ในการวางแผนการดำเนินงานในปีต่อไป " ผลลัพธ์ตอบสนองความต้องการของชุมชนและส่งผลให้ชุมชนเข้มแข็ง"',
        'description' => NULL,
        'sequence' => 2,
        'indicator_id' => 52,
        'status' => 0,
        'report' => NULL,
    ),
    162 => 
    array (
        'id' => 162,
        'name' => 'อยละของบัณฑิตมีอัตลักษณ์/คุณลักษณะพิเศษตามที่สถาบันกำหนดต่อจำนวนบัณฑิตทั้งหมดโดยการประเมินจากผู้ใช้บัณฑิต ภายหลังสำเร็จการศึกษา 1 ปี',
        'description' => NULL,
        'sequence' => 1,
        'indicator_id' => 55,
        'status' => 0,
        'report' => NULL,
    ),
    163 => 
    array (
        'id' => 176,
        'name' => 'ดํารงตําแหน่งบริหารในสถาบันการศึกษาพยาบาล ไม่น้อยกว่า 2 ปี',
        'description' => 'หลักฐานแสดงคุณสมบัติของผู้บริหารสูงสุดและผู้บริหารระดับรองในปีการศึกษาที่ปฏิบัติงานในช่วงเวลาของการประเมินเพื่อการรับรองสถาบัน',
        'sequence' => 4,
        'indicator_id' => 57,
        'status' => 0,
        'report' => NULL,
    ),
    164 => 
    array (
        'id' => 177,
        'name' => 'ผู้บริหารระดับรอง มีคุณสมบัติครบถ้วนตามข้อบังคับสภาการพยาบาลว่าด้วยหลักเกณฑ์การรับรองสถาบันฯ',
        'description' => 'หลักฐานแสดงคุณสมบัติของผู้บริหารสูงสุดและผู้บริหารระดับรองในปีการศึกษาที่ปฏิบัติงานในช่วงเวลาของการประเมินเพื่อการรับรองสถาบัน',
        'sequence' => 5,
        'indicator_id' => 57,
        'status' => 0,
        'report' => NULL,
    ),
    165 => 
    array (
        'id' => 178,
        'name' => 'มีประสบการณ์ด้านการสอนในสถาบันการศึกษาพยาบาลมาแล้วไม่น้อยกว่า 5 ปี',
        'description' => 'หลักฐานแสดงคุณสมบัติของผู้บริหารสูงสุดและผู้บริหารระดับรองในปีการศึกษาที่ปฏิบัติงานในช่วงเวลาของการประเมินเพื่อการรับรองสถาบัน',
        'sequence' => 3,
        'indicator_id' => 57,
        'status' => 0,
        'report' => NULL,
    ),
    166 => 
    array (
        'id' => 179,
        'name' => 'มีใบอนุญาตเป็นผู้ประกอบวิชาชีพการพยาบาลและการผดุงครรภ์ ชั้นหนึ่ง ที่เป็นปัจจุบัน และเป็นผู้ปฏิบัติงานประจําเต็มเวลาของสถาบันการศึกษานั้น',
        'description' => 'หลักฐานแสดงคุณสมบัติของผู้บริหารสูงสุดและผู้บริหารระดับรองในปีการศึกษาที่ปฏิบัติงานในช่วงเวลาของการประเมินเพื่อการรับรองสถาบัน',
        'sequence' => 1,
        'indicator_id' => 57,
        'status' => 0,
        'report' => NULL,
    ),
    167 => 
    array (
        'id' => 180,
        'name' => 'วุฒิการศึกษาไม่ต่ํากว่าปริญญาโททางการพยาบาล วิทยาศาสตร์สุขภาพ การบริหาร การศึกษา หรือมีตําแหน่งทางวิชาการไม่ต่ํากว่ารองศาสตราจารย์สาขาพยาบาลศาสตร์',
        'description' => 'หลักฐานแสดงคุณสมบัติของผู้บริหารสูงสุดและผู้บริหารระดับรองในปีการศึกษาที่ปฏิบัติงานในช่วงเวลาของการประเมินเพื่อการรับรองสถาบัน',
        'sequence' => 2,
        'indicator_id' => 57,
        'status' => 0,
        'report' => NULL,
    ),
    168 => 
    array (
        'id' => 181,
        'name' => 'ร้อยละของอาจารย์ประจําที่มีคุณวุฒิปริญญาเอกทางการพยาบาลไม่น้อยกว่า ร้อยละ 40',
        'description' => 'หลักฐาน/เอกสารแสดงรายชื่ออาจารย์ทุกคน คุณวุฒิ วันที่เริ่มปฏิบัติงาน ณ สถาบันการศึกษานี้',
        'sequence' => 1,
        'indicator_id' => 58,
        'status' => 0,
        'report' => NULL,
    ),
    169 => 
    array (
        'id' => 167,
        'name' => 'วุฒิการศึกษาไม่ต่ํากว่าปริญญาโททางการพยาบาล วิทยาศาสตร์สุขภาพ การบริหาร การศึกษา หรือมีตําแหน่งทางวิชาการไม่ต่ํากว่ารองศาสตราจารย์สาขาพยาบาลศาสตร์',
        'description' => 'หลักฐานแสดงคุณสมบัติของผู้บริหารสูงสุดและผู้บริหารระดับรองในปีการศึกษาที่ปฏิบัติงานในช่วงเวลาของการประเมินเพื่อการรับรองสถาบัน',
        'sequence' => 2,
        'indicator_id' => 1,
        'status' => 0,
        'report' => NULL,
    ),
    170 => 
    array (
        'id' => 154,
        'name' => 'จำนวนผลงานทางวิชาการที่ตีพิมพ์เผยแพร่โดยอาจารย์ในสถาบันนั้น ๆ ที่เป็นไปตามกฏกระทรวง มาตรฐานการขอตำแหน่งทางวิชาการในสถาบันอุดมศึกษา ของกระทรวงการอุดมศึกษา วิทยาศาสตร์ วิจัยและนวัตกรรมต่ออาจารย์ประจำทั้งหมด ร้อยละ 70 ภายใน 5 ปีย้อนหลัง',
    'description' => '(หากเป็นผลงานวิชาการที่เผยแพร่หลัง 24 มิถุนายน 2565 เป็นต้นไปต้องเป็นชื่อแรกหรือผู้ประพันธ์บรรณกิจเท่านั้น)',
        'sequence' => 1,
        'indicator_id' => 49,
        'status' => 0,
        'report' => NULL,
    ),
    171 => 
    array (
        'id' => 6,
        'name' => 'ร้อยละของอาจารย์ประจําที่มีคุณวุฒิปริญญาเอกทางการพยาบาลไม่น้อยกว่า ร้อยละ 40',
        'description' => 'หลักฐาน/เอกสารแสดงรายชื่ออาจารย์ทุกคน คุณวุฒิ วันที่เริ่มปฏิบัติงาน ณ สถาบันการศึกษานี้',
        'sequence' => 1,
        'indicator_id' => 2,
        'status' => 0,
        'report' => NULL,
    ),
    172 => 
    array (
        'id' => 7,
        'name' => 'ร้อยละของอาจารย์ประจําที่มีคุณสมบัติตามเกณฑ์ต่ออาจารย์ประจําทั้งหมด ร้อยละ 85',
        'description' => 'หลักฐาน/เอกสารแสดงรายชื่ออาจารย์ประจําทั้งหมด ที่ระบุ วุฒิทางการศึกษา ปีที่สําเร็จการศึกษาสถาบันที่สําเร็จการศึกษา ตําแหน่งทางวิชาการ ประสบการณ์การปฏิบัติการพยาบาล ประสบการณ์การสอนการพยาบาลใน สถาบันการศึกษา และหลักฐานแสดงระยะเวลาการปฏิบัติงานในสถาบันที่ขอการรับรอง รวมทั้งระบุเลขที่ใบอนุญาตประกอบวิชาชีพฯและเลขที่สมาชิกสภาการพยาบาล

สําหรับอาจารย์ใหม่ให้แสดงหลักฐานการทดสอบความสามารถภาษาอังกฤษ และระบบกลไกการพัฒนาอาจารย์ใหม่ด้วย',
        'sequence' => 1,
        'indicator_id' => 3,
        'status' => 0,
        'report' => NULL,
    ),
    173 => 
    array (
        'id' => 11,
        'name' => 'อัตราส่วนจํานวนอาจารย์ประจําหรือพยาบาลวิชาชีพ ที่ทําหน้าที่สอนภาคปฏิบัติต่อนิสิต/นักศึกษาในการสอนแต่ละรายวิชาของภาคปฏิบัติ',
    'description' => '1.1) ไม่เกิน 1:4 กรณีพยาบาลวิชาชีพปฏิบัติงานประจําในขณะสอนภาคปฏิบัติ
1.2) ไม่เกิน 1:8 กรณีอาจารย์พยาบาลหรือพยาบาลวิชาชีพไม่ปฏิบัติงานประจํา ในขณะสอนภาคปฏิบัติ',
'sequence' => 1,
'indicator_id' => 7,
'status' => 0,
'report' => NULL,
),
        );
        // Filter out entries for indicator 57 and 58 (keep 8)
        $rows = array_values(array_filter($rows, function($r){
            return !isset($r['indicator_id']) || !in_array($r['indicator_id'], [57, 58]);
        }));
        \DB::table('criterias')->insert($rows);

        $sequence = \DB::selectOne("SELECT pg_get_serial_sequence(?, 'id') AS seq", ['criterias']);
        if ($sequence && !empty($sequence->seq)) {
            $maxId = \DB::table('criterias')->max('id');
            $value = $maxId ?? 0;
            $isCalled = $maxId !== null;

            \DB::select("SELECT setval(?, ?, ?)", [$sequence->seq, $value, $isCalled]);
        }


    }
}
