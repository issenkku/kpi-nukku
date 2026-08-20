# KPI NUKKU

ระบบบริหารตัวบ่งชี้ หลักฐาน และรายงาน SAR พัฒนาด้วย Laravel 12, PostgreSQL, Redis และ Vite

เอกสารนี้อธิบายการติดตั้ง production ด้วย Docker Compose เป็นวิธีหลัก รวมถึงการอัปเกรด สำรองข้อมูล และตรวจสอบระบบก่อนส่งมอบ

## ความต้องการของระบบ

- Docker Engine 24 ขึ้นไป
- Docker Compose v2
- พื้นที่ว่างสำหรับ PostgreSQL, ไฟล์หลักฐาน และไฟล์สำรอง
- Domain พร้อม HTTPS reverse proxy สำหรับ production
- การเชื่อมต่ออินเทอร์เน็ตระหว่างติดตั้ง dependency และ build frontend

พอร์ตที่เปิดออกจากชุด Docker คือ `8080` สำหรับเว็บเท่านั้น PostgreSQL และ Redis ไม่เปิดออกสู่ host

## ติดตั้งใหม่ด้วย Docker

### 1. เตรียม source code

แตก release archive หรือ clone repository แล้วเข้า directory ของโครงการ

```bash
cd kpi-nukku
```

### 2. สร้างไฟล์ environment

Linux/macOS:

```bash
cp .env.example .env
```

PowerShell:

```powershell
Copy-Item .env.example .env
```

แก้ `.env` อย่างน้อยดังนี้

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://kpi.example.ac.th

DB_DATABASE=kpi_nukku
DB_USERNAME=kpi_nukku
DB_PASSWORD=<random-password-at-least-32-characters>
REDIS_PASSWORD=<different-random-password-at-least-32-characters>
```

ปล่อย `APP_KEY=` ว่างในการติดตั้งครั้งแรก สคริปต์ setup จะสร้างให้ครั้งเดียว ห้ามเปลี่ยน `APP_KEY` หลังระบบมีข้อมูลใช้งานแล้ว เพราะ session และข้อมูลที่เข้ารหัสเดิมอาจอ่านไม่ได้

ห้าม commit หรือส่งไฟล์ `.env` ให้บุคคลอื่น ไฟล์ที่ใช้เป็นตัวอย่างต้องลงท้ายด้วย `.example` และไม่มี secret จริง

### 3. ตั้งค่า KKU SSO

กรอกค่าที่ได้รับจากผู้ดูแล SSO

```dotenv
SSO_APP_ID=
SSO_CLIENT_ID=
SSO_CLIENT_SECRET=
SSO_REDIRECT_URL=https://kpi.example.ac.th/auth
SSO_WEB_BASE_URL=https://sso.example.ac.th
SSO_API_BASE_URL=https://sso-api.example.ac.th
```

ลงทะเบียน callback URL ให้ตรงกับ `SSO_REDIRECT_URL` และตรวจสอบว่า SSO ส่งค่า OAuth `state` กลับมาด้วย ระบบจะปฏิเสธ callback ที่ไม่มีหรือมี `state` ไม่ตรงกัน

ตั้งค่าระบบส่งอีเมลด้วย SMTP หรือ KKU Mail API ก่อนเปิดใช้งาน notification ค่าเริ่มต้น `MAIL_MAILER=log` จะเขียนอีเมลลง log เท่านั้นและไม่ส่งถึงผู้รับ สำหรับ KKU Mail API ให้กำหนดอย่างน้อย:

```dotenv
MAIL_MAILER=kku
MAIL_FROM_ADDRESS=no_reply@kku.ac.th
KKU_API_BASE=https://api.kku.ac.th/v3
KKU_CLIENT_ID=
KKU_SECRET_KEY=
```

### 4. Build image และเตรียมระบบ

```bash
docker compose build
docker compose --profile setup run --rm setup
docker compose --profile build run --rm node
docker compose up -d
```

คำสั่ง setup จะติดตั้ง production dependencies, สร้าง `APP_KEY` เฉพาะเมื่อยังไม่มี, รัน migrations, สร้าง roles/permissions และ cache configuration

ตรวจสอบ container:

```bash
docker compose ps
docker compose logs --tail=100 app nginx queue scheduler
```

ตรวจ health endpoint ที่ `http://SERVER_IP:8080/up`

### 5. สร้างผู้ดูแลระบบคนแรก

คำสั่งนี้ถามรหัสผ่านแบบไม่แสดงบนหน้าจอ และกำหนดขั้นต่ำ 12 ตัวอักษร

```bash
docker compose exec app php artisan app:create-super-admin \
  --email=admin@example.ac.th \
  --first-name=System \
  --last-name=Administrator
```

ไม่ควรรัน `php artisan db:seed` หรือ `migrate:fresh --seed` บน production เพราะ `DatabaseSeeder` มีข้อมูลตัวอย่างและล้างตารางเดิม ใช้เฉพาะคำสั่ง setup หรือ seeder นี้:

```bash
docker compose exec app php artisan db:seed \
  --class='Database\Seeders\RolesAndPermissionsSeeder' --force
```

### 6. ตั้งค่า HTTPS reverse proxy

ให้ reverse proxy ภายนอกส่ง traffic มาที่ `SERVER_IP:8080` และเขียนทับ `X-Forwarded-For`, `X-Forwarded-Host`, `X-Forwarded-Port` และ `X-Forwarded-Proto` ด้วยค่าที่ proxy เชื่อถือได้

