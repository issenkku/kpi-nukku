# พจนานุกรมข้อมูล (Data Dictionary)

เอกสารนี้จัดทำจากไฟล์ migration ภายในระบบ Laravel ณ เวลาปัจจุบัน เพื่ออธิบายตาราง โครงสร้างคอลัมน์ ชนิดข้อมูล การเป็นค่าว่าง ค่าเริ่มต้น และคำอธิบายภาษาไทยอย่างย่อ รวมถึงความสัมพันธ์ระหว่างตารางหลักในระบบประเมิน/รายงาน SAR

หมายเหตุ:
- ชนิดข้อมูลอ้างอิงตาม MySQL/MariaDB ทั่วไปเทียบจากประเภทใน Laravel Schema
- `timestamps` หมายถึงมีคอลัมน์ `created_at`, `updated_at`
- ตาราง pivot คือ ตารางความสัมพันธ์หลายต่อหลาย ไม่มีกุญแจหลักแบบเดี่ยว

---

## departments
Table comments: ตารางเก็บข้อมูลหน่วยงาน/ภาควิชา

| Column | Type | Null | Default | Comments |
|---|---|---|---|---|
| id (PK) | BIGINT UNSIGNED AUTO_INCREMENT | No | - | รหัสหน่วยงาน |
| name | VARCHAR(255) | No | - | ชื่อหน่วยงาน |
| created_at | TIMESTAMP | Yes | null | เวลาสร้าง |
| updated_at | TIMESTAMP | Yes | null | เวลาอัปเดต |

Relations
- 1:N กับ `users` ผ่าน `users.department_id`

---

## users
Table comments: ตารางผู้ใช้ระบบ

| Column | Type | Null | Default | Comments |
|---|---|---|---|---|
| id (PK) | BIGINT UNSIGNED AUTO_INCREMENT | No | - | รหัสผู้ใช้ |
| title | VARCHAR(255) | Yes | null | คำนำหน้าชื่อ |
| first_name | VARCHAR(255) | No | - | ชื่อ |
| last_name | VARCHAR(255) | No | - | นามสกุล |
| positype | VARCHAR(255) | Yes | null | ประเภทตำแหน่ง/สายงาน |
| workline | VARCHAR(255) | Yes | null | สายงาน/กลุ่มงาน |
| posi | VARCHAR(255) | Yes | null | ตำแหน่ง |
| level | VARCHAR(255) | Yes | null | ระดับตำแหน่ง |
| email | VARCHAR(255) UNIQUE | No | - | อีเมลสำหรับเข้าสู่ระบบ |
| phone | VARCHAR(20) | Yes | null | เบอร์โทร |
| password | VARCHAR(255) | No | - | รหัสผ่าน (แฮช) |
| status | BOOLEAN | No | 1 | สถานะผู้ใช้ (1=Active) |
| email_verified_at | TIMESTAMP | Yes | null | เวลายืนยันอีเมล |
| remember_token | VARCHAR(100) | Yes | null | โทเคนจำการเข้าสู่ระบบ |
| created_at | TIMESTAMP | Yes | null | เวลาสร้าง |
| updated_by | TIMESTAMP | Yes | null | เวลาอัปเดต |
| department_id (FK) | BIGINT UNSIGNED | No | - | FK ไป `departments.id` (restrict on delete) |

Relations
- N:1 ไป `departments`
- ใช้เป็นผู้สร้าง/แก้ไขใน `sar_reports.created_by`, `sar_reports.updated_by`

---

## standards
Table comments: ตารางเก็บข้อมูลรายการ “มาตรฐาน” ที่ใช้อ้างอิงหมวดหมู่ของตัวชี้วัด

| Column | Type | Null | Default | Comments |
|---|---|---|---|---|
| id (PK) | BIGINT UNSIGNED AUTO_INCREMENT | No | - | รหัสมาตรฐาน |
| name | VARCHAR(255) | No | - | ชื่อมาตรฐาน |

Relations
- 1:N กับ `categories` ผ่าน `categories.standard_id`

---

## categories
Table comments: หมวดหมู่ของตัวชี้วัดภายใต้มาตรฐาน

