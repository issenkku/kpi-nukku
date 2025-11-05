# Test Coverage Guide (EN)

This document summarizes the automated tests in the repository, how to run them, and what each suite covers.

## Overview

- Framework: Laravel 12, PHPUnit 11
- Total: 53 tests, 218 assertions (current)
- Scope: Authentication, authorization, indicators (API + controller), evidence (upload/download/toggle), users/departments/categories/standards CRUD, exports, resources, and helper services.

Roles/Permissions Matrix
- See Roles Matrix: docs/ROLES_MATRIX.md
- UI visibility tests: tests/Feature/NavbarVisibilityByRoleTest.php


## Running Tests

- Run all: `php artisan test`
- Filter by class: `vendor/bin/phpunit --filter ClassName`
- Example: `vendor/bin/phpunit --filter IndicatorControllerE2eTest --testdox`

Notes
- Web routes use the default session `web` guard in tests (`actingAs($user)`), with sanctum used where routes specify its middleware.
- Roles/permissions are seeded via `RolesAndPermissionsSeeder` where needed (see `NavbarVisibilityByRoleTest`, `RolesMatrixByRoleTest`).
- Indicator list ordering is DB-agnostic: controller sorts in PHP to avoid SQLite vs Postgres differences.

## Data Reset & Seeding

- Reset DB and seed everything (destructive):
  - `php artisan migrate:fresh --seed`
- Seed only roles/permissions (idempotent):
  - `php artisan db:seed --class=Database\\Seeders\\RolesAndPermissionsSeeder`
- Note: The seeder clears Spatie permission cache automatically.

## End-to-End (E2E) Verification

- Indicators flow: `tests/Feature/IndicatorControllerE2eTest.php`
  - Creates indicator with criteria and generates checklist (count-based)
  - Updates indicator: syncs criteria, variables/formula, regenerates checklist
- Permissions flow: `tests/Feature/RolesMatrixByRoleTest.php`
  - Verifies access across key routes for each role
- Navbar visibility: `tests/Feature/NavbarVisibilityByRoleTest.php`
  - Ensures menu items show/hide correctly per role/permission

## Feature Tests

- Access & Routing: `tests/Feature/AccessAndRoutingTest.php` — Guest redirect to login, guest vs. auth access to `/home`.
- Auth: `tests/Feature/AuthTest.php` (login redirect to `/home`, logout redirect `/`), `tests/Feature/AuthAdditionalTest.php` (invalid login 401 JSON).
- Permissions Matrix: `tests/Feature/PermissionsMatrixTest.php` — indicator create, users index, dashboard deny, indicator index.
- Indicators (API): `tests/Feature/IndicatorTest.php` (role-based CRUD via `/api/indicators`), `tests/Feature/IndicatorApiPermissionsAndValidationTest.php` (422/404/guest blocked).
- Indicators (Controller E2E): `tests/Feature/IndicatorControllerE2eTest.php` — store generates combinations; update syncs criteria + regenerates checklist.
- Evidence: `tests/Feature/EvidenceTest.php` — index permission, upload (fake storage), download, toggle status.
- Export: `tests/Feature/FileExportTest.php` (XLSX header), `tests/Feature/ExportGuestTest.php` (guest blocked).
- Factories: `tests/Feature/FactoryTest.php` — IndicatorFactory creates valid row (includes `deadline`, `categorie_id`).
- Basic Indexes: `tests/Feature/BasicIndexesTest.php` — departments, categories, standards require `view-*` permissions.
- Users CRUD: `tests/Feature/UsersCrudTest.php` — create/update/delete with proper permissions.
- Departments CRUD: `tests/Feature/DepartmentsCrudTest.php` — index/create/update/delete with permissions.
- Categories CRUD: `tests/Feature/CategoriesCrudTest.php` — index/create/update/delete (requires an existing standard).
- Standards CRUD: `tests/Feature/StandardsCrudTest.php` — index/create/update/delete with permissions.

## Unit Tests

- Checklist Generation: `tests/Unit/ChecklistGeneratorTest.php` — `kCombinations()`, `syncFromCounts()` no duplicates.
- Indicator Resource: `tests/Unit/IndicatorResourceTest.php` — maps category, standard, criterias, departments, deadline.
- Path Normalization: `tests/Unit/PathNormalizerTest.php` — trims storage/public prefixes, normalizes slashes, handles null/empty inputs.
- Policies: `tests/Unit/IndicatorPolicyTest.php` — create access by role.

## Supporting Code Introduced

- `app/Services/ChecklistGenerator.php`: encapsulates combination generation and checklist syncing (used in `IndicatorController`).
- `app/Support/PathNormalizer.php`: central path normalization (used in `EvidenceController`).
- `database/seeders/RolesAndPermissionsSeeder.php`: seeds roles and permissions in one step.

## Adding New Tests

- Validation: negative cases (duplicates, formats) on all CRUD forms.
- Constraints: unique/index tests (e.g., `indicators.code` if unique enforced).
- API Auth: token-based tests for Sanctum (not session).
- Performance: N+1 detection for heavy listings.

## Troubleshooting

- SQLite vs Postgres: Controller uses PHP-side sorting for indicator list, avoiding DB-specific SQL.
- Permissions: Ensure `permission:*` strings match `routes/web.php`. If seeding manually, run `php artisan db:seed --class=Database\\Seeders\\RolesAndPermissionsSeeder`.


# คู่มือการทดสอบ (TH, อัปเดต)

เอกสารนี้สรุปชุดทดสอบอัตโนมัติ วิธีการรัน ขอบเขตที่ครอบคลุม และคำสั่งสำหรับรีเซ็ต/เตรียมข้อมูลใหม่ทั้งหมด

## ภาพรวม

- Framework: Laravel 12, PHPUnit 11
- รวม: 53 tests, 218 assertions (ปัจจุบัน)
- ขอบเขต: การยืนยันตัวตน/กำหนดสิทธิ์, ตัวชี้วัด (API + Controller), หลักฐาน (อัปโหลด/ดาวน์โหลด/สลับสถานะ), CRUD ผู้ใช้/หน่วยงาน/หมวดหมู่/มาตรฐาน, ส่งออกไฟล์, Resources และ Service helper

เมทริกซ์บทบาท/สิทธิ์
- ดูเอกสาร: `docs/ROLES_MATRIX.md`
- เทสการมองเห็นเมนู: `tests/Feature/NavbarVisibilityByRoleTest.php`

## การรันเทส

- รันทั้งหมด: `php artisan test`
- รันเฉพาะคลาส: `vendor/bin/phpunit --filter ClassName`
- ตัวอย่าง: `vendor/bin/phpunit --filter IndicatorControllerE2eTest --testdox`

หมายเหตุ
- ในเทสใช้ session guard `web` (`actingAs($user)`); กรณีมีเส้นทางที่กำหนด sanctum จะใช้ตาม middleware ที่ตั้งไว้
- การ seed บทบาท/สิทธิ์ทำผ่าน `RolesAndPermissionsSeeder` ในเทสที่เกี่ยวข้อง (เช่น `NavbarVisibilityByRoleTest`, `RolesMatrixByRoleTest`)
- รายการตัวชี้วัด (Indicator list) เรียงลำดับในฝั่ง PHP เพื่อลดความต่างของ SQLite กับ Postgres

## การรีเซ็ตข้อมูลและ Seed ใหม่ทั้งหมด

- รีเซ็ตฐานข้อมูลและ seed ทั้งหมด (ลบข้อมูลเดิม):
  - `php artisan migrate:fresh --seed`
- seed เฉพาะบทบาท/สิทธิ์ (รันซ้ำได้ปลอดภัย):
  - `php artisan db:seed --class=Database\\Seeders\\RolesAndPermissionsSeeder`
- หมายเหตุ: seeder จะล้าง cache ของ Spatie permission ให้อัตโนมัติ

## การยืนยันแบบ End-to-End (E2E)