ห้ามเปิด PHP-FPM, PostgreSQL หรือ Redis ออกสู่อินเทอร์เน็ตโดยตรง

## บริการใน Docker Compose

| Service | หน้าที่ |
|---|---|
| `nginx` | รับ HTTP ที่พอร์ต 8080 |
| `app` | PHP-FPM/Laravel |
| `db` | PostgreSQL พร้อม persistent volume `dbdata` |
| `redis` | Redis พร้อมรหัสผ่านและ volume `redisdata` |
| `queue` | ประมวลผลงาน queue |
| `scheduler` | รัน Laravel scheduler และการแจ้งเตือนตามกำหนด |
| `setup` | migration/dependency setup แบบ one-off |
| `node` | build frontend แบบ one-off |

## อัปเกรดระบบเดิม

สำรองฐานข้อมูลและไฟล์หลักฐานก่อนทุกครั้ง จากนั้นรัน:

```bash
docker compose exec app php artisan down
docker compose --profile setup run --rm setup
docker compose --profile build run --rm node
docker compose up -d --build
docker compose exec app php artisan queue:restart
docker compose exec app php artisan up
```

Migration `2026_08_20_000009_sanitize_existing_rich_text` จะลบ script/event handler ที่ไม่ปลอดภัยจาก rich text เดิม การเปลี่ยนแปลงนี้ย้อนกลับไม่ได้ จึงต้องมีฐานข้อมูลสำรองก่อน migrate

## สำรองและกู้คืน

สำรอง PostgreSQL:

```bash
docker compose exec -T db sh -lc 'pg_dump -U "$POSTGRES_USER" "$POSTGRES_DB"' > kpi-nukku.sql
```

ต้องสำรอง directory `storage/app/public` พร้อมฐานข้อมูลด้วย

กู้คืน PostgreSQL บน Linux/macOS:

```bash
docker compose exec -T db sh -lc 'psql -U "$POSTGRES_USER" "$POSTGRES_DB"' < kpi-nukku.sql
```

PowerShell:

```powershell
Get-Content -Raw .\kpi-nukku.sql | docker compose exec -T db sh -lc 'psql -U "$POSTGRES_USER" "$POSTGRES_DB"'
```

การกู้คืนเขียนทับข้อมูลเป้าหมาย ควรทดสอบใน staging ก่อน ห้ามใช้ `docker compose down -v` เว้นแต่ตั้งใจลบฐานข้อมูลและ Redis volumes อย่างถาวร

## ติดตั้งแบบไม่ใช้ Docker

ต้องมี PHP 8.2 ขึ้นไปพร้อม extensions `pdo_pgsql`, `bcmath`, `exif`, `gd`, `zip`, `redis`, Composer 2, Node.js 22, PostgreSQL 15 และ Redis 7

```bash
composer install --no-dev --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class='Database\Seeders\RolesAndPermissionsSeeder' --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

กำหนด web root ไปที่ `public/` และจัด process แยกสำหรับ:

```bash
php artisan queue:work --sleep=3 --tries=3 --timeout=120
php artisan schedule:work
```

## การทดสอบก่อน release

```bash
composer validate --strict --no-check-publish
composer audit --locked
php artisan test
npm ci
npm audit
npm run build
```

ควรทดสอบเพิ่มบน staging ที่ใช้ PostgreSQL จริง: Login/SSO, สิทธิ์ทั้ง 5 roles, assignment, upload/download/preview, export PDF/DOCX/XLSX, queue/email/scheduler และ backup/restore

รายละเอียดชุดทดสอบอยู่ใน [docs/TESTS.md](docs/TESTS.md) และสิทธิ์แต่ละ role อยู่ใน [docs/ROLES_MATRIX.md](docs/ROLES_MATRIX.md)

## External assets

บางหน้าจอยังโหลด jQuery, DataTables, Trumbowyg, Alpine.js, Lucide, Font Awesome และ Google Fonts จาก CDN หาก production ออกอินเทอร์เน็ตไม่ได้ ต้องดาวน์โหลดและ bundle assets เหล่านี้ไว้ภายในก่อนใช้งาน

## การสร้างชุดส่งมอบ

หลัง commit, tag และทดสอบ release แล้ว ให้สร้าง archive จาก Git เพื่อไม่รวม `.git`, `.env`, `vendor`, `node_modules`, log และไฟล์ชั่วคราว:

```bash
git archive --format=zip --output=kpi-nukku-v1.0.0.zip v1.0.0
```

ถ้า secret เคยถูก commit ไปแล้ว การลบไฟล์ใน commit ใหม่ไม่ได้นำค่าออกจาก Git history ต้องเปลี่ยน secret จริงทั้งหมด และใช้เครื่องมือ rewrite history ก่อนส่ง repository ที่มีประวัติ commit

## Troubleshooting

```bash
docker compose logs -f app nginx queue scheduler
docker compose exec app php artisan optimize:clear
docker compose exec app php artisan config:cache
docker compose exec app php artisan migrate:status
```

ระบบรองรับไฟล์สูงสุด 200 MB ต่อไฟล์ โดย reverse proxy ภายนอกต้องกำหนด body-size และ timeout ไม่น้อยกว่าค่าใน Nginx/PHP ของโครงการ
