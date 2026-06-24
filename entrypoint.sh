#!/bin/sh
set -e

php artisan optimize:clear

if [ ! -L public/storage ]; then
  php artisan storage:link
fi

echo "DB host: ${DB_HOST:-undefined}"
echo "DB port: ${DB_PORT:-undefined}"
echo "DB database: ${DB_DATABASE:-undefined}"
echo "Esperando a la base de datos..."
until php artisan migrate --force; do
  echo "MySQL no esta listo, reintentando..."
  sleep 3
done

echo "Iniciando Octane con FrankenPHP..."
exec php artisan octane:start \
    --server=frankenphp \
    --host=0.0.0.0 \
    --port=8000
