FROM php:8.3-cli

RUN apt-get update \
    && apt-get install -yqq --no-install-recommends \
        git \
        unzip \
        make \
        libpq-dev \
    && docker-php-ext-install pdo_pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-interaction --no-dev --optimize-autoloader

# Render passes the port it wants in $PORT; the Makefile default is only for
# a local run.
ENV PORT=8000
EXPOSE 8000

CMD ["sh", "-c", "make start PORT=$PORT"]
