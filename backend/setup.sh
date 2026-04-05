#!/bin/bash

set -e

echo "🚀 IP Address Management - Setup Script"
echo "========================================"

# Check if .env exists
if [ ! -f ".env" ]; then
    echo "📋 Copying .env.example to .env..."
    cp .env.example .env
    echo "✅ .env file created. Please edit it with your database credentials if needed."
else
    echo "ℹ️  .env file already exists."
fi

# Navigate to docker directory
cd docker

echo "🏗️  Building Docker containers (PHP-FPM + Nginx)..."
docker compose build --no-cache

echo "📦 Installing PHP dependencies..."
docker compose run --rm --user root app sh -c "composer install"

echo "🔑 Generating application key..."
docker compose run --rm app php artisan key:generate 2>/dev/null || echo "⚠️  Key generation failed - you may need to run it manually"

echo "🗄️  Running database migrations..."
docker compose run --rm app php /var/www/html/run-migrations.php

echo ""
echo "🎉 Setup complete!"
echo ""
echo "To start the application:"
echo "  cd docker && docker compose up -d"
echo ""
echo "API will be available at: http://localhost:8081"
echo "Health check: http://localhost:8081/api/health"