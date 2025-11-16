#!/bin/bash

echo "🚀 Setting up Ikhana Backend..."

# Install dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader

# Generate application key
echo "🔑 Generating application key..."
php artisan key:generate --ansi

# Wait for database
echo "⏳ Waiting for database connection..."
until php artisan db:show 2>/dev/null; do
    echo "Waiting for database..."
    sleep 2
done

# Run migrations
echo "📊 Running migrations..."
php artisan migrate --force

# Generate API documentation
echo "📚 Generating API documentation..."
php artisan scribe:generate

# Clear caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear

echo ""
echo "✅ Setup completed successfully!"
echo ""
echo "🌐 API available at: http://localhost:8000/api"
echo "📖 API Documentation: http://localhost:8000/docs"
echo ""