- กระบวนการตัวชี้วัด: `tests/Feature/IndicatorControllerE2eTest.php`
  - สร้างตัวชี้วัด + เกณฑ์ และสร้าง checklist จากจำนวน (count-based)
  - อัปเดตตัวชี้วัด: sync เกณฑ์ ตัวแปร/สูตร และสร้าง checklist ใหม่
- กระบวนการสิทธิ์การเข้าถึง: `tests/Feature/RolesMatrixByRoleTest.php`
  - ตรวจสอบสิทธิ์การเข้าถึงเส้นทางหลักสำหรับแต่ละบทบาท
- การมองเห็นเมนูบน Navbar: `tests/Feature/NavbarVisibilityByRoleTest.php`
  - ตรวจสอบการแสดง/ซ่อนเมนูตามบทบาท/สิทธิ์

## รายการชุดเทสเด่น

- Access & Routing: `tests/Feature/AccessAndRoutingTest.php` — ตรวจสอบการ redirect และการเข้าถึง `/home`
- Auth: `tests/Feature/AuthTest.php` (ล็อกอินไป `/home`, ออกสู่ระบบไป `/`), `tests/Feature/AuthAdditionalTest.php` (ล็อกอินผิด 401 JSON)
- Permissions Matrix: `tests/Feature/PermissionsMatrixTest.php`
- Indicators (API / Controller): `tests/Feature/IndicatorTest.php`, `tests/Feature/IndicatorApiPermissionsAndValidationTest.php`, `tests/Feature/IndicatorControllerE2eTest.php`
- Evidence: `tests/Feature/EvidenceTest.php`
- Export: `tests/Feature/FileExportTest.php`, `tests/Feature/ExportGuestTest.php`
- CRUD พื้นฐาน: Users / Departments / Categories / Standards

## แนวทางแก้ปัญหา

- ความต่าง SQLite vs Postgres: ใช้การเรียงลำดับใน PHP สำหรับรายการตัวชี้วัด เพื่อลดปัญหา SQL เฉพาะค่าย
- Permissions: ตรวจให้ตรงกับ `routes/web.php` (`permission:*`); หาก seed เองให้รัน `php artisan db:seed --class=Database\\Seeders\\RolesAndPermissionsSeeder`

# คู่มือการทดสอบ (TH)

เอกสารนี้สรุปชุดทดสอบอัตโนมัติ วิธีการรัน และขอบเขตที่ครอบคลุม

## ภาพรวม

- Framework: Laravel 12, PHPUnit 11
- รวม: 43 tests, 138 assertions (ณ เวลาจัดทำ)
- ขอบเขต: ระบบล็อกอิน/สิทธิ์ (Auth/Permissions), Indicators (API + Controller), Evidence (อัปโหลด/ดาวน์โหลด/สลับสถานะ), CRUD สำหรับ Users/Departments/Categories/Standards, Export, Resource mapping และ Service/Helper ที่เกี่ยวข้อง

## สรุปสิทธิ์ 
- ตารางบทบาทและสิทธิ์: docs/ROLES_MATRIX.md
- เทสเมนูตามบทบาท: tests/Feature/NavbarVisibilityByRoleTest.php

## วิธีรันเทสต์

- รันทั้งหมด: `php artisan test`
- รันเฉพาะคลาส: `vendor/bin/phpunit --filter ชื่อคลาส`
- ตัวอย่าง: `vendor/bin/phpunit --filter IndicatorControllerE2eTest --testdox`

หมายเหตุ
- เส้นทางเว็บที่ใช้ `auth:sanctum` ในเทสต์จะใช้ `actingAs($user, 'sanctum')`
- บทบาท/สิทธิ์ (roles/permissions) ถูกสร้างใน `tests/TestCase.php` ตอน setUp
- SQL บางส่วนของ Dashboard เป็นแบบ Postgres จึงทดสอบเฉพาะกรณีถูกปฏิเสธ (403) เพื่อหลีกเลี่ยงปัญหา SQLite

## Feature Tests