| Column | Type | Null | Default | Comments |
|---|---|---|---|---|
| id (PK) | BIGINT UNSIGNED AUTO_INCREMENT | No | - | รหัสหมวดหมู่ |
| name | VARCHAR(255) | No | - | ชื่อหมวด |
| max_score | FLOAT(5,2) | Yes | null | คะแนนเต็มของหมวด (ถ้ามี) |
| standard_id (FK) | BIGINT UNSIGNED | No | - | FK ไป `standards.id` (restrict on delete) |

Relations
- N:1 ไป `standards`
- 1:N กับ `indicators` (ผ่าน `indicators.categorie_id`)

---

## indicators
Table comments: ตัวชี้วัด (Indicator) ในแต่ละหมวด/มาตรฐาน

| Column | Type | Null | Default | Comments |
|---|---|---|---|---|
| id (PK) | BIGINT UNSIGNED AUTO_INCREMENT | No | - | รหัสตัวชี้วัด |
| name | VARCHAR(255) | No | - | ชื่อตัวชี้วัด |
| year | VARCHAR(5) | Yes | null | ปีการประเมิน/ปีงบประมาณ |
| code | VARCHAR(100) | No | - | รหัสตัวชี้วัด |
| type | VARCHAR(255) | Yes | null | ประเภทตัวชี้วัด |
| description | TEXT | Yes | null | คำอธิบาย |
| condition | TEXT | Yes | null | เงื่อนไขการประเมิน |
| annotation | TEXT | Yes | null | หมายเหตุเพิ่มเติม |
| deadline | DATE | No | - | วันสิ้นสุดส่งหลักฐาน/รายงาน |
| status | INT | No | 0 | สถานะ (เช่น 0=Draft) |
| comment | TEXT | Yes | null | ความเห็น/หมายเหตุ |
| score_acc | FLOAT(5,2) | Yes | null | คะแนนสะสม |
| max_score | FLOAT(5,2) | Yes | null | คะแนนเต็มของตัวชี้วัด |
| created_at | TIMESTAMP | Yes | null | เวลาสร้าง |
| updated_at | TIMESTAMP | Yes | null | เวลาอัปเดต |
| categorie_id (FK) | BIGINT UNSIGNED | No | - | FK ไป `categories.id` (restrict on delete) |

Relations
- N:1 ไป `categories`
- 1:N ไป `criterias`, `variables`, `formulas`, `checklist_items`, `assignments`
- ถูกอ้างถึงใน `sar_reports.indicator_id`

---

## criterias
Table comments: เกณฑ์พิจารณาของตัวชี้วัด

| Column | Type | Null | Default | Comments |
|---|---|---|---|---|
| id (PK) | BIGINT UNSIGNED AUTO_INCREMENT | No | - | รหัสเกณฑ์ |
| name | TEXT | No | - | ชื่อเกณฑ์ |
| description | TEXT | Yes | null | คำอธิบายเกณฑ์ |
| sequence | INT | No | - | ลำดับแสดงผล |
| indicator_id (FK) | BIGINT UNSIGNED | No | - | FK ไป `indicators.id` (cascade on delete) |
| status | INT | No | 0 | สถานะ |
| report | TEXT | Yes | null | ผลการประเมิน/รายงานประกอบเกณฑ์ |

Relations
- N:1 ไป `indicators`
- 1:N ไป `evidence`
- ถูกอ้างถึงใน `sar_reports.criteria_id`

---

## evidence
Table comments: หลักฐานประกอบเกณฑ์/ตัวชี้วัด

| Column | Type | Null | Default | Comments |
|---|---|---|---|---|
| id (PK) | BIGINT UNSIGNED AUTO_INCREMENT | No | - | รหัสหลักฐาน |
| name | TEXT | No | - | ชื่อไฟล์/ชื่อรายการ |
| path | TEXT | No | - | ที่อยู่ไฟล์/ลิงก์ |
| type | VARCHAR(255) | Yes | null | ประเภทไฟล์/ชนิดหลักฐาน |
| detail | TEXT | Yes | null | รายละเอียดเพิ่มเติม |
| status | BOOLEAN | No | 0 | สถานะการยืนยันหลักฐาน |
| created_at | TIMESTAMP | Yes | null | เวลาสร้าง |
| updated_at | TIMESTAMP | Yes | null | เวลาอัปเดต |
| criteria_id (FK) | BIGINT UNSIGNED | No | - | FK ไป `criterias.id` (cascade on delete) |
| user_id (FK) | BIGINT UNSIGNED | Yes | null | ผู้แนบ (FK ไป `users.id`, null on delete) |

