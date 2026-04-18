#!/bin/sh
set -e
cd /var/www/html

chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

i=0
while [ "$i" -lt 45 ]; do
  if php -r '
    try {
      $h = getenv("DB_HOST") ?: "mysql";
      $p = getenv("DB_PORT") ?: "3306";
      $u = getenv("DB_USERNAME");
      $w = getenv("DB_PASSWORD");
      new PDO("mysql:host={$h};port={$p}", $u, $w, [PDO::ATTR_TIMEOUT => 2]);
      exit(0);
    } catch (Throwable $e) {
      exit(1);
    }
  ' 2>/dev/null; then
    break
  fi
  i=$((i + 1))
  sleep 2
done

php artisan migrate --force --no-interaction

exec docker-php-entrypoint "$@"
