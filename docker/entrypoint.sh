#!/bin/sh
set -e

php artisan optimize:clear

if [ ! -L public/storage ]; then
  php artisan storage:link
fi

echo "Waiting for the database..."
until php artisan migrate --force; do
  echo "Database is not ready, retrying..."
  sleep 3
done

echo "Starting Octane with FrankenPHP..."
exec php artisan octane:start \
    --server=frankenphp \
    --host=0.0.0.0 \
    --port=8000
