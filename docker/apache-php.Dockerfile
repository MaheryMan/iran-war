FROM php:8.2-apache

# ─── Modules Apache activés ────────────────────────────────────────────────
# rewrite   → URL rewriting (.htaccess)
# headers   → contrôle des headers HTTP (Cache-Control, X-Frame-Options…)
# deflate   → compression Gzip/Brotli
# expires   → cache navigateur (Expires / Cache-Control)
# mime      → types MIME corrects (utile pour les assets)
# setenvif  → conditions sur variables d'env (utile pour les proxies)
# filter    → pipeline de filtres (requis par deflate)
RUN a2enmod rewrite headers deflate expires mime setenvif filter

# ─── Extensions PHP utiles ─────────────────────────────────────────────────
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libzip-dev \
    libonig-dev \
    unzip \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mysqli \
        mbstring \
        zip \
        gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ─── PHP config optimisée ──────────────────────────────────────────────────
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# ─── Permissions ───────────────────────────────────────────────────────────
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
