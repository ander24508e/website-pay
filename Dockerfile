###################################
# 1️⃣ STAGE: Node (Vite build)
###################################
FROM node:20-alpine AS node-build

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY resources ./resources
COPY public ./public 
COPY vite.config.* ./
RUN npm run build


###################################
# 2️⃣ STAGE: PHP + FrankenPHP
###################################
FROM dunglas/frankenphp:1-php8.2

WORKDIR /app

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/*

RUN install-php-extensions \
    pdo_mysql \
    zip \
    pcntl \
    intl \
    opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .
# 👉 INSTALAR DEPENDENCIAS (ESTO FALTABA)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 👉 COPIAR ASSETS COMPILADOS
COPY --from=node-build /app/public/build ./public/build

RUN chown -R www-data:www-data /app \
    && chmod -R 775 storage bootstrap/cache

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["/entrypoint.sh"]