Relations
- N:1 ไป `criterias`
- N:1 ไป `users` (ผู้แนบ)
- N:N กับ `sar_reports` ผ่าน `sar_report_evidence`

---

## variables
Table comments: ตัวแปรสำหรับคำนวณคะแนน/ตรรกะของตัวชี้วัด

| Column | Type | Null | Default | Comments |
|---|---|---|---|---|
| id (PK) | BIGINT UNSIGNED AUTO_INCREMENT | No | - | รหัสตัวแปร |
| variable_name | VARCHAR(255) | No | - | ชื่อตัวแปร (เช่น x, y) |
| label_name | VARCHAR(255) | No | - | ป้ายชื่อแสดงผล |
| type | VARCHAR(50) | No | - | ชนิดค่า (number, text ฯลฯ) |
| value | FLOAT(5,2) | Yes | null | ค่าเริ่มต้น/ค่าปัจจุบัน |
| indicator_id (FK) | BIGINT UNSIGNED | No | - | FK ไป `indicators.id` (cascade on delete) |
| created_at | TIMESTAMP | Yes | null | เวลาสร้าง |
| updated_at | TIMESTAMP | Yes | null | เวลาอัปเดต |

Relations
- N:1 ไป `indicators`
- N:N กับ `formulas` ผ่าน `variable_formulas`

---

## formulas
Table comments: สูตร/เงื่อนไขการคำนวณของตัวชี้วัด

| Column | Type | Null | Default | Comments |
|---|---|---|---|---|
| id (PK) | BIGINT UNSIGNED AUTO_INCREMENT | No | - | รหัสสูตร |
| condition | TEXT | No | - | นิพจน์/เงื่อนไขสูตร |
| indicator_id (FK) | BIGINT UNSIGNED | No | - | FK ไป `indicators.id` (cascade on delete) |
| created_at | TIMESTAMP | Yes | null | เวลาสร้าง |
| updated_at | TIMESTAMP | Yes | null | เวลาอัปเดต |

Relations
- N:1 ไป `indicators`
- N:N กับ `variables` ผ่าน `variable_formulas`

---

## variable_formulas (pivot)
Table comments: Pivot เชื่อมตัวแปรกับสูตร (หลายต่อหลาย)

| Column | Type | Null | Default | Comments |
|---|---|---|---|---|
| variable_id (FK, PK part) | BIGINT UNSIGNED | No | - | FK ไป `variables.id` |
| formula_id (FK, PK part) | BIGINT UNSIGNED | No | - | FK ไป `formulas.id` |

---

## checklist_items
Table comments: รายการตรวจสอบ/หัวข้อย่อยของตัวชี้วัด

| Column | Type | Null | Default | Comments |
|---|---|---|---|---|
| id (PK) | BIGINT UNSIGNED AUTO_INCREMENT | No | - | รหัสรายการตรวจสอบ |
| required_items | JSON | Yes | null | รายการสิ่งที่ต้องมี |
| score | FLOAT(5,2) | Yes | null | คะแนนของรายการ |
| description | TEXT | Yes | null | คำอธิบายเพิ่มเติม (เพิ่มภายหลัง) |
| sequence | INT | Yes | null | ลำดับแสดงผล |
| indicator_id (FK) | BIGINT UNSIGNED | No | - | FK ไป `indicators.id` (cascade on delete) |

Relations
- N:1 ไป `indicators`

---

## assignments (pivot)
Table comments: กำหนดผู้รับผิดชอบรวบรวมหลักฐานของตัวชี้วัด

| Column | Type | Null | Default | Comments |
|---|---|---|---|---|
| indicator_id (FK, PK part) | BIGINT UNSIGNED | No | - | FK ไป `indicators.id` |
| collector (FK, PK part) | BIGINT UNSIGNED | No | - | ผู้รับผิดชอบ (FK ไป `users.id`) |

---

## settings
Table comments: การตั้งค่าระบบ/ตั้งค่าการแจ้งเตือน

