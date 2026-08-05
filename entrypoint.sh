#!/bin/sh
set -e

echo "=== Limpiando cache ==="
php artisan config:clear  || true
php artisan cache:clear   || true
php artisan route:clear   || true
php artisan view:clear    || true

echo "=== Storage link ==="
if [ ! -L public/storage ]; then
  php artisan storage:link || true
fi

echo "=== DB host: ${DB_HOST:-undefined} ==="
echo "=== DB port: ${DB_PORT:-undefined} ==="
echo "=== DB database: ${DB_DATABASE:-undefined} ==="

echo "=== Esperando base de datos ==="
MAX_TRIES=30
TRIES=0
until php artisan migrate --force 2>&1; do
  TRIES=$((TRIES + 1))
  if [ $TRIES -ge $MAX_TRIES ]; then
    echo "ERROR: Base de datos no disponible tras $MAX_TRIES intentos. Abortando."
    exit 1
  fi
  echo "MySQL no listo, reintentando ($TRIES/$MAX_TRIES)..."
  sleep 3
done

echo "=== Optimizando ==="
php artisan optimize || true

echo "=== Iniciando FrankenPHP ==="
exec php artisan octane:start \
    --server=frankenphp \
    --host=0.0.0.0 \
    --port=8000