<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Standard;
use App\Models\Category;
use App\Models\Indicator;
use App\Models\User;
use App\Models\Department;
use App\Models\Assignment;
use Illuminate\Support\Facades\Hash;

class ComprehensiveDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔍 กำลังตรวจสอบข้อมูลที่มีอยู่แล้ว...');
        
        // ========== 1. ดึง Standards และ Categories ที่มีอยู่แล้ว ==========
        // กำหนดจำนวน indicators ให้รวมเป็น 56 ตัว/ปี โดยแบ่งตามสัดส่วนคะแนน
        $standardsConfig = [
            'มาตรฐานโครงสร้าง' => [
                ['name' => 'ด้านองค์กรและการบริหารองค์กร', 'indicators' => 8, 'max_per_indicator' => 13.125], // 105/8
                ['name' => 'ด้านบุคลากร', 'indicators' => 7, 'max_per_indicator' => 14.286], // 100/7
                // รวม: 15 ตัวชี้วัด = 205 คะแนน
            ],
            'มาตรฐานกระบวนการ' => [
                ['name' => 'ด้านการจัดการศึกษา', 'indicators' => 25, 'max_per_indicator' => 11.8], // 295/25
                // รวม: 25 ตัวชี้วัด = 295 คะแนน
            ],
            'มาตรฐานผลลัพธ์' => [
                ['name' => 'ด้านการวิจัยและนวัตกรรมและผลผลิตทางวิชาการ', 'indicators' => 4, 'max_per_indicator' => 17.5], // 70/4
                ['name' => 'ด้านการบริการวิชาการ/วิชาชีพแก่สังคม', 'indicators' => 5, 'max_per_indicator' => 15.0], // 75/5
                ['name' => 'ด้านการทำนุบำรุงศิลปะและวัฒนธรรม', 'indicators' => 3, 'max_per_indicator' => 10.0], // 30/3
                ['name' => 'ด้านนิสิตและนักศึกษา', 'indicators' => 4, 'max_per_indicator' => 16.25], // 65/4
                // รวม: 16 ตัวชี้วัด = 240 คะแนน
            ]
            // รวมทั้งหมด: 56 ตัวชี้วัด = 740 คะแนน
        ];

        // ========== 2. ดึง Departments ที่มีอยู่แล้ว และสร้างใหม่ถ้าไม่มี ==========
        $this->command->info('🏢 กำลังเตรียม Departments...');
        
        $allDepartments = Department::all();
        $departments = [];
        
        if ($allDepartments->count() >= 5) {
            // ใช้ departments ที่มีอยู่แล้ว 5 อันแรก
            $departments = $allDepartments->take(5)->values()->all();
            $this->command->info('   ✓ ใช้ Departments ที่มีอยู่แล้ว: ' . $allDepartments->count() . ' แห่ง');
        } else {
            // สร้าง departments ใหม่ถ้าไม่พอ
            $departmentNames = [
                'สำนักงานคณบดี',
                'ภาควิชาการพยาบาลผู้ใหญ่',
                'ภาควิชาการพยาบาลเด็กและวัยรุ่น',
                'ภาควิชาการพยาบาลสูติศาสตร์-นรีเวชวิทยา',
                'ภาควิชาการพยาบาลจิตเวชและสุขภาพจิต',
            ];
            
            foreach ($departmentNames as $deptName) {
                $dept = Department::where('name', $deptName)->first();
                if (!$dept) {
                    $dept = Department::create(['name' => $deptName]);
                    $this->command->info("   + สร้าง Department ใหม่: {$deptName}");
                }
                $departments[] = $dept;
            }
        }

        // ========== 3. สร้าง Users (5 คน) ==========
        $this->command->info('👥 กำลังเตรียม Users...');
        
        $users = [
            [
                'title' => 'ผศ.ดร.',
                'first_name' => 'สมหญิง',
                'last_name' => 'ใจดี',
                'email' => 'user2@example.com',
                'phone' => '081-234-5601',
                'department_id' => $departments[0]->id,
                'password' => Hash::make('password'),
                'status' => true,
            ],
            [
                'title' => 'อ.ดร.',
                'first_name' => 'ประภาส',
                'last_name' => 'วิชาการ',
                'email' => 'user3@example.com',
                'phone' => '081-234-5602',
                'department_id' => $departments[1]->id,
                'password' => Hash::make('password'),
                'status' => true,
            ],
            [
                'title' => 'ผศ.',
                'first_name' => 'วิภา',
                'last_name' => 'รักเด็ก',
                'email' => 'user4@example.com',
                'phone' => '081-234-5603',
                'department_id' => $departments[2]->id,
                'password' => Hash::make('password'),
                'status' => true,
            ],
            [
                'title' => 'อ.',
                'first_name' => 'สุดารัตน์',
                'last_name' => 'มารดา',
                'email' => 'user5@example.com',
                'phone' => '081-234-5604',
                'department_id' => $departments[3]->id,
                'password' => Hash::make('password'),
                'status' => true,
            ],
            [
                'title' => 'ผศ.ดร.',
                'first_name' => 'จิตติมา',
                'last_name' => 'สุขใจ',
                'email' => 'user6@example.com',
                'phone' => '081-234-5605',
                'department_id' => $departments[4]->id,
                'password' => Hash::make('password'),
                'status' => true,
            ],
        ];

        $createdUsers = [];
        foreach ($users as $userData) {
            $user = User::where('email', $userData['email'])->first();
            if (!$user) {
                $user = User::create($userData);
                $this->command->info("   + สร้าง User ใหม่: {$user->email}");
            } else {
                $this->command->info("   ✓ ใช้ User ที่มีอยู่: {$user->email}");
            }
            
            // Assign role 'user' ถ้ายังไม่มี
            if (!$user->hasRole('user')) {
                $user->assignRole('user');
                $this->command->info("      → Assign role 'user' ให้กับ {$user->email}");
            }
            
            $createdUsers[] = $user;
        }

        $this->command->info('✅ เตรียม Users เรียบร้อย: ' . count($createdUsers) . ' คน');

        // ========== 4. สร้าง Indicators สำหรับ 5 ปี ==========
        $this->command->info('📈 กำลังสร้าง Indicators...');
        
        // ลบ indicators และ assignments ที่สร้างจาก seeder นี้ก่อน (code ขึ้นต้นด้วย NCS-, NCP-, NCO-)
        $this->command->warn('🗑️  กำลังลบข้อมูลเก่า...');
        $deletedAssignments = DB::table('assignments')
            ->whereIn('indicator_id', function($query) {
                $query->select('id')
                    ->from('indicators')
                    ->where('code', 'LIKE', 'NCS-%')
                    ->orWhere('code', 'LIKE', 'NCP-%')
                    ->orWhere('code', 'LIKE', 'NCO-%');
            })
            ->delete();
        $this->command->info("   - ลบ Assignments: {$deletedAssignments} รายการ");
        
        $deletedIndicators = DB::table('indicators')
            ->where('code', 'LIKE', 'NCS-%')
            ->orWhere('code', 'LIKE', 'NCP-%')
            ->orWhere('code', 'LIKE', 'NCO-%')
            ->delete();
        $this->command->info("   - ลบ Indicators: {$deletedIndicators} ตัว");
        
        // Reset sequence สำหรับ indicators table
        $maxId = DB::table('indicators')->max('id');
        if ($maxId && $maxId > 0) {
            DB::statement("SELECT setval('indicators_id_seq', {$maxId})");
        } else {
            DB::statement("SELECT setval('indicators_id_seq', 1, false)"); // Reset เป็น 1 โดยไม่ consume
        }
        
        $years = [2021, 2022, 2023, 2024, 2025];
        $indicatorTypes = ['บังคับ', 'เลือก'];

        $codeCounter = 1;
        $allIndicators = [];

        foreach ($standardsConfig as $stdName => $categoriesData) {
            $standard = Standard::where('name', $stdName)->first();
            
            if (!$standard) {
                $this->command->error("   ✗ ไม่พบ Standard: {$stdName}");
                continue;
            }
            
            $this->command->info("   📂 {$stdName}");
            
            foreach ($categoriesData as $catData) {
                // ค้นหา category โดยระบุ standard_id เฉพาะเจาะจง
                $category = Category::where('name', $catData['name'])
                    ->where('standard_id', $standard->id)
                    ->first();

                if (!$category) {
                    $this->command->warn("   ! ไม่พบ Category: {$catData['name']} ใน Standard: {$stdName} (ID: {$standard->id})");
                    continue;
                }
                
                $this->command->info("      ✓ {$catData['name']} ({$catData['indicators']} ตัวชี้วัด)");

                // สร้าง indicators ตามจำนวนที่กำหนด
                for ($i = 1; $i <= $catData['indicators']; $i++) {
                    // กำหนด prefix ตาม standard
                    if ($standard->name === 'มาตรฐานโครงสร้าง') {
                        $prefix = 'NCS';
                    } elseif ($standard->name === 'มาตรฐานกระบวนการ') {
                        $prefix = 'NCP';
                    } else {
                        $prefix = 'NCO';
                    }

                    $code = $prefix . '-' . str_pad($codeCounter, 3, '0', STR_PAD_LEFT);
                    
                    foreach ($years as $year) {
                        // ตรวจสอบว่ามี indicator นี้อยู่แล้วหรือไม่
                        $existingIndicator = Indicator::where('code', $code)
                            ->where('year', $year)
                            ->first();
                        
                        if ($existingIndicator) {
                            // ใช้ indicator ที่มีอยู่แล้ว
                            $allIndicators[] = $existingIndicator;
                            continue;
                        }

                        // คำนวณคะแนนสุ่ม (70-100% ของ max_score)
                        $maxScore = $catData['max_per_indicator'];
                        $minScore = $maxScore * 0.70;
                        $scoreAcc = round(rand($minScore * 100, $maxScore * 100) / 100, 2);

                        // สุ่ม status โดยมี 60% ที่จะเป็น status 3 (เสร็จสมบูรณ์)
                        $statusRand = rand(1, 100);
                        if ($statusRand <= 60) {
                            $status = 3; // เสร็จสมบูรณ์
                        } elseif ($statusRand <= 75) {
                            $status = 2; // รอตรวจสอบ
                        } elseif ($statusRand <= 85) {
                            $status = 1; // กำลังดำเนินการ
                        } elseif ($statusRand <= 95) {
                            $status = 4; // ไม่สมบูรณ์
                        } else {
                            $status = 0; // ไม่เริ่ม
                        }

                        $indicator = Indicator::create([
                            'name' => "ตัวบ่งชี้ที่ {$codeCounter} - {$category->name}",
                            'year' => (string)$year,
                            'code' => $code,
                            'type' => $indicatorTypes[array_rand($indicatorTypes)],
                            'description' => "คำอธิบายตัวบ่งชี้ {$code} สำหรับปีการศึกษา {$year}",
                            'condition' => null,
                            'annotation' => "หมายเหตุสำหรับ {$code}",
                            'deadline' => now()->addMonths(rand(1, 12)),
                            'status' => $status,
                            'comment' => null,
                            'score_acc' => $scoreAcc,
                            'max_score' => $maxScore,
                            'categorie_id' => $category->id,
                        ]);

                        $allIndicators[] = $indicator;
                    }
                    
                    $codeCounter++;
                }
            }
        }

        $this->command->info('✅ สร้าง Indicators เรียบร้อย: ' . count($allIndicators) . ' ตัว');

        // ========== 5. Assign Indicators ให้กับ Users ==========
        $this->command->info('🔄 กำลัง Assign Indicators ให้กับ Users...');
        
        // แบ่ง indicators ให้กับ users อย่างเท่าๆ กัน
        $indicatorsPerUser = intval(count($allIndicators) / count($createdUsers));
        $indicatorChunks = array_chunk($allIndicators, $indicatorsPerUser);

        foreach ($createdUsers as $index => $user) {
            if (!isset($indicatorChunks[$index])) {
                continue;
            }

            foreach ($indicatorChunks[$index] as $indicator) {
                Assignment::firstOrCreate([
                    'indicator_id' => $indicator->id,
                    'collector' => $user->id,
                ]);
            }

            $assignedCount = count($indicatorChunks[$index]);
            $this->command->info("   ✓ {$user->display_name} ได้รับมอบหมาย {$assignedCount} ตัวชี้วัด");
        }

        // Assign indicators ที่เหลือให้กับ user แรก (ถ้ามี)
        $remainingIndicators = array_slice($allIndicators, count($createdUsers) * $indicatorsPerUser);
        if (count($remainingIndicators) > 0 && count($createdUsers) > 0) {
            foreach ($remainingIndicators as $indicator) {
                Assignment::firstOrCreate([
                    'indicator_id' => $indicator->id,
                    'collector' => $createdUsers[0]->id,
                ]);
            }
            $this->command->info("   ✓ มอบหมายตัวชี้วัดที่เหลืออีก " . count($remainingIndicators) . " ตัวให้กับ {$createdUsers[0]->display_name}");
        }

        // ========== สรุปผลลัพธ์ ==========
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->info('📊 สรุปข้อมูลที่สร้าง:');
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->info('👥 Users: ' . count($createdUsers) . ' คน');
        $this->command->info('🏢 Departments: ' . count($departments));
        $this->command->info('📚 Standards: ' . Standard::count());
        $this->command->info('📁 Categories: ' . Category::count());
        $this->command->info('📈 Indicators: ' . count($allIndicators) . ' ตัว (56 ตัว x 5 ปี)');
        $this->command->info('📌 Assignments: ' . Assignment::count());
        
        $this->command->info('');
        $this->command->info('📅 ปีที่มีข้อมูล: ' . implode(', ', $years));
        
        // แสดงคะแนนรวมของแต่ละปี
        foreach ($years as $year) {
            $totalScore = Indicator::where('year', $year)->sum('score_acc');
            $totalMax = Indicator::where('year', $year)->sum('max_score');
            $percentage = $totalMax > 0 ? round(($totalScore / $totalMax) * 100, 2) : 0;
            $this->command->info("   ปี {$year}: {$totalScore}/{$totalMax} คะแนน ({$percentage}%)");
        }
        
        $this->command->info('');
        $this->command->info('🔐 ข้อมูลการเข้าสู่ระบบ:');
        foreach ($createdUsers as $user) {
            $this->command->info("   📧 {$user->email} | รหัสผ่าน: password");
        }
        
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->info('✅ สร้างข้อมูลตัวอย่างเรียบร้อยแล้ว!');
        $this->command->info('═══════════════════════════════════════════════');
    }
}
