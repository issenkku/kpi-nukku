#!/bin/sh
set -e

echo "[setup] Waiting for database ${DB_HOST:-db}:${DB_PORT:-5432} ..."
php -r '
for($i=0;$i<120;$i++){
  $h=getenv("DB_HOST")?:"db";
  $p=(int)(getenv("DB_PORT")?:5432);
  $c=@fsockopen($h,$p,$e,$s,1);
  if($c){fclose($c); exit(0);} 
  sleep(1);
}
fwrite(STDERR, "DB not ready after wait\n");
exit(1);
'

echo "[setup] Installing composer deps"
composer install --no-interaction --prefer-dist --optimize-autoloader

echo "[setup] App key + storage link"
php artisan key:generate --force || true
php artisan storage:link || true

echo "[setup] Note: keep dbdata volume (avoid 'docker compose down -v') to retain data"

echo "[setup] Fixing permissions"
chown -R www-data:www-data storage bootstrap/cache public/storage || true
chmod -R ug+rwX storage bootstrap/cache public/storage || true

echo "[setup] Running migrations"
php artisan migrate

echo "[setup] Copying fonts (if available)"
php artisan fonts:copy || true

echo "[setup] Caching config/routes/views"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "[setup] Done"
