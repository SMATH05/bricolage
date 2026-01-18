#!/bin/bash
# Build script for Render.com deployment

set -e

echo "🚀 Starting Render build process..."

# Install dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Clear and warmup cache
echo "🗑️  Clearing cache..."
php bin/console cache:clear --env=prod --no-debug

echo "🔥 Warming up cache..."
php bin/console cache:warmup --env=prod --no-debug

# Run migrations (optional - can be done via Render shell)
# echo "📊 Running database migrations..."
# php bin/console doctrine:migrations:migrate --no-interaction --env=prod

echo "✅ Build completed successfully!"
