FROM php:8.1-apache

LABEL maintainer="DIABLE Team"
LABEL description="SQL Injection based on Comments Lab - DIABLE v3.0"
LABEL version="1.0"

RUN apt-get update && apt-get install -y \
    sqlite3 \
    libsqlite3-dev \
    curl \
    && docker-php-ext-install pdo pdo_sqlite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite
RUN sed -i "s/AllowOverride None/AllowOverride All/g" /etc/apache2/apache2.conf

WORKDIR /var/www/html

COPY src/ /var/www/html/

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

RUN php /var/www/html/init_db.php

EXPOSE 80

ENV DEBUG_MODE=false
ENV DB_PATH=/var/www/html/database.db

HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/health.php || exit 1

CMD ["apache2-foreground"]
