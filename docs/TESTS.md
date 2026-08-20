# คู่มือการทดสอบ

ชุดทดสอบใช้ Laravel 12 และ PHPUnit 11 โดยรันบน SQLite แบบ in-memory เพื่อให้แยกจากฐานข้อมูลจริง

## สถานะล่าสุด

- 63 tests
- 255 assertions
- ครอบคลุม authentication, authorization, role matrix, KPI assignment, rich-text sanitization, evidence, CRUD, export และคำสั่งสร้างผู้ดูแลระบบ

จำนวน assertion อาจเพิ่มขึ้นตาม dependency เวอร์ชันใหม่ ให้ยึดผลจากคำสั่งทดสอบเป็นหลัก

## วิธีรัน

เตรียมระบบตาม README และสร้าง `.env` ก่อน จากนั้นรัน:

```bash
php artisan test
```

รันเฉพาะกลุ่ม:

```bash
php artisan test --filter=DashboardKpiAuthorizationTest
php artisan test --filter=RichTextSanitizerTest
```

ตรวจ dependency และ frontend เพิ่มเติม:

```bash
composer audit --locked
npm audit
npm run build
```

## ขอบเขตสำคัญ

- `AccessAndRoutingTest`, `AuthTest`: login/logout และการป้องกัน route
- `RolesMatrixByRoleTest`, `NavbarVisibilityByRoleTest`: สิทธิ์และเมนูของทั้ง 5 roles
- `DashboardKpiAuthorizationTest`: ป้องกันการแก้ KPI/ตัวแปรข้าม assignment
- `DeliverySecurityTest`: route ทดสอบไม่ถูกเปิด และ guest ส่ง notification ไม่ได้
- `SsoSecurityTest`: OAuth state และการระงับบัญชี local เมื่อเข้าสู่ระบบผ่าน SSO
- `CreateSuperAdminCommandTest`: การสร้างผู้ดูแลระบบคนแรก
- `EvidenceTest`: upload, download และสถานะหลักฐาน
- `IndicatorControllerE2eTest`: สร้าง/แก้ตัวบ่งชี้ เกณฑ์ ตัวแปร และ checklist
- `FileExportTest`: export XLSX จริง
- `RichTextSanitizerTest`: ตัด script, event handler และ URL scheme ที่ไม่ปลอดภัย
- CRUD: users, departments, categories และ standards

## ฐานข้อมูลทดสอบ

ค่าใน `phpunit.xml` บังคับใช้ SQLite in-memory, cache/session แบบ array และ queue แบบ sync จึงไม่แตะ PostgreSQL หรือ Redis ใน `.env`

SQL สำหรับเรียงรหัสตัวบ่งชี้รองรับทั้ง PostgreSQL และ SQLite อย่างไรก็ตาม ก่อน release ต้อง smoke test บน staging ที่ใช้ PostgreSQL จริง โดยตรวจ login/SSO, ทั้ง 5 roles, assignment, upload/download/preview, export PDF/DOCX/XLSX, queue, email, scheduler และ backup/restore

## Seeding

เฉพาะ roles/permissions ซึ่งรันซ้ำได้:

```bash
php artisan db:seed --class='Database\Seeders\RolesAndPermissionsSeeder' --force
```

`DatabaseSeeder` มีข้อมูลตัวอย่างและล้างตาราง จึงถูกปิดไม่ให้ทำงานใน production ห้ามใช้กับฐานข้อมูลลูกค้า
