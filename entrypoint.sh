#!/bin/sh
set -e

echo "==============================="
echo "  INICIANDO LAVADORA ENDARA"
echo "==============================="

echo ">>> [1/6] Limpiando cache..."
php artisan config:clear  || true
php artisan cache:clear   || true
php artisan route:clear   || true
php artisan view:clear    || true
php artisan migrate --force   || true

npm install    || true
composer install    || true

echo ">>> [2/6] Storage link..."
if [ ! -L public/storage ]; then
  php artisan storage:link || true
fi

echo ">>> [3/6] Verificando dependencias PHP..."
if [ ! -d vendor ]; then
  echo "vendor no existe, instalando..."
  composer install --no-dev --optimize-autoloader --no-interaction
else
  echo "vendor OK"
fi

echo ">>> [4/6] Verificando assets Vite..."
if [ ! -d public/build ]; then
  echo "public/build no existe, compilando..."
  npm ci --omit=dev --ignore-scripts
  npm run build
else
  echo "Assets OK"
fi

echo ">>> [5/6] DB: ${DB_HOST:-undefined}:${DB_PORT:-undefined}/${DB_DATABASE:-undefined}"
echo "Esperando base de datos..."
MAX_TRIES=30
TRIES=0
until php artisan migrate --force 2>&1; do
  TRIES=$((TRIES + 1))
  if [ $TRIES -ge $MAX_TRIES ]; then
    echo "ERROR: Base de datos no disponible. Abortando."
    exit 1
  fi
  echo "MySQL no listo ($TRIES/$MAX_TRIES), reintentando en 3s..."
  sleep 3
done

echo ">>> [6/6] Optimizando Laravel..."
php artisan optimize || true

echo "==============================="
echo "  INICIANDO FRANKENPHP :8000"
echo "==============================="
exec php artisan octane:start \
    --server=frankenphp \
    --host=0.0.0.0 \
    --port=8000