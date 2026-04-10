# IP Address Management — Backend

A REST API for managing IP addresses with full audit logging and token-based authentication via Laravel Sanctum.

---

## Tech Stack

- **PHP** 8.2
- **Laravel** 10
- **MySQL** 8.0
- **Laravel Sanctum** (API token authentication)
- **Nginx** (reverse proxy via Docker)
- **PHPUnit** 10 + Mockery (unit tests)

---

## Project Structure

```
backend/
├── app/
│   ├── Http/Controllers/Api/   # AuthController, IpAddressController, AuditLogController
│   ├── Models/                 # User, IpAddress, AuditLog
│   ├── Repositories/           # Data access layer
│   ├── Services/               # Business logic (Auth, IpAddress, AuditLog)
│   └── Library/Response/       # ResponseBuilder utility
├── database/
│   ├── migrations/             # Database migrations
│   └── seeders/                # UserSeeder (creates default user)
├── docker/
│   ├── docker-compose.yml      # Main Compose file
│   ├── docker-compose.override.yml
│   ├── Dockerfile              # PHP-FPM image
│   ├── nginx.conf              # Nginx site config
│   ├── entrypoint.sh           # Container startup script
│   └── .envs/
│       ├── app.env             # Active environment (git-ignored)
│       └── app.env.example     # Template to copy from
├── tests/Unit/                 # Unit tests for all services
├── Makefile                    # Convenience commands
└── routes/api.php              # All API routes
```

---

## Prerequisites

