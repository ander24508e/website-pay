#!/bin/sh
set -e

echo "⏳ Esperando a la base de datos..."
until php artisan migrate --seed --force; do
  echo "⏳ MySQL no listo, reintentando..."
  sleep 3
done

echo "🚀 Iniciando Octane con FrankenPHP..."
exec php artisan octane:start \
    --server=frankenphp \
    --host=0.0.0.0 \
    --port=8000

    # Ejecutar migraciones, levantar el servidor y ejecutar tareas programadas
    # php artisan migrate --seed --force && \              