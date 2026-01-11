# ===========================================
# Al-Haramain Store - Development Commands
# ===========================================

.PHONY: dev dev-build dev-down dev-logs dev-shell dev-shell-frontend dev-fresh \
        sail sail-build sail-down sail-logs sail-shell \
        prod-build prod-up prod-down

# ===========================================
# Unified Development (Frontend + Backend)
# ===========================================

# Start unified development (frontend + backend)
dev:
	docker-compose -f docker-compose.dev.yml up

# Build and start
dev-build:
	docker-compose -f docker-compose.dev.yml up --build

# Stop all services
dev-down:
	docker-compose -f docker-compose.dev.yml down

# View logs
dev-logs:
	docker-compose -f docker-compose.dev.yml logs -f

# Shell into backend
dev-shell:
	docker-compose -f docker-compose.dev.yml exec laravel.test bash

# Shell into frontend
dev-shell-frontend:
	docker-compose -f docker-compose.dev.yml exec frontend sh

# Fresh database
dev-fresh:
	docker-compose -f docker-compose.dev.yml exec laravel.test php artisan migrate:fresh --seed

# Run artisan command (usage: make artisan cmd="migrate")
dev-artisan:
	docker-compose -f docker-compose.dev.yml exec laravel.test php artisan $(cmd)

# ===========================================
# Laravel Sail (Backend Only)
# ===========================================

# Start sail (backend only, uses docker-compose.yml)
sail:
	./vendor/bin/sail up

# Build and start sail
sail-build:
	./vendor/bin/sail build --no-cache && ./vendor/bin/sail up

# Stop sail
sail-down:
	./vendor/bin/sail down

# Sail logs
sail-logs:
	./vendor/bin/sail logs -f

# Shell into sail container
sail-shell:
	./vendor/bin/sail shell

# ===========================================
# Production Build (Local Testing)
# ===========================================

# Build production images
prod-build:
	docker-compose -f docker-compose.prod.yml build

# Start production containers
prod-up:
	docker-compose -f docker-compose.prod.yml up -d

# Stop production containers
prod-down:
	docker-compose -f docker-compose.prod.yml down

# ===========================================
# Database Commands
# ===========================================

# Run migrations
migrate:
	docker-compose -f docker-compose.dev.yml exec laravel.test php artisan migrate

# Run seeders
seed:
	docker-compose -f docker-compose.dev.yml exec laravel.test php artisan db:seed

# Fresh migrate and seed
fresh:
	docker-compose -f docker-compose.dev.yml exec laravel.test php artisan migrate:fresh --seed

# ===========================================
# Cache Commands
# ===========================================

# Clear all caches
cache-clear:
	docker-compose -f docker-compose.dev.yml exec laravel.test php artisan optimize:clear

# Cache for production
cache:
	docker-compose -f docker-compose.dev.yml exec laravel.test php artisan optimize

# ===========================================
# Testing
# ===========================================

# Run all tests
test:
	docker-compose -f docker-compose.dev.yml exec laravel.test php artisan test

# Run tests with coverage
test-coverage:
	docker-compose -f docker-compose.dev.yml exec laravel.test php artisan test --coverage

# ===========================================
# Utility
# ===========================================

# Install dependencies
install:
	docker-compose -f docker-compose.dev.yml exec laravel.test composer install
	docker-compose -f docker-compose.dev.yml exec frontend npm install

# Update dependencies
update:
	docker-compose -f docker-compose.dev.yml exec laravel.test composer update
	docker-compose -f docker-compose.dev.yml exec frontend npm update

# Show running containers
ps:
	docker-compose -f docker-compose.dev.yml ps

# Remove all containers and volumes (CAUTION: destroys data!)
clean:
	docker-compose -f docker-compose.dev.yml down -v --remove-orphans

# ===========================================
# Help
# ===========================================

help:
	@echo "Al-Haramain Store - Available Commands"
	@echo ""
	@echo "Unified Development (Frontend + Backend):"
	@echo "  make dev              - Start full stack development"
	@echo "  make dev-build        - Build and start all containers"
	@echo "  make dev-down         - Stop all containers"
	@echo "  make dev-logs         - View logs from all services"
	@echo "  make dev-shell        - Shell into backend container"
	@echo "  make dev-shell-frontend - Shell into frontend container"
	@echo "  make dev-fresh        - Fresh migration with seeding"
	@echo ""
	@echo "Laravel Sail (Backend Only):"
	@echo "  make sail             - Start backend only"
	@echo "  make sail-down        - Stop sail"
	@echo "  make sail-shell       - Shell into sail container"
	@echo ""
	@echo "Database:"
	@echo "  make migrate          - Run migrations"
	@echo "  make seed             - Run seeders"
	@echo "  make fresh            - Fresh migrate and seed"
	@echo ""
	@echo "Testing:"
	@echo "  make test             - Run all tests"
	@echo "  make test-coverage    - Run tests with coverage"
	@echo ""
	@echo "Access Points (when running 'make dev'):"
	@echo "  Frontend:    http://localhost:3000"
	@echo "  Backend API: http://localhost:80/api"
	@echo "  Admin Panel: http://localhost:80/admin"
	@echo "  phpMyAdmin:  http://localhost:8080"
	@echo "  Mailpit:     http://localhost:8025"
