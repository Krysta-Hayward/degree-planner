#!/usr/bin/env bash

set -e

cd /var/www/html

# Create the environment file if missing
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Create the environment testing file if missing
if [ ! -f .env.testing ]; then
    cp .env.testing.example .env.testing
fi

# Install PHP dependencies
composer install --no-interaction

# Generate the application key if missing
if [ -f .env ] && ! grep -q '^APP_KEY=.' .env; then
    php artisan key:generate
fi

# Generate the testing application key if missing
if [ -f .env.testing ] && ! grep -q '^APP_KEY=.' .env.testing; then
    php artisan key:generate --env=testing
fi

# Create the SQLite database if missing
mkdir -p database

if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
fi

# Run database migrations
php artisan migrate --force

# Start Laravel
exec php artisan serve --host=0.0.0.0 --port=8000