- [Docker](https://docs.docker.com/get-docker/) and Docker Compose v2

---

## Getting Started

### 1. Enter the backend directory

```bash
cd backend/
```

### 2. Set up the environment file

```bash
cp docker/.envs/app.env.example docker/.envs/app.env
```

Edit `docker/.envs/app.env` and set your values:

```env
APP_KEY=                    # leave blank — generate in step 4
DB_HOST=db                  # matches the db service name in docker-compose.yml
DB_DATABASE=ip_management_db
DB_USERNAME=root
DB_PASSWORD=secret
WEB_PORT=8081               # host port for the API
```

### 3. Build images and install dependencies

```bash
make setup
```

This single command copies the env file, builds the Docker image, and installs Composer dependencies.

Or do it step by step:

```bash
cd docker && docker compose build --no-cache
cd docker && docker compose run --rm --user root app sh -c "composer install"
```

### 4. Start the containers

```bash
make up

# Or manually
cd docker && docker compose up -d
```

### 5. Generate the application key

```bash
make key-generate

# Or manually
cd docker && docker compose run --rm app php artisan key:generate
```

### 6. Run database migrations

```bash
# Via the custom migration script (recommended — skips already-existing tables)
make migrate

# Or via Artisan
make migrate-artisan
```

### 7. Seed the database

```bash
make seed

# Or manually
cd docker && docker compose run --rm app php artisan db:seed
```

This creates the default user:

| Field    | Value              |
|----------|--------------------|
| Email    | `test@example.com` |
| Password | `password123`      |

### 8. Verify the installation

```bash
curl http://localhost:8081/api/health
```

Expected response:

```json
{ "status": true, "code": 200, "message": "OK" }
```

---

## Docker Commands

### Using Make

| Command               | Description                                       |
|-----------------------|---------------------------------------------------|
| `make setup`          | First-time setup: build image + install deps      |
| `make up`             | Start all containers in the background            |
| `make down`           | Stop all containers                               |
| `make restart`        | Restart all containers                            |
| `make build`          | Rebuild Docker images                             |
| `make shell`          | Open a bash shell in the app container            |
| `make logs`           | Tail app container logs                           |
| `make migrate`        | Run migrations via the custom PHP script          |
| `make migrate-artisan`| Run migrations via Artisan                        |
| `make seed`           | Run database seeders                              |
| `make key-generate`   | Generate the Laravel APP_KEY                      |
| `make install`        | Install Composer dependencies                     |
| `make update`         | Update Composer dependencies                      |
| `make test`           | Run the full test suite inside Docker             |
| `make clean`          | Stop containers, remove volumes, prune images     |

Run `make help` to see all commands at any time.

### Raw Docker Compose commands

All Compose commands must be run from the `docker/` directory:

```bash
# Build images
cd docker && docker compose build

# Build without cache
cd docker && docker compose build --no-cache

# Start containers (detached)
cd docker && docker compose up -d

# Stop containers
cd docker && docker compose down

# Stop and remove volumes
cd docker && docker compose down -v

# Open a shell in the running app container
cd docker && docker compose exec app bash

# Run a one-off Artisan command in a fresh container
cd docker && docker compose run --rm app php artisan <command>

# View logs (follow)
cd docker && docker compose logs -f app

# View all container statuses
cd docker && docker compose ps
```

---

## Running Tests

```bash
# Using Make (runs inside Docker)
make test

# Run unit tests only
cd docker && docker compose run --rm app ./vendor/bin/phpunit --testsuite Unit

# Run with verbose output
cd docker && docker compose run --rm app ./vendor/bin/phpunit --testsuite Unit --verbose

# Run a specific test class
cd docker && docker compose run --rm app ./vendor/bin/phpunit tests/Unit/Services/Auth/LoginServiceTest.php
cd docker && docker compose run --rm app ./vendor/bin/phpunit tests/Unit/Services/IpAddress/IpAddressServiceTest.php
cd docker && docker compose run --rm app ./vendor/bin/phpunit tests/Unit/Services/AuditLog/AuditLogQueryServiceTest.php

# Run with code coverage (requires Xdebug or PCOV)
cd docker && docker compose run --rm app ./vendor/bin/phpunit --coverage-text
```

---

## API Reference

Base URL: `http://localhost:8081/api`

### Authentication

| Method | Endpoint  | Auth required | Description      |
|--------|-----------|---------------|------------------|
| GET    | `/health` | No            | Health check     |
| POST   | `/login`  | No            | Obtain API token |

**POST `/login`**

Request:
```json
{
  "email": "test@example.com",
  "password": "password123"
}
```

Response:
```json
{
  "status": true,
  "code": 200,
  "message": "Login successful",
  "data": { "token": "<bearer-token>" }
}
```

All protected endpoints require:
```
Authorization: Bearer <token>
```

---

### IP Addresses

| Method | Endpoint             | Description             |
|--------|----------------------|-------------------------|
| GET    | `/ip-addresses`      | List all IP addresses   |
| POST   | `/ip-addresses`      | Create a new IP address |
| GET    | `/ip-addresses/{id}` | Get a single IP address |
| PUT    | `/ip-addresses/{id}` | Update the label        |

**POST/PUT body:**
```json
{
  "ip_address": "192.168.1.1",
  "label": "Office Router"
}
```

---

### Audit Logs

| Method | Endpoint                      | Description                    |
|--------|-------------------------------|--------------------------------|
| GET    | `/audit-logs`                 | All audit entries (paginated)  |
| GET    | `/audit-logs/login`           | All login events               |
| GET    | `/audit-logs/my-logins`       | Current user's login history   |
| GET    | `/audit-logs/ip-address/{id}` | Change history for one IP      |

---

## Containers

| Container             | Role                | Internal port | Host port       |
|-----------------------|---------------------|---------------|-----------------|
| `ip-management-app`   | PHP-FPM (Laravel)   | 9000          | —               |
| `ip-management-nginx` | Nginx reverse proxy | 80            | `8081`          |
| `ip-management-db`    | MySQL 8.0           | 3306          | `3306` (default)|

---

## Environment Variables (`docker/.envs/app.env`)

| Variable      | Description                              | Default              |
|---------------|------------------------------------------|----------------------|
| `APP_KEY`     | Laravel encryption key                   | *(generate via artisan)* |
| `APP_ENV`     | Environment (`local`, `production`)      | `local`              |
| `APP_DEBUG`   | Enable debug mode                        | `true`               |
| `APP_URL`     | Public URL of the app                    | `http://localhost:8081` |
| `DB_HOST`     | MySQL hostname                           | `db`                 |
| `DB_PORT`     | MySQL port                               | `3306`               |
| `DB_DATABASE` | Database name                            | `ip_management_db`   |
| `DB_USERNAME` | Database user                            | `root`               |
| `DB_PASSWORD` | Database password                        | `secret`             |
| `WEB_PORT`    | Host port exposed by Nginx               | `8081`               |

---

## Troubleshooting

**Artisan commands hang or time out**

Use the raw migration script instead:
```bash
make migrate
# or: cd docker && docker compose run --rm app php /var/www/html/run-migrations.php
```

**Permission errors in the container**

Run Composer commands as root:
```bash
cd docker && docker compose run --rm --user root app sh -c "composer install"
```

**Storage / cache permission errors**

```bash
cd docker && docker compose exec app bash -c "chown -R www-data:www-data storage bootstrap/cache && chmod -R 775 storage bootstrap/cache"
```
