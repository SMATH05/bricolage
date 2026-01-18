#!/bin/bash
set -e

# Wait for database to be ready
echo "Waiting for database connection..."
sleep 5

# Clear and warm up cache
echo "Warming up cache..."
php bin/console cache:warmup --env=prod || true

# Run migrations
echo "Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration || true

# Start Apache
echo "Starting Apache..."
exec "$@"
