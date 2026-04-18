#!/usr/bin/env sh
# Helpers for MySQL inside Docker Compose (run from repository root).
# Requires a .env file with MYSQL_ROOT_PASSWORD and DB_DATABASE set (same as Laravel).

set -e
cd "$(dirname "$0")/.."

if [ ! -f .env ]; then
    echo "Missing .env — copy env.docker.example to .env and fill values." >&2
    exit 1
fi

# shellcheck disable=SC1091
. ./.env

case "${1:-}" in
drop)
    echo "Dropping database ${DB_DATABASE} (if exists) and recreating empty schema..."
    docker compose exec -T mysql sh -c "mysql -uroot -p\"${MYSQL_ROOT_PASSWORD}\" -e \"DROP DATABASE IF EXISTS \\\`${DB_DATABASE}\\\`; CREATE DATABASE \\\`${DB_DATABASE}\\\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\""
    echo "Done. Import with: $0 import path/to/file.sql"
    ;;
import)
    if [ -z "${2:-}" ] || [ ! -f "$2" ]; then
        echo "Usage: $0 import /absolute/or/relative/path/to.sql" >&2
        exit 1
    fi
    echo "Importing $2 into ${DB_DATABASE}..."
    docker compose exec -T mysql sh -c "mysql -uroot -p\"${MYSQL_ROOT_PASSWORD}\" \"${DB_DATABASE}\"" < "$2"
    echo "Import finished. Run migrations if needed: docker compose exec app php artisan migrate --force"
    ;;
migrate)
    docker compose exec app php artisan migrate --force
    ;;
shell)
    docker compose exec mysql sh -c "mysql -uroot -p\"${MYSQL_ROOT_PASSWORD}\" \"${DB_DATABASE}\""
    ;;
*)
    echo "Usage: $0 drop | import <file.sql> | migrate | shell" >&2
    exit 1
    ;;
esac
