# IP Address Management Solution

A Laravel-based API for managing IP addresses with audit logging, running on Nginx + PHP-FPM.

## Prerequisites

- Docker and Docker Compose
- PHP 8.1+
- MySQL 8.0+
- Composer

## Quick Setup (Recommended)

Run the automated setup script:

```bash
./setup.sh
```

This will:
- Copy `.env.example` to `.env`
- Build Docker containers
- Install PHP dependencies
- Generate application key
- Run database migrations

## Manual Setup

### 1. Environment Configuration

Copy the example environment file:

```bash
cp .env.example .env
```

Edit `.env` to match your database configuration. The app is configured to work with an external MySQL database on the `common-net` network.

If you need to customize the Docker setup (ports, networks, etc.), copy the override example:

```bash
cd docker
cp docker-compose.override.example.yml docker-compose.override.yml
```

Edit the override file as needed for your local environment.

### 2. Install Dependencies

```bash
# Navigate to docker directory
cd docker

# Build and start the containers
docker compose build --no-cache
docker compose up -d

# Install PHP dependencies
docker compose run --rm --user root app sh -c "composer install"
```

### 3. Database Setup

The application expects a MySQL database named `ip_management_db` accessible at `common-mysql:3306`.

If you need to run migrations manually (in case of Artisan issues):

```bash
# Run migrations using the provided script
docker compose run --rm app php /var/www/html/run-migrations.php
```

Or if Artisan works in your environment:

```bash
docker compose run --rm app php artisan migrate
```

### 4. Generate Application Key

```bash
docker compose run --rm app php artisan key:generate
```

### 5. Verify Installation

Check that the application is running:

```bash
curl http://localhost:8081/api/health
```

You should receive a JSON response indicating the API is healthy.

## Makefile Commands

Use the provided Makefile for common tasks:

```bash
make setup    # Initial setup
make up       # Start containers
make migrate  # Run migrations
make test     # Run tests
make clean    # Clean up
```

Run `make help` to see all available commands.

## API Endpoints

- `GET /api/health` - Health check endpoint
- `GET /api/user` - Get authenticated user (requires Sanctum token)

## Database Schema

The application includes three main tables:

- `users` - User accounts
- `ip_addresses` - IP address records with labels
- `audit_logs` - Audit trail for all changes

## Troubleshooting

### Artisan Commands Hanging

If `php artisan` commands hang or timeout, use the provided `run-migrations.php` script instead:

```bash
docker compose run --rm app php /var/www/html/run-migrations.php
```

### Database Connection Issues

Ensure your database is accessible on the `common-net` network and the credentials in `.env` are correct.

### Permission Issues

If you encounter permission errors, run commands with `--user root`:

```bash
docker compose run --rm --user root app sh -c "composer install"
```

## Development

### Running Tests

```bash
docker compose run --rm app php artisan test
```

### Code Style

```bash
docker compose run --rm app ./vendor/bin/pint
```