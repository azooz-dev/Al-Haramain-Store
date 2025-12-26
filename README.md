<p align="center">
  <h1 align="center">Al-Haramain Store</h1>
  <p align="center">
    <strong>Enterprise-Grade E-Commerce Backend Platform</strong>
  </p>
  <p align="center">
    A production-ready, modular e-commerce API infrastructure built with Laravel 12, featuring HMVC architecture, comprehensive testing, and containerized deployment.
  </p>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/Docker-Ready-2496ED?style=flat-square&logo=docker&logoColor=white" alt="Docker">
  <img src="https://img.shields.io/badge/Tests-PHPUnit-6C5CE7?style=flat-square" alt="PHPUnit">
  <img src="https://img.shields.io/badge/CI-GitHub_Actions-2088FF?style=flat-square&logo=github-actions&logoColor=white" alt="GitHub Actions">
</p>

---

## Project Overview

**Al-Haramain Store** is a comprehensive backend platform designed for online retail operations at scale. The system provides a complete RESTful API infrastructure for managing products with multi-variant support, order processing with payment integration, customer relationships, and real-time business analytics.

### Target Scale & Intent

- **API-First Architecture**: Designed to serve web SPAs, mobile applications, and third-party integrations
- **Enterprise Operations**: Multi-language support (English/Arabic), role-based access control, comprehensive audit logging
- **Horizontal Scalability**: Stateless API design with Redis-backed caching, sessions, and queues
- **Production Deployment**: Docker-ready with multi-stage builds, health checks, and orchestration

### Core Business Capabilities

| Domain | Capability |
|--------|------------|
| **Catalog** | Multi-variant products (color, size, pricing), hierarchical categories, internationalized content |
| **Orders** | Pipeline-based processing, stock validation, fulfillment tracking, status transitions |
| **Payments** | Stripe integration, cash-on-delivery, webhook handling, transaction records |
| **Analytics** | Real-time KPIs, revenue charts, customer metrics, product performance dashboards |
| **Promotions** | Coupon management with usage limits, promotional offers and bundles |

---

## Key Engineering Highlights

### Architecture & Design

- **HMVC Modular Architecture** — 11 self-contained business modules with clear domain boundaries using `nwidart/laravel-modules`
- **Interface-Based Dependency Injection** — All inter-module communication through contracts, enabling loose coupling and testability
- **Pipeline Pattern** — Order processing decomposed into 10 sequential, independently testable steps (validation → payment → fulfillment)
- **Event-Driven Architecture** — Cross-cutting concerns handled via Laravel Events (OrderCreated, OrderStatusChanged, ReviewCreated)
- **Repository Pattern** — Dedicated data access layer abstracting Eloquent operations from business logic

### Code Quality & Standards

- **Clean Layered Structure per Module** — Controllers → Services → Repositories → Entities with clear separation
- **PHP 8.2 Modern Features** — Typed properties, enums for type-safe statuses, constructor property promotion
- **Contract-First Design** — Service interfaces define module APIs; implementations are swappable
- **Form Request Validation** — Input validation and authorization decoupled from controllers
- **API Resource Transformers** — Consistent JSON responses through dedicated resource classes
- **Custom Exception Hierarchy** — Domain-specific exceptions with appropriate HTTP status codes

### Testing Strategy

- **Multi-Layer Test Suites** — Unit (isolated class tests), Integration (cross-module), Feature (API tests), E2E (full user flows)
- **Module-Scoped Tests** — Each module contains its own `Tests/` directory with Feature, Integration, and Unit subdirectories
- **Critical Path E2E Tests** — Complete purchase flows (Stripe, COD), review submission, admin management
- **Test Isolation** — Dedicated test database, array drivers for cache/session/mail, placeholder Stripe keys
- **Factory-Based Data** — Eloquent factories for all entities with translation support

### DevOps & Production Readiness

- **Multi-Stage Docker Build** — Composer dependencies → Frontend assets → Production image (PHP-FPM + Nginx + Supervisor)
- **Container Orchestration** — `docker-compose.prod.yml` with MySQL, Redis, health checks, named volumes
- **GitHub Actions CI** — Automated testing on push/PR to develop/main with MySQL and Redis services
- **Environment Separation** — `.env.production` for production, `.env.example` for development, CI-specific configuration
- **Process Management** — Supervisor managing PHP-FPM, queue workers, and scheduler

### Scalability Considerations

- **Stateless API** — Token-based auth (Sanctum) with no server-side session state
- **Redis Backing** — Cache, queue, and session stores configurable for Redis
- **Database Optimization** — Dedicated indexes, eager loading patterns, query builder abstractions
- **Queue Processing** — Background job infrastructure for async operations
- **WebSocket Support** — Laravel Reverb configuration for real-time features

---

## Architecture Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                      Client Applications                         │
│              (Web SPA / Mobile Apps / Third-Party)              │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                    API Gateway Layer                             │
│            (Laravel Routing + Sanctum Authentication)           │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                   HMVC Modules Layer                             │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐           │
│  │   Auth   │ │  Catalog │ │   Order  │ │  Payment │           │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘           │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐           │
│  │   User   │ │  Coupon  │ │   Offer  │ │  Review  │           │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘           │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐                        │
│  │ Favorite │ │Analytics │ │   Admin  │                        │
│  └──────────┘ └──────────┘ └──────────┘                        │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│               Service & Repository Layer                         │
│         (Business Logic + Data Access Abstraction)              │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                       Data Layer                                 │
│              (MySQL + Redis + Eloquent ORM)                     │
└─────────────────────────────────────────────────────────────────┘
```

### Module Structure

Each module follows a consistent internal organization:

```
Modules/{ModuleName}/
├── app/
│   ├── Contracts/          # Service interfaces
│   ├── DTOs/               # Data Transfer Objects
│   ├── Entities/           # Eloquent models
│   ├── Enums/              # PHP 8.1 enums
│   ├── Events/             # Domain events
│   ├── Exceptions/         # Custom exceptions
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Requests/       # Form validation
│   │   └── Resources/      # API transformers
│   ├── Repositories/
│   │   ├── Interface/
│   │   └── Eloquent/
│   └── Services/           # Business logic
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── api.php
└── Tests/
    ├── Feature/
    ├── Integration/
    └── Unit/
```

---

## Technology Stack

| Layer | Technology | Purpose |
|-------|------------|---------|
| Runtime | PHP 8.2+ | Modern PHP with typed properties, enums |
| Framework | Laravel 12.x | Application foundation |
| Modularity | nwidart/laravel-modules | HMVC architecture |
| API Auth | Laravel Sanctum | Token-based authentication |
| Admin Panel | Filament 3.3 | Administrative interface |
| Permissions | Spatie Permission + Filament Shield | RBAC |
| Translations | Astrotomic Translatable | Multi-language content |
| Payments | Stripe PHP SDK | Payment processing |
| Database | MySQL 8.0+ / PostgreSQL 12+ | Primary data store |
| Caching | Redis 7+ | Cache, queue, sessions |
| Testing | PHPUnit 11 | Test framework |
| CI/CD | GitHub Actions | Automated testing |
| Containers | Docker + Docker Compose | Deployment |

---

## Branch Strategy

The repository follows a **GitFlow**-inspired branching model:

| Branch Type | Pattern | Purpose |
|-------------|---------|---------|
| `main` | Protected | Production-ready code |
| `develop` | Integration | Development integration |
| `feature/*` | `feature/add-*`, `feature/implement-*` | New functionality |
| `fix/*` | `fix/multi-feature-*` | Bug fixes |
| `test/*` | `test/*-complete`, `test/fix-*` | Test improvements |
| `refactor/*` | `refactor/hmvc-*` | Code restructuring |
| `doc/*` | `doc/create-*` | Documentation |

**Commit Conventions**: Conventional commits with type prefixes (`feat:`, `fix:`, `refactor:`, `test:`, `docs:`)

---

## Quick Start

### Prerequisites

- Docker & Docker Compose
- PHP 8.2+ (for local development)
- Composer
- Node.js 20+

### Local Development (Laravel Sail)

```bash
# Clone repository
git clone https://github.com/azooz-dev/Al-Haramain-Store.git
cd Al-Haramain-Store

# Install dependencies
composer install
npm ci

# Configure environment
cp .env.example .env
php artisan key:generate

# Start development environment
./vendor/bin/sail up -d

# Run migrations and seed
./vendor/bin/sail artisan migrate --seed

# Build frontend assets
npm run dev
```

### Production Deployment

```bash
# Configure production environment
cp .env.production.example .env.production
# Edit .env.production with actual credentials

# Build and start containers
docker compose -f docker-compose.prod.yml up -d

# Run migrations (with AUTO_MIGRATE=true in env, or manually)
docker exec alharamain-app php artisan migrate --force
```

---

## Testing

```bash
# Run all tests
php artisan test

# Run specific test suites
php artisan test --testsuite=Module-Unit
php artisan test --testsuite=Module-Feature
php artisan test --testsuite=E2E

# Run with stop on failure
php artisan test --stop-on-failure
```

### Test Suite Structure

| Suite | Location | Scope |
|-------|----------|-------|
| Module-Unit | `Modules/*/Tests/Unit` | Class-level isolation |
| Module-Integration | `Modules/*/Tests/Integration` | Cross-module interactions |
| Module-Feature | `Modules/*/Tests/Feature` | API endpoint testing |
| E2E | `tests/E2E` | Complete user flows |

---

## API Documentation

Base URL: `/api/v1`

### Authentication
- `POST /register` — User registration
- `POST /login` — User login (returns bearer token)
- `POST /logout` — User logout

### Catalog
- `GET /categories` — List categories
- `GET /products` — List products
- `GET /products/{id}` — Product details

### Orders
- `GET /orders` — User orders
- `POST /orders` — Create order
- `GET /orders/{id}` — Order details

### Admin Panel
- `/admin` — Filament administrative interface

For detailed API documentation, see [`docs/TECHNICAL_DOCUMENTATION.md`](docs/TECHNICAL_DOCUMENTATION.md).

---

## Documentation

- **[Technical Documentation](docs/TECHNICAL_DOCUMENTATION.md)** — Comprehensive system architecture, module details, and API specifications

---

## License

This project is proprietary software developed for Al-Haramain Store.

---

<p align="center">
  <sub>Built with ❤️ by <a href="https://github.com/azooz-dev">Abdulaziz Alameri</a></sub>
</p>