- Access & Routing: `tests/Feature/AccessAndRoutingTest.php` — ตรวจ redirect ไปหน้า login และการเข้าถึง `/home` ของ guest/ผู้ล็อกอิน
- Auth: `tests/Feature/AuthTest.php` (login แล้ว redirect `/home`, logout redirect `/`), `tests/Feature/AuthAdditionalTest.php` (login ผิดได้ 401 JSON)
- Permissions Matrix: `tests/Feature/PermissionsMatrixTest.php` — สิทธิ์ create indicator, หน้า users index, ปฏิเสธ dashboard, หน้า indicator index
- Indicators (API): `tests/Feature/IndicatorTest.php` (สิทธิ์ CRUD ผ่าน `/api/indicators`), `tests/Feature/IndicatorApiPermissionsAndValidationTest.php` (422/404/guest ถูกบล็อก)
- Indicators (Controller E2E): `tests/Feature/IndicatorControllerE2eTest.php` — store สร้าง combinations จาก `multiCounts`; update sync criteria และ regenerate checklist
- Evidence: `tests/Feature/EvidenceTest.php` — สิทธิ์หน้า index, อัปโหลดไฟล์ (ใช้ `Storage::fake('public')`), ดาวน์โหลดไฟล์, toggle สถานะ
- Export: `tests/Feature/FileExportTest.php` (ชนิดไฟล์ XLSX), `tests/Feature/ExportGuestTest.php` (guest ถูกบล็อก)
- Factories: `tests/Feature/FactoryTest.php` — `IndicatorFactory` สร้างข้อมูลครบ (รวม `deadline`, `categorie_id`)
- Basic Indexes: `tests/Feature/BasicIndexesTest.php` — หน้า departments/categories/standards ต้องมีสิทธิ์ `view-*`
- Users CRUD: `tests/Feature/UsersCrudTest.php` — สร้าง/แก้ไข/ลบ ด้วยสิทธิ์ที่เหมาะสม
- Departments CRUD: `tests/Feature/DepartmentsCrudTest.php` — index/create/update/delete พร้อมสิทธิ์
- Categories CRUD: `tests/Feature/CategoriesCrudTest.php` — index/create/update/delete (ต้องมี standard อ้างอิง)
- Standards CRUD: `tests/Feature/StandardsCrudTest.php` — index/create/update/delete พร้อมสิทธิ์

## Unit Tests

- Checklist Generation: `tests/Unit/ChecklistGeneratorTest.php` — ทดสอบ `kCombinations()` และ `syncFromCounts()` ไม่สร้างข้อมูลซ้ำ
- Indicator Resource: `tests/Unit/IndicatorResourceTest.php` — ตรวจ mapping ของ category, standard, criterias, departments, deadline
- Path Normalization: `tests/Unit/PathNormalizerTest.php` — ตัด prefix storage/public, แปลง backslash และรองรับค่า null/ว่าง
- Policies: `tests/Unit/IndicatorPolicyTest.php` — สิทธิ์ create ตามบทบาท

## โค้ดสนับสนุนที่เพิ่ม

- `app/Services/ChecklistGenerator.php`: รวม logic การสร้าง combination และ sync checklist (เรียกใช้ใน `IndicatorController`)
- `app/Support/PathNormalizer.php`: รวมฟังก์ชัน normalize path (เรียกใช้ใน `EvidenceController`)

## ข้อเสนอการเพิ่มเทสต์

- Validation: เพิ่มเคสค่าผิด/ซ้ำ ในฟอร์ม CRUD ต่างๆ
- Constraints: ทดสอบ unique/index (เช่น `indicators.code` หากกำหนด unique)
- API Auth: ทดสอบด้วย Sanctum token (ไม่ใช้ session)
- Performance: ตรวจ N+1 สำหรับหน้าที่โหลดข้อมูลจำนวนมาก

## แนวทางแก้ปัญหา

- SQLite vs Postgres: Dashboard ใช้ SQL เฉพาะ Postgres จึง assert แค่ 403 เพื่อหลีกเลี่ยง error บน SQLite
- Permissions: ตรวจให้ตรงกับ `routes/web.php` (`permission:*`) ในเทสต์จะสร้าง permission ที่ยังไม่มีให้โดยอัตโนมัติ
