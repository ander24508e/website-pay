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
COPY tailwind.config.* ./
COPY postcss.config.* ./

RUN npm run build


###################################
# 2️⃣ STAGE: PHP + FrankenPHP
###################################
FROM dunglas/frankenphp:1-php8.2

WORKDIR /app

ENV PUPPETEER_SKIP_DOWNLOAD=true
ENV BROWSERSHOT_CHROME_PATH=/usr/bin/chromium

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    ca-certificates \
    nodejs \
    npm \
    chromium \
    fonts-liberation \
    libasound2 \
    libatk-bridge2.0-0 \
    libatk1.0-0 \
    libcups2 \
    libdbus-1-3 \
    libdrm2 \
    libgbm1 \
    libgtk-3-0 \
    libnspr4 \
    libnss3 \
    libx11-xcb1 \
    libxcomposite1 \
    libxdamage1 \
    libxfixes3 \
    libxrandr2 \
    xdg-utils \
    && rm -rf /var/lib/apt/lists/*

RUN install-php-extensions \
    pdo_mysql \
    zip \
    pcntl \
    intl \
    opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN npm ci --omit=dev --ignore-scripts

RUN composer install --no-dev --optimize-autoloader --no-interaction

COPY --from=node-build /app/public/build ./public/build

RUN chown -R www-data:www-data /app \
    && chmod -R 775 storage bootstrap/cache

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["/entrypoint.sh"]
