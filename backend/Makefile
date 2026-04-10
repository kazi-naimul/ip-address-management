.PHONY: help setup build up down restart logs shell migrate seed test clean

# Default target
help: ## Show this help message
	@echo "Available commands:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  %-15s %s\n", $$1, $$2}'

setup: ## Initial setup - copy env, build containers, install dependencies
	cp .env.example .env
	cd docker && docker compose build --no-cache
	cd docker && docker compose run --rm --user root app sh -c "composer install"
	@echo "Setup complete! Run 'make up' to start the application."

build: ## Build the Docker containers
	cd docker && docker compose build

up: ## Start the application containers
	cd docker && docker compose up -d

down: ## Stop the application containers
	cd docker && docker compose down

restart: ## Restart the application containers
	cd docker && docker compose restart

logs: ## Show application logs
	cd docker && docker compose logs -f app

shell: ## Open a shell in the application container
	cd docker && docker compose exec app bash

migrate: ## Run database migrations
	cd docker && docker compose run --rm app php /var/www/html/run-migrations.php

migrate-artisan: ## Run migrations using Artisan (if working)
	cd docker && docker compose run --rm app php artisan migrate

seed: ## Run database seeders
	cd docker && docker compose run --rm app php artisan db:seed

test: ## Run the test suite
	cd docker && docker compose run --rm app php artisan test

key-generate: ## Generate application key
	cd docker && docker compose run --rm app php artisan key:generate

clean: ## Clean up containers and volumes
	cd docker && docker compose down -v --remove-orphans
	docker system prune -f

install: ## Install PHP dependencies
	cd docker && docker compose run --rm --user root app sh -c "composer install"

update: ## Update PHP dependencies
	cd docker && docker compose run --rm --user root app sh -c "composer update"