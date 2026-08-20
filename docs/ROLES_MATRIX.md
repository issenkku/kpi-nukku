# Roles & Permissions Matrix (EN/TH)

This matrix summarizes key permissions per role as configured in `database/seeders/RolesAndPermissionsSeeder.php` and validated by tests.

## Seeding (How to Apply)

- Seed only roles/permissions:
  - `php artisan db:seed --class=Database\\Seeders\\RolesAndPermissionsSeeder`
- Full refresh with all demo data (destructive):
  - `php artisan migrate:fresh --seed`
  - This runs `DatabaseSeeder`, which in turn calls roles, permissions, users, and sample content seeders.
  - This is disabled in production and must only be used in development/test environments.

Notes
- After seeding, Spatie permission cache is cleared automatically by the seeder.
- Guards: all permissions/roles use the `web` guard.

## Legend
- Y: permitted
- N: not permitted

## Matrix (Key Areas)

| Area                          | Permission(s)                    | super_admin | system_admin | qa_admin | administration_admin | user |
|------------------------------|----------------------------------|-------------|--------------|----------|----------------------|------|
| Dashboard                    | view-dashboard                   | Y           | Y            | Y        | Y                    | N    |
| Dashboard Export             | export-dashboard                 | Y           | Y            | Y        | Y                    | N    |
| Indicator Dashboard          | view-indicator-dashboard         | Y           | Y            | Y        | Y                    | N    |
| Indicator Create/Edit/Delete | create/edit/delete-indicator     | Y           | Y            | Y        | Y                    | N    |
| Users Management             | view/create/edit/delete-users    | Y           | Y            | View     | View                 | N    |
| Departments                  | view/create/edit/delete-dept     | Y           | Y            | View     | View                 | N    |
| Categories                   | view/create/edit/delete-categories| Y          | Y            | View     | View                 | N    |
| Standards                    | view/create/edit/delete-standards| Y           | Y            | View     | View                 | N    |
| Settings                     | view/create/edit-settings        | Y           | Y            | View     | View                 | N    |
| Evidence Index               | view-evidence                    | Y           | Y            | Y        | Y                    | Y    |
| Evidence Create/Edit/Delete  | create/edit/delete-evidence      | Y           | Y            | Y        | N                    | Y    |
| Evidence Download            | download-evidence                | Y           | Y            | Y        | Y                    | Y    |
| KPI User Dashboard           | view/show-dashboard-kpi-user     | Y           | Y            | Y        | Y                    | Y    |

Notes
- “View” in Users/Departments/Categories/Standards means read-only according to seeder.
- Routes are protected in `routes/web.php` via `permission:*` and session `auth` guard in this project’s tests.
- Verified by tests in `tests/Feature/RolesMatrixByRoleTest.php` and `tests/Feature/NavbarVisibilityByRoleTest.php`.

---

# ตารางบทบาทและสิทธิ์ (TH)

สรุปสิทธิ์หลักของแต่ละบทบาทตาม `database/seeders/RolesAndPermissionsSeeder.php` และยืนยันด้วยเทสต์

## สัญลักษณ์
- Y: มีสิทธิ์
- N: ไม่มีสิทธิ์

## ตาราง (หัวข้อสำคัญ)

| หัวข้อ                         | สิทธิ์ (permission)               | super_admin | system_admin | qa_admin | administration_admin | user |
|--------------------------------|------------------------------------|-------------|--------------|----------|----------------------|------|
| Dashboard                      | view-dashboard                     | Y           | Y            | Y        | Y                    | N    |
| Export Dashboard               | export-dashboard                   | Y           | Y            | Y        | Y                    | N    |
| Indicator Dashboard            | view-indicator-dashboard           | Y           | Y            | Y        | Y                    | N    |
| จัดการ Indicator              | create/edit/delete-indicator       | Y           | Y            | Y        | Y                    | N    |
| จัดการผู้ใช้งาน               | view/create/edit/delete-users      | Y           | Y            | View     | View                 | N    |
| หน่วยงาน (Departments)        | view/create/edit/delete-departments| Y           | Y            | View     | View                 | N    |
| หมวดหมู่ (Categories)         | view/create/edit/delete-categories | Y           | Y            | View     | View                 | N    |
| มาตรฐาน (Standards)           | view/create/edit/delete-standards  | Y           | Y            | View     | View                 | N    |
| การตั้งค่า (Settings)         | view/create/edit-settings          | Y           | Y            | View     | View                 | N    |
| หลักฐาน Evidence (ดูรายการ)   | view-evidence                      | Y           | Y            | Y        | Y                    | Y    |
| หลักฐาน Evidence (แก้ไข)      | create/edit/delete-evidence        | Y           | Y            | Y        | N                    | Y    |
| ดาวน์โหลด Evidence            | download-evidence                  | Y           | Y            | Y        | Y                    | Y    |
| Dashboard KPI ผู้ใช้           | view/show-dashboard-kpi-user       | Y           | Y            | Y        | Y                    | Y    |

หมายเหตุ
- “View” หมายถึงดูได้อย่างเดียว (ตาม seeder) ไม่สามารถสร้าง/แก้ไข/ลบ
- เส้นทางเว็บถูกป้องกันด้วย `auth` และ `permission:*` (ดูที่ `routes/web.php`)
- ผู้ใช้ role `user` จัดการหรือดาวน์โหลดหลักฐานได้เฉพาะตัวบ่งชี้ที่ได้รับ assignment และแก้/ลบได้เฉพาะหลักฐานของตนเอง
- ยืนยันด้วยเทสต์ `tests/Feature/RolesMatrixByRoleTest.php` และ `tests/Feature/NavbarVisibilityByRoleTest.php`
