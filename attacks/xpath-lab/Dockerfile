FROM php:8.1-apache

# Métadonnées
LABEL maintainer="DIABLE Team"
LABEL description="XPath Injection Lab - DIABLE v3.0"
LABEL version="1.0"

# Installation des dépendances
RUN apt-get update && apt-get install -y \
    libxml2-dev \
    libxslt1-dev \
    curl \
    && docker-php-ext-install dom \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configuration Apache
RUN a2enmod rewrite
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Création du répertoire de travail
WORKDIR /var/www/html

# Copie des fichiers sources
COPY src/ /var/www/html/

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Initialisation des données XML
RUN php /var/www/html/init_data.php

# Port exposé
EXPOSE 80

# Variables d'environnement par défaut
ENV DEBUG_MODE=false
ENV DATA_PATH=/var/www/html/data

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/health.php || exit 1

# Démarrage d'Apache
CMD ["apache2-foreground"]