| Column | Type | Null | Default | Comments |
|---|---|---|---|---|
| id (PK) | BIGINT UNSIGNED AUTO_INCREMENT | No | - | รหัสตั้งค่า |
| title | VARCHAR(255) | Yes | null | ชื่อรอบ/ชื่อการแจ้ง |
| notify_date1 | DATE | Yes | null | วันที่แจ้งเตือนครั้งที่ 1 |
| notify_time1 | VARCHAR(5) | Yes | null | เวลาแจ้งเตือนครั้งที่ 1 (HH:MM) |
| notify_date2 | DATE | Yes | null | วันที่แจ้งเตือนครั้งที่ 2 |
| notify_time2 | VARCHAR(5) | Yes | null | เวลาแจ้งเตือนครั้งที่ 2 (HH:MM) |
| message | VARCHAR(500) | Yes | null | ข้อความแจ้งเตือน |
| remind_days | VARCHAR(50) | Yes | null | วันล่วงหน้าที่เตือน เช่น "7,3,1" |
| remind_time | VARCHAR(5) | Yes | null | เวลาเตือน (HH:MM) |
| remind_enabled | BOOLEAN | No | 0 | เปิดใช้งานการเตือนอัตโนมัติ |
| created_at | TIMESTAMP | Yes | null | เวลาสร้าง |
| updated_at | TIMESTAMP | Yes | null | เวลาอัปเดต |

---

## sar_reports
Table comments: บันทึกรายงาน SAR ต่อมาตรฐาน/ตัวชี้วัด/เกณฑ์

| Column | Type | Null | Default | Comments |
|---|---|---|---|---|
| id (PK) | BIGINT UNSIGNED AUTO_INCREMENT | No | - | รหัสรายงาน |
| year | INT | No | - | ปีของรายงาน |
| standard_id (FK) | BIGINT UNSIGNED | No | - | FK ไป `standards.id` |
| indicator_id (FK) | BIGINT UNSIGNED | No | - | FK ไป `indicators.id` |
| criteria_id (FK) | BIGINT UNSIGNED | No | - | FK ไป `criterias.id` |
| section1 | LONGTEXT | Yes | null | เนื้อหา ส่วนที่ 1 |
| section2 | LONGTEXT | Yes | null | เนื้อหา ส่วนที่ 2 |
| section4 | LONGTEXT | Yes | null | เนื้อหา ส่วนที่ 4 |
| title | VARCHAR(255) | Yes | null | ชื่อเรื่องของรายงาน (เพิ่มภายหลัง) |
| created_by (FK) | BIGINT UNSIGNED | No | - | ผู้สร้าง (FK ไป `users.id`) |
| updated_by (FK) | BIGINT UNSIGNED | Yes | null | ผู้แก้ไขล่าสุด (FK ไป `users.id`, null on delete) |
| created_at | TIMESTAMP | Yes | null | เวลาสร้าง |
| updated_at | TIMESTAMP | Yes | null | เวลาอัปเดต |

Relations
- N:1 ไป `standards`, `indicators`, `criterias`, `users`
- N:N กับ `evidence` ผ่าน `sar_report_evidence`

---

## sar_report_evidence (pivot)
Table comments: Pivot เชื่อมรายงาน SAR กับหลักฐานที่แนบ

| Column | Type | Null | Default | Comments |
|---|---|---|---|---|
| id (PK) | BIGINT UNSIGNED AUTO_INCREMENT | No | - | รหัสรายการเชื่อม |
| sar_report_id (FK) | BIGINT UNSIGNED | No | - | FK ไป `sar_reports.id` |
| evidence_id (FK) | BIGINT UNSIGNED | No | - | FK ไป `evidence.id` |
| created_at | TIMESTAMP | Yes | null | เวลาสร้าง |
| updated_at | TIMESTAMP | Yes | null | เวลาอัปเดต |

---

## ระบบสิทธิ์ (Spatie Permission)
ตารางที่ได้จากแพ็กเกจสิทธิ์การใช้งาน ใช้โครงสร้างมาตรฐาน ได้แก่ `permissions`, `roles`, `model_has_permissions`, `model_has_roles`, `role_has_permissions` ซึ่งเก็บชื่อสิทธิ์ บทบาท และความสัมพันธ์กับโมเดล `users` (รายละเอียดตามไฟล์ migration ของแพ็กเกจ)

---

## ตารางระบบทั่วไป
- `sessions`, `password_reset_tokens`, `cache`, `jobs`, `personal_access_tokens` เป็นตารางสนับสนุนของ Laravel ใช้ตามโครงสร้างมาตรฐาน ไม่เกี่ยวกับโดเมนโดยตรง

---

อัปเดตล่าสุดจากไฟล์ migration ในโฟลเดอร์ `database/migrations` ณ เวลาสร้างเอกสารนี้
