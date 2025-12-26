# Al-Haramain Store - Enterprise Technical Documentation

**Version:** 1.0.0  
**Last Updated:** December 26, 2024  
**Document Status:** Official  

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [System Architecture](#2-system-architecture)
3. [Technology Stack](#3-technology-stack)
4. [Project Structure](#4-project-structure)
5. [Core Domains & Modules](#5-core-domains--modules)
6. [Data Flow & Request Lifecycle](#6-data-flow--request-lifecycle)
7. [Authentication & Authorization](#7-authentication--authorization)
8. [Database Design](#8-database-design)
9. [Error Handling & Logging](#9-error-handling--logging)
10. [Testing Strategy](#10-testing-strategy)
11. [Environment Setup](#11-environment-setup)
12. [Deployment & CI/CD](#12-deployment--cicd)
13. [Coding Standards & Conventions](#13-coding-standards--conventions)
14. [Scalability & Future Improvements](#14-scalability--future-improvements)

---

## 1. Project Overview

### 1.1 Purpose of the System

**Al-Haramain Store** is a comprehensive, enterprise-grade e-commerce platform designed to facilitate online retail operations. The system provides a complete backend API infrastructure for managing products, orders, payments, customer relationships, and business analytics.

### 1.2 High-Level Business Problem It Solves

The platform addresses the following business challenges:

- **Product Catalog Management**: Enables businesses to manage complex product catalogs with multi-variant support (colors, sizes, pricing), multi-language translations, and hierarchical categorization.
- **Order Processing**: Provides a robust, pipeline-based order processing system that handles stock validation, payment processing, coupon application, and fulfillment tracking.
- **Payment Integration**: Offers secure payment processing through Stripe for credit card transactions and supports cash-on-delivery for flexibility.
- **Customer Engagement**: Facilitates customer reviews, wishlists/favorites, and promotional offers to drive engagement and sales.
- **Business Intelligence**: Delivers real-time analytics and reporting for administrators to monitor revenue, orders, customers, and product performance.
- **Internationalization**: Supports bilingual content (English and Arabic) to serve international markets.

### 1.3 Target Users and Stakeholders

| User Type | Description | Primary Interactions |
|-----------|-------------|---------------------|
| **Customers** | End-users who browse products, place orders, and manage their accounts | API endpoints for browsing, ordering, reviews, favorites |
| **Administrators** | Store managers and staff who manage inventory, orders, and customers | Filament admin panel for CRUD operations and analytics |
| **Super Administrators** | System administrators with full access to all modules and configuration | Full admin panel access including role/permission management |
| **Frontend Developers** | Engineers building web/mobile clients consuming the API | RESTful API endpoints with JSON responses |
| **System Integrators** | Third-party systems integrating via webhooks and APIs | Stripe webhooks, API integrations |

### 1.4 System Boundaries and Responsibilities

#### In Scope

- RESTful API for e-commerce operations
- Product catalog with multi-variant and multi-language support
- Order lifecycle management (creation to delivery)
- Payment processing (Stripe, Cash on Delivery)
- Customer account management
- Review and rating system
- Coupon and promotional offer management
- Admin panel for store management
- Real-time analytics dashboard
- Role-based access control

#### Out of Scope

- Frontend web/mobile applications (assumed separate)
- Shipping carrier integration (assumed external)
- Tax calculation engine (assumed external)
- Multi-currency support
- Inventory management beyond stock tracking
- Customer service/support ticketing

---

## 2. System Architecture

### 2.1 Overall Architecture Style

The system follows a **Hierarchical Model-View-Controller (HMVC)** modular architecture pattern, built on the Laravel 12 framework and leveraging the **nwidart/laravel-modules** package for module management.

```
┌─────────────────────────────────────────────────────────────────────────┐
│                           Client Applications                            │
│                    (Web SPA / Mobile Apps / Third-party)                │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                           API Gateway Layer                              │
│               (Laravel Routing + Sanctum Authentication)                │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                          Middleware Layer                                │
│           (Authentication, Locale, Rate Limiting, CORS)                 │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                         HMVC Modules Layer                               │
│  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐            │
│  │  Auth   │ │ Catalog │ │  Order  │ │ Payment │ │  User   │            │
│  └─────────┘ └─────────┘ └─────────┘ └─────────┘ └─────────┘            │
│  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐            │
│  │ Coupon  │ │  Offer  │ │ Review  │ │Favorite │ │Analytics│            │
│  └─────────┘ └─────────┘ └─────────┘ └─────────┘ └─────────┘            │
│  ┌─────────┐                                                            │
│  │  Admin  │                                                            │
│  └─────────┘                                                            │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                       Service & Repository Layer                         │
│              (Business Logic + Data Access Abstraction)                 │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                           Data Layer                                     │
│                   (MySQL/PostgreSQL + Eloquent ORM)                     │
└─────────────────────────────────────────────────────────────────────────┘
```

### 2.2 Major System Components and Interactions

#### Module Communication Patterns

The architecture employs two primary patterns for inter-module communication:

**Pattern 1: Event-Driven Communication**

Used when one module needs to notify other modules about state changes without creating direct dependencies.

```
┌──────────┐      Event        ┌───────────────────┐      Listener     ┌──────────┐
│  Order   │ ──────────────▶  │   Event Dispatcher │ ───────────────▶ │  Admin   │
│  Module  │  OrderCreated    │     (Laravel)      │                   │  Module  │
└──────────┘                  └───────────────────┘                   └──────────┘
                                      │
                                      │ Listener
                                      ▼
                              ┌──────────────┐
                              │  Analytics   │
                              │   Module     │
                              └──────────────┘
```

**Pattern 2: Interface-Based Service Communication**

Used when one module needs functionality from another module, maintaining loose coupling through contracts.

```
┌──────────────┐           ┌─────────────────────────┐          ┌──────────────┐
│    Order     │           │   PaymentService        │          │   Payment    │
│   Module     │ ─────────▶│   Interface             │◀──────── │   Module     │
│              │  depends  │                         │ implements│              │
│ProcessPayment│  on       │ processPayment(...)     │          │PaymentService│
│    Step      │           │                         │          │              │
└──────────────┘           └─────────────────────────┘          └──────────────┘
```

### 2.3 Request Lifecycle Overview

```
HTTP Request
     │
     ▼
┌─────────────────┐
│   index.php     │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Bootstrap     │
│   Application   │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Middleware    │
│   Pipeline      │
│  ┌───────────┐  │
│  │ auth:sanctum│ │
│  │ set.locale │ │
│  │ throttle   │ │
│  └───────────┘  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   Route         │
│   Resolution    │
└────────┬────────┘
         │
         ▼
┌─────────────────────────────────────────┐
│        Module Controller                 │
│  ┌─────────────────────────────────────┐│
│  │     Form Request Validation         ││
│  └─────────────────────────────────────┘│
│  ┌─────────────────────────────────────┐│
│  │     Service Layer (Business Logic)  ││
│  └─────────────────────────────────────┘│
│  ┌─────────────────────────────────────┐│
│  │     Repository (Data Access)        ││
│  └─────────────────────────────────────┘│
└────────┬────────────────────────────────┘
         │
         ▼
┌─────────────────┐
│   API Resource  │
│   Transform     │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ JSON Response   │
└─────────────────┘
```

### 2.4 Design Decisions and Trade-offs

| Decision | Rationale | Trade-off |
|----------|-----------|-----------|
| **HMVC Modular Architecture** | Enables independent development, testing, and deployment of modules. Supports team scaling. | Increased initial complexity; requires discipline to maintain module boundaries. |
| **Interface-Based Module Communication** | Loose coupling; easy to mock for testing; implementations can be swapped without modifying consumers. | Additional abstraction layer; more files to maintain. |
| **Event-Driven Notifications** | Decouples modules; supports multiple listeners; enables async processing. | Debugging can be more complex; event flow not immediately visible. |
| **Pipeline Pattern for Orders** | Clear separation of concerns; easy to add/remove/reorder steps; each step is independently testable. | More classes to manage; need to understand pipeline flow. |
| **API-First Design** | Supports multiple frontends; future-proof for mobile apps and integrations. | No server-side rendering; requires separate frontend development. |
| **Filament Admin Panel** | Rapid admin UI development; consistent UX; built-in components. | Tied to Filament's paradigm; customization has limits. |

---

## 3. Technology Stack

### 3.1 Backend Technologies

| Technology | Version | Purpose | Rationale |
|------------|---------|---------|-----------|
| **PHP** | 8.2+ | Runtime | Modern PHP features (typed properties, enums, named arguments); performance improvements |
| **Laravel** | 12.x | Framework | Industry-standard PHP framework; extensive ecosystem; excellent documentation |
| **Laravel Modules** | 12.x | Module Management | nwidart/laravel-modules provides HMVC structure for large applications |
| **Laravel Sanctum** | 4.2 | API Authentication | Lightweight token-based auth for SPAs and mobile apps |
| **Composer** | Latest | Dependency Management | PHP package management with autoloading |

### 3.2 Frontend Technologies (Admin Panel)

| Technology | Version | Purpose |
|------------|---------|---------|
| **Filament** | 3.3 | Admin Panel Framework |
| **Livewire** | 3.6 | Dynamic UI Components |
| **Alpine.js** | Bundled | Client-side Interactivity |
| **Vite** | Latest | Asset Bundling |

### 3.3 Database & Storage

| Technology | Configuration | Purpose |
|------------|--------------|---------|
| **MySQL** | 8.0+ / PostgreSQL 12+ | Primary Database |
| **Eloquent ORM** | Built-in | Object-Relational Mapping |
| **Redis** | 6.0+ | Cache, Queue, and Session Storage |
| **Local/S3 Filesystem** | Configurable | File Storage |

### 3.4 Queues & Background Jobs

| Technology | Configuration | Purpose | Rationale |
|------------|--------------|---------|----------|
| **Laravel Queue** | Redis driver | Background Job Processing | Optimized for performance and scalability |
| **Redis** | 6.0+ | Queue backend for optimized job processing | High-performance, in-memory data store for fast job handling |
| **Laravel Reverb** | 1.0 | WebSocket Server | |
| **Pusher** | Optional | Real-time Broadcasting | |

### 3.5 Third-party Services and Integrations

| Service | Package | Purpose |
|---------|---------|---------|
| **Stripe** | stripe/stripe-php v18.2 | Credit Card Payment Processing |
| **Spatie Permission** | spatie/laravel-permission | Role & Permission Management |
| **Filament Shield** | bezhansalleh/filament-shield v3.3 | Admin Panel Permissions |
| **Laravel Translatable** | astrotomic/laravel-translatable v11.16 | Multi-language Content |

---

## 4. Project Structure

### 4.1 Directory Structure Explanation

```
Al-Haramain-Store/
├── app/                          # Core application code
│   ├── Filament/                 # Filament admin panel components
│   ├── Helpers/                  # Global helper functions
│   ├── Http/                     # Core HTTP components
│   ├── Policies/                 # Authorization policies
│   ├── Providers/                # Service providers
│   ├── Services/                 # Shared services
│   └── Traits/                   # Shared traits
│
├── Modules/                      # HMVC Modules (Primary Business Logic)
│   ├── Admin/                    # Admin user management
│   ├── Analytics/                # Dashboard & reporting
│   ├── Auth/                     # Authentication
│   ├── Catalog/                  # Products & categories
│   ├── Coupon/                   # Discount coupons
│   ├── Favorite/                 # Wishlist functionality
│   ├── Offer/                    # Promotional offers
│   ├── Order/                    # Order management
│   ├── Payment/                  # Payment processing
│   ├── Review/                   # Product reviews
│   └── User/                     # Customer management
│
├── config/                       # Configuration files
├── database/                     # Migrations, seeders, factories
├── docs/                         # Documentation
├── lang/                         # Language files (en, ar)
├── public/                       # Public assets
├── resources/                    # Views, CSS, JS
├── routes/                       # Route definitions
├── storage/                      # Logs, cache, uploads
├── tests/                        # Test suites
└── vendor/                       # Composer dependencies
```

### 4.2 Purpose of Each Major Folder/Module

| Directory | Purpose |
|-----------|---------|
| `app/` | Core application code shared across modules |
| `Modules/` | Self-contained HMVC modules with own MVC structure |
| `config/` | Application configuration (database, auth, cache, etc.) |
| `database/` | Schema migrations and data seeders |
| `lang/` | Internationalization files for English and Arabic |
| `routes/` | API and web route definitions |
| `tests/` | Automated test suites (E2E, Feature, Unit) |

### 4.3 Module Structure (Standard Template)

Each module follows a consistent internal structure:

```
Modules/{ModuleName}/
├── app/
│   ├── Contracts/              # Service interfaces for cross-module communication
│   ├── DTOs/                   # Data Transfer Objects
│   ├── Entities/               # Eloquent models (domain entities)
│   ├── Enums/                  # PHP 8.1 enums
│   ├── Events/                 # Event classes dispatched by this module
│   ├── Exceptions/             # Module-specific exceptions
│   ├── Http/
│   │   ├── Controllers/        # HTTP controllers (API endpoints)
│   │   ├── Requests/           # Form request validation classes
│   │   └── Resources/          # API resource transformers
│   ├── Listeners/              # Event listeners
│   ├── Observers/              # Eloquent model observers
│   ├── Policies/               # Authorization policies
│   ├── Providers/              # Module service providers
│   ├── Repositories/           # Data access layer
│   │   ├── Interface/          # Repository interfaces
│   │   └── Eloquent/           # Eloquent implementations
│   ├── Rules/                  # Custom validation rules
│   └── Services/               # Business logic layer
├── config/                     # Module configuration
├── database/
│   ├── factories/              # Model factories for testing
│   ├── migrations/             # Database migrations
│   └── seeders/                # Data seeders
├── routes/
│   ├── api.php                 # API routes
│   └── web.php                 # Web routes (if applicable)
├── Tests/
│   ├── Feature/                # Feature/API tests
│   ├── Integration/            # Cross-module integration tests
│   └── Unit/                   # Unit tests
└── composer.json               # Module dependencies
```

### 4.4 Naming Conventions and Standards

| Element | Convention | Example |
|---------|------------|---------|
| **Module Names** | PascalCase, singular | `Order`, `Catalog`, `Payment` |
| **Controllers** | PascalCase + Controller | `OrderController`, `ProductController` |
| **Services** | PascalCase + Service | `OrderService`, `PaymentService` |
| **Repositories** | PascalCase + Repository | `OrderRepository`, `ProductRepository` |
| **Interfaces** | PascalCase + Interface | `OrderServiceInterface`, `PaymentServiceInterface` |
| **Entities/Models** | PascalCase, singular | `Order`, `Product`, `User` |
| **Events** | PascalCase, past tense | `OrderCreated`, `PaymentProcessed` |
| **Listeners** | PascalCase, action-based | `SendOrderNotification` |
| **Tables** | snake_case, plural | `orders`, `products`, `product_variants` |
| **Columns** | snake_case | `created_at`, `order_number`, `total_amount` |

---

## 5. Core Domains & Modules

### 5.1 Admin Module

**Business Responsibility:** Manages system administrators, their authentication via Filament, and role-based access to the admin panel.

**Public Interfaces:**
- Web routes: `/admin/*` (Filament admin panel)

**Internal Services & Classes:**
- `Admin` entity with `HasRoles` trait (Spatie Permission)
- `AdminResource` for Filament CRUD operations

**Database Entities:**
- `admins` - Admin user accounts

**Key Workflows:**
1. Admin authentication via Filament guard
2. Role/permission assignment via Filament Shield
3. Admin panel access control via `canAccessPanel()` method

---

### 5.2 Analytics Module

**Business Responsibility:** Provides dashboard widgets, KPI statistics, and business intelligence for administrators.

**Public Interfaces:**
- Filament dashboard widgets (no public API)

**Internal Services:**
- `DashboardWidgetServiceInterface` - KPI calculations
- `OrderAnalyticsServiceInterface` - Order-related metrics
- `CustomerAnalyticsServiceInterface` - Customer metrics
- `ProductAnalyticsServiceInterface` - Product performance

**Dashboard Widgets:**
- KPI Stats (revenue, orders, AOV, new customers)
- Revenue Overview (time-series charts)
- Order Status Distribution (doughnut chart)
- Top Products
- Customer Analytics
- Recent Orders
- Review Analytics

---

### 5.3 Auth Module

**Business Responsibility:** Handles customer authentication, registration, email verification, and password reset.

**Public Interfaces:**
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/register` | POST | User registration |
| `/api/login` | POST | User login |
| `/api/logout` | POST | User logout (authenticated) |
| `/api/user` | GET | Get authenticated user |
| `/api/users/email/verify-code` | POST | Verify email with code |
| `/api/users/email/resend-code` | POST | Resend verification (rate-limited) |
| `/api/forget-password` | POST | Request password reset |
| `/api/reset-password` | POST | Reset password |

**Key Workflows:**
1. Registration → Email Verification → Login
2. Forgot Password → Reset Token → Password Reset
3. Sanctum token-based API authentication

---

### 5.4 Catalog Module

**Business Responsibility:** Manages the product catalog including products, categories, variants, colors, and multi-language translations.

**Public Interfaces:**
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/categories` | GET | List categories |
| `/api/categories/{id}` | GET | Get category details |
| `/api/products` | GET | List products |
| `/api/products/{id}` | GET | Get product details |

**Internal Services:**
- `ProductServiceInterface` - Product operations
- `ProductVariantServiceInterface` - Variant management
- `ProductStockServiceInterface` - Stock management

**Database Entities:**
- `products` + `product_translations`
- `categories` + `category_translations`
- `product_colors` + `product_color_images`
- `product_variants`

**Key Workflows:**
1. Product creation with colors, variants, and translations
2. Stock tracking and validation
3. Price range calculation from variants

---

### 5.5 Coupon Module

**Business Responsibility:** Manages discount coupons with validation rules, usage limits, and discount calculations.

**Public Interfaces:**
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/coupons/{code}/{userId}` | GET | Apply/validate coupon |

**Internal Services:**
- `CouponServiceInterface` - Coupon validation and application

**Database Entities:**
- `coupons` - Coupon definitions
- `coupon_users` - Usage tracking

**Business Rules:**
- Fixed or percentage discount types
- Global and per-user usage limits
- Date-based validity periods
- Status-based activation

---

### 5.6 Favorite Module

**Business Responsibility:** Provides wishlist/favorites functionality for customers.

**Public Interfaces:**
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/users/{id}/favorites` | GET | List user favorites |
| `/api/users/{userId}/products/{productId}/colors/{colorId}/variants/{variantId}/favorites` | POST | Add to favorites |
| `/api/users/{id}/favorites/{favoriteId}` | DELETE | Remove favorite |

**Database Entities:**
- `favorites` - Links users to specific product variants

---

### 5.7 Offer Module

**Business Responsibility:** Manages promotional offers/bundles combining multiple products at discounted prices.

**Public Interfaces:**
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/offers` | GET | List offers |
| `/api/offers/{id}` | GET | Get offer details |

**Internal Services:**
- `OfferServiceInterface` - Offer retrieval operations

**Database Entities:**
- `offers` + `offer_translations`
- `products_offers` (pivot with variant/color/quantity)

---

### 5.8 Order Module

**Business Responsibility:** Manages the complete order lifecycle from creation through delivery using a pipeline-based processing system.

**Public Interfaces:**
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/orders` | GET | List user orders |
| `/api/orders` | POST | Create order |
| `/api/orders/{id}` | GET | Get order details |

**Internal Services:**
- `OrderServiceInterface` - Order operations
- **Pipeline Steps:**
  1. `ValidateBuyerStep` - Verify authenticated user
  2. `ValidateStockStep` - Check stock availability
  3. `CalculatePricesStep` - Calculate item and total prices
  4. `ApplyCouponStep` - Apply coupon discount
  5. `ProcessPaymentStep` - Process payment
  6. `CreateOrderStep` - Create order record
  7. `CreateOrderItemsStep` - Create order items
  8. `UpdateStockStep` - Deduct stock
  9. `RecordPaymentStep` - Record payment transaction

**Database Entities:**
- `orders` - Order headers
- `order_items` - Polymorphic order lines (products or offers)

**Events Dispatched:**
- `OrderCreated` - After successful order creation
- `OrderStatusChanged` - On status transitions

**Order Status Transitions:**
```
PENDING ──────┬──→ PROCESSING ──┬──→ SHIPPED ──┬──→ DELIVERED ──→ REFUNDED
              │                 │              │
              └──→ CANCELLED ◄──┴──────────────┘
```

---

### 5.9 Payment Module

**Business Responsibility:** Handles payment processing including Stripe integration and cash-on-delivery support.

**Public Interfaces:**
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/payments/create-intent` | POST | Create Stripe payment intent |
| `/api/stripe/webhook` | POST | Stripe webhook handler |

**Internal Services:**
- `PaymentServiceInterface` - Payment processing
- `StripePaymentProcessor` - Stripe implementation
- `CashOnDeliveryProcessor` - COD implementation
- `WebhookService` - Signature validation

**Database Entities:**
- `payments` - Payment transactions

**Payment Methods:**
- `CREDIT_CARD` - Via Stripe
- `CASH_ON_DELIVERY` - Manual payment on delivery

---

### 5.10 Review Module

**Business Responsibility:** Manages product reviews submitted by customers who have purchased items.

**Public Interfaces:**
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/reviews` | GET | List reviews |
| `/api/reviews/{id}` | GET | Get review details |
| `/api/users/{userId}/orders/{orderId}/items/{itemId}/reviews` | POST | Create review |

**Database Entities:**
- `reviews` - Review content with status

**Business Rules:**
- Reviews only for purchased items
- One review per order item
- Moderation workflow (PENDING → APPROVED/REJECTED)

**Events Dispatched:**
- `ReviewCreated`
- `ReviewUpdated`

---

### 5.11 User Module

**Business Responsibility:** Manages customer accounts, profiles, and shipping addresses.

**Public Interfaces:**
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/users/{id}` | PUT | Update profile |
| `/api/users/{id}` | DELETE | Delete account |
| `/api/users/{id}/addresses` | GET | List addresses |
| `/api/users/{id}/addresses` | POST | Create address |
| `/api/users/{id}/addresses/{addressId}` | PUT | Update address |
| `/api/users/{id}/addresses/{addressId}` | DELETE | Delete address |

**Database Entities:**
- `users` - Customer accounts
- `addresses` - Shipping/billing addresses

**Address Types:**
- `HOME`
- `WORK`
- `OTHER`

---

## 6. Data Flow & Request Lifecycle

### 6.1 From HTTP Request to Response

```
1. HTTP Request arrives at public/index.php
         │
         ▼
2. Laravel Application Bootstrap
   - Load configuration
   - Register service providers
   - Boot application
         │
         ▼
3. HTTP Kernel Pipeline
   - Global middleware execution
         │
         ▼
4. Route Resolution
   - Match route to controller action
   - Apply route-specific middleware
         │
         ▼
5. Controller Action
   - Form Request Validation (if applicable)
   - Service Method Invocation
   - Repository Data Access
         │
         ▼
6. Response Transformation
   - API Resource transformation
   - JSON encoding
         │
         ▼
7. HTTP Response
```

### 6.2 Middleware Usage

| Middleware | Purpose | Applied To |
|------------|---------|------------|
| `auth:sanctum` | Token-based API authentication | Protected API routes |
| `set.locale` | Set application locale from request | All API routes |
| `throttle` | Rate limiting | API routes |
| `verified` | Email verification check | Sensitive operations |

### 6.3 Validation and Authorization Flow

```
Request → Form Request → Validation Rules → Authorization Check → Controller
                │                                    │
                ▼                                    ▼
         Validation Errors             Authorization Denied (403)
         Return 422 Response           Return 403 Response
```

### 6.4 Error Handling Strategy

- **Validation Errors (422):** Returned with field-specific error messages
- **Authentication Errors (401):** Missing or invalid token
- **Authorization Errors (403):** Insufficient permissions
- **Not Found Errors (404):** Resource not found
- **Business Logic Errors:** Custom exceptions with specific messages
- **Server Errors (500):** Logged with full stack trace

---

## 7. Authentication & Authorization

### 7.1 Authentication Mechanisms

#### API Authentication (Customers)

- **Technology:** Laravel Sanctum with personal access tokens
- **Token Type:** Bearer tokens
- **Header Format:** `Authorization: Bearer {token}`
- **Token Storage:** `personal_access_tokens` table
- **Session:** Stateless API authentication

#### Admin Authentication

- **Technology:** Filament with session-based auth
- **Guard:** Custom `admin` guard
- **Provider:** `admins` user provider
- **Model:** `Modules\Admin\Entities\Admin`

### 7.2 Guards Configuration

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'admin' => [
        'driver' => 'session',
        'provider' => 'admins',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => Modules\User\Entities\User::class,
    ],
    'admins' => [
        'driver' => 'eloquent',
        'model' => Modules\Admin\Entities\Admin::class,
    ],
],
```

### 7.3 Roles and Permissions Model

- **Package:** Spatie Permission with Filament Shield
- **Admin Role:** `super_admin` with all permissions
- **Permission Granularity:** Resource-level (view, create, update, delete)
- **Implementation:** `HasRoles` trait on Admin model

### 7.4 Security Considerations

| Concern | Implementation |
|---------|----------------|
| Password Hashing | Bcrypt with configurable rounds |
| Token Security | Cryptographically secure token generation |
| CSRF Protection | Built-in for web routes |
| Rate Limiting | Configurable throttle middleware |
| Webhook Signature | Stripe signature verification |
| Input Validation | Form Request classes |
| SQL Injection | Eloquent parameter binding |
| XSS Prevention | Blade auto-escaping |

---

## 8. Database Design

### 8.1 Database Philosophy

- **Normalization:** Third normal form (3NF) for most tables
- **Soft Deletes:** Used for products to maintain order history integrity
- **Polymorphism:** Used for order items (products or offers)
- **Translations:** Separate translation tables for i18n support
- **Indexing:** Performance indexes on frequently queried columns

### 8.2 Important Tables and Relationships

#### User Domain
```
users ─────────┬──→ addresses (1:N)
               ├──→ orders (1:N)
               ├──→ favorites (1:N)
               ├──→ reviews (1:N)
               └──→ coupon_users (1:N)
```

#### Catalog Domain
```
products ──────┬──→ product_translations (1:N)
               ├──→ product_colors ─────→ product_color_images (1:N)
               │                   └────→ product_variants (1:N)
               ├──→ product_variants (1:N)
               └──→ categories (N:M via category_product)
```

#### Order Domain
```
orders ────────┬──→ order_items (1:N) ──→ orderable (polymorphic: product/offer)
               ├──→ payments (1:N)
               ├──→ coupon (N:1)
               └──→ address (N:1)
```

### 8.3 Indexing Strategy

| Table | Index | Purpose |
|-------|-------|---------|
| `products` | `slug` (unique) | Fast slug-based lookups |
| `products` | `sku` | Inventory queries |
| `product_translations` | `product_id`, `locale` | Translation lookups |
| `product_variants` | `product_id`, `color_id` | Variant queries |
| `orders` | `user_id` | User order history |
| `orders` | `order_number` (unique) | Order lookup |
| `orders` | `status` | Status filtering |
| `personal_access_tokens` | `tokenable_type`, `tokenable_id` | Token lookups |

### 8.4 Performance Considerations

- **Eager Loading:** Used to prevent N+1 queries
- **Query Optimization:** Indexes on foreign keys and frequently filtered columns
- **Pagination:** All list endpoints support pagination
- **Caching:** Application-level caching for analytics
- **Connection Pooling:** Recommended for production

---

## 9. Error Handling & Logging

### 9.1 Exception Handling Strategy

| Exception Type | HTTP Status | Handling |
|----------------|-------------|----------|
| `ValidationException` | 422 | Return field-specific errors |
| `AuthenticationException` | 401 | Return "Unauthenticated" message |
| `AuthorizationException` | 403 | Return "Forbidden" message |
| `ModelNotFoundException` | 404 | Return "Not Found" message |
| `OutOfStockException` | 400 | Return stock error details |
| `PaymentFailedException` | 400 | Return payment error details |
| General `Exception` | 500 | Log and return generic error |

### 9.2 Custom Error Responses

```php
// Standard API Error Response Format
{
    "message": "Error description",
    "errors": {
        "field_name": ["Specific validation error"]
    }
}

// Standard API Success Response Format
{
    "data": { ... },
    "message": "Success message"
}
```

### 9.3 Logging Configuration

| Channel | Driver | Purpose |
|---------|--------|---------|
| `stack` | stack | Default channel combining multiple channels |
| `single` | single | Single log file at `storage/logs/laravel.log` |
| `daily` | daily | Daily rotated log files (14 days retention) |
| `slack` | slack | Critical error notifications |
| `stderr` | monolog | Container/serverless logging |

### 9.4 Logging Levels

| Level | Usage |
|-------|-------|
| `debug` | Development debugging |
| `info` | Business events (order created, payment processed) |
| `warning` | Non-critical issues (deprecated features, slow queries) |
| `error` | Errors that need attention |
| `critical` | System failures |

---

## 10. Testing Strategy

### 10.1 Test Types Used

| Type | Coverage | Location |
|------|----------|----------|
| **Unit Tests** | 70% | `Modules/*/Tests/Unit/` |
| **Integration Tests** | 25% | `Modules/*/Tests/Integration/` |
| **Feature Tests** | API endpoints | `Modules/*/Tests/Feature/` |
| **E2E Tests** | 5% | `tests/E2E/` |

### 10.2 Test Suite Structure

```
phpunit.xml Test Suites:
├── Module-Unit      → Modules/*/Tests/Unit
├── Module-Integration → Modules/*/Tests/Integration
├── Module-Feature   → Modules/*/Tests/Feature
├── E2E              → tests/E2E
├── Unit             → tests/Unit (legacy)
└── Feature          → tests/Feature (legacy)
```

### 10.3 Testing Philosophy

- **Unit Tests:** Test individual services, repositories, and pipeline steps in isolation
- **Integration Tests:** Test module-to-module communication via interfaces
- **Feature Tests:** Test API endpoints end-to-end with database
- **E2E Tests:** Test complete user journeys across multiple modules

### 10.4 Test Environment Configuration

```xml
<php>
    <env name="APP_ENV" value="testing"/>
    <env name="DB_DATABASE" value="al-haramain-store-test"/>
    <env name="CACHE_STORE" value="array"/>
    <env name="MAIL_MAILER" value="array"/>
    <env name="QUEUE_CONNECTION" value="sync"/>
    <env name="SESSION_DRIVER" value="array"/>
</php>
```

### 10.5 Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suites
vendor/bin/phpunit --testsuite Module-Unit
vendor/bin/phpunit --testsuite Module-Integration
vendor/bin/phpunit --testsuite Module-Feature
vendor/bin/phpunit --testsuite E2E

# Run specific module tests
vendor/bin/phpunit Modules/Order/Tests/

# Run with coverage
vendor/bin/phpunit --coverage-html coverage/
```

---

## 11. Environment Setup

### 11.1 Local Development Setup

**Prerequisites:**
- PHP 8.2 or higher
- Composer 2.x
- Node.js 18+ and npm
- MySQL 8.0+ or PostgreSQL 12+

**Installation Steps:**

```bash
# 1. Clone repository
git clone <repository-url>
cd Al-Haramain-Store

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Copy environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Configure database in .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=al-haramain-store
# DB_USERNAME=root
# DB_PASSWORD=

# 7. Run migrations
php artisan migrate

# 8. Seed database (optional)
php artisan db:seed

# 9. Start development servers
composer run dev
# This runs: php artisan serve, queue:listen, pail, npm run dev
```

### 11.2 Environment Variables

| Variable | Description | Example |
|----------|-------------|---------|
| `APP_ENV` | Environment (local, staging, production) | `local` |
| `APP_DEBUG` | Debug mode | `true` |
| `APP_URL` | Application URL | `http://localhost` |
| `APP_LOCALE` | Default locale | `en` |
| `DB_CONNECTION` | Database driver | `mysql` |
| `DB_DATABASE` | Database name | `al-haramain-store` |
| `REDIS_HOST` | Redis server host | `127.0.0.1` |
| `REDIS_PASSWORD` | Redis password (if set) | `null` |
| `REDIS_PORT` | Redis port | `6379` |
| `STRIPE_KEY` | Stripe public key | `pk_test_...` |
| `STRIPE_SECRET` | Stripe secret key | `sk_test_...` |
| `STRIPE_WEBHOOK_SECRET` | Webhook signing secret | `whsec_...` |
| `QUEUE_CONNECTION` | Queue driver | `redis` |
| `CACHE_STORE` | Cache driver | `redis` |
| `SESSION_DRIVER` | Session driver | `redis` |

### 11.3 Common Setup Issues

| Issue | Solution |
|-------|----------|
| Module not loading | Run `php artisan module:enable ModuleName` |
| Class not found | Run `composer dump-autoload` |
| Migration conflicts | Run `php artisan migrate:fresh` (development only) |
| Permission issues | Check storage/ and bootstrap/cache/ permissions |
| Stripe webhook fails | Verify webhook secret and signature |

---

## 12. Deployment & CI/CD

### 12.1 Deployment Strategy

**Recommended Production Environment:**
- Web Server: Nginx
- PHP: PHP-FPM 8.2+
- Database: MySQL 8.0+ with connection pooling
- Cache: Redis 6.0+
- Queue: Redis (optimized processing)
- Session: Redis (distributed sessions)
- File Storage: S3 or equivalent

### 12.2 Deployment Steps

```bash
# 1. Install production dependencies
composer install --no-dev --optimize-autoloader

# 2. Build frontend assets
npm ci
npm run build

# 3. Run migrations
php artisan migrate --force

# 4. Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Restart queue workers
php artisan queue:restart

# 6. Clear old cache
php artisan cache:clear
```

### 12.3 Environment Separation

| Environment | Database | Cache | Queue | Session | Debug |
|-------------|----------|-------|-------|---------|-------|
| Local | MySQL local | Redis | Redis | Redis | true |
| Testing | MySQL test DB | Array | Sync | Array | true |
| Staging | MySQL staging | Redis | Redis | Redis | false |
| Production | MySQL production | Redis | Redis | Redis | false |

### 12.4 CI/CD Pipeline Overview (Assumed Design)

```yaml
# Suggested GitHub Actions workflow
name: CI/CD

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      - name: Install Dependencies
        run: composer install
      - name: Run Tests
        run: php artisan test

  deploy:
    needs: test
    if: github.ref == 'refs/heads/main'
    runs-on: ubuntu-latest
    steps:
      - name: Deploy to Production
        # Deployment steps
```

---

## 13. Coding Standards & Conventions

### 13.1 Code Style Guidelines

- **Standard:** PSR-12 coding standard
- **Tool:** Laravel Pint for code formatting
- **Type Hints:** Required for all method parameters and return types
- **Strict Types:** Enabled in all PHP files

```bash
# Format code with Pint
./vendor/bin/pint
```

### 13.2 Architecture Conventions

| Principle | Implementation |
|-----------|----------------|
| **Dependency Injection** | All services injected via constructor |
| **Interface Segregation** | Modules expose contracts for cross-module communication |
| **Single Responsibility** | One class, one purpose |
| **Repository Pattern** | Data access abstracted through repositories |
| **Service Layer** | Business logic in service classes |

### 13.3 Git Workflow (Assumed Design)

**Branching Strategy:**
- `main` - Production-ready code
- `develop` - Integration branch
- `feature/*` - Feature development
- `bugfix/*` - Bug fixes
- `hotfix/*` - Production hotfixes

### 13.4 Commit Message Conventions

```
<type>(<scope>): <subject>

<body>

<footer>
```

**Types:**
- `feat` - New feature
- `fix` - Bug fix
- `docs` - Documentation
- `refactor` - Code refactoring
- `test` - Adding tests
- `chore` - Maintenance

**Example:**
```
feat(order): add order cancellation endpoint

- Add cancel order API endpoint
- Implement stock restoration on cancellation
- Add cancellation reason tracking

Closes #123
```

---

## 14. Scalability & Future Improvements

### 14.1 Known Limitations

| Limitation | Impact | Workaround |
|------------|--------|------------|
| Single database | Vertical scaling only | Consider read replicas |
| Synchronous queue | Processing delays | Use Redis queue driver |
| File storage local | Not horizontally scalable | Use S3 or equivalent |
| Single currency | Limited market reach | Planned enhancement |
| No shipping integration | Manual fulfillment | Third-party integration |

### 14.2 Planned Improvements

| Enhancement | Priority | Status |
|-------------|----------|--------|
| Multi-vendor support | Medium | Planned |
| Additional payment gateways | Medium | Planned |
| Advanced inventory management | Medium | Planned |
| Shipping carrier integration | High | Planned |
| Multi-currency support | Medium | Planned |
| Advanced analytics | Low | Planned |
| Customer loyalty programs | Low | Planned |
| Product recommendations | Low | Planned |

### 14.3 Scaling Strategies

**Horizontal Scaling:**
- Load balancer distribution
- Read replica databases
- Redis cluster for cache/sessions
- S3 for file storage
- Stateless application design

**Vertical Scaling:**
- Database server upgrades
- Cache memory increases
- PHP-FPM worker tuning

**Performance Optimization:**
- Query optimization and indexing
- Eager loading patterns
- Response caching
- CDN for static assets
- Queue processing for heavy operations

---

## Appendices

### Appendix A: Module Status

| Module | Status | Description |
|--------|--------|-------------|
| Admin | Active | Admin user management |
| Analytics | Active | Dashboard & reporting |
| Auth | Active | Authentication |
| Catalog | Active | Products & categories |
| Coupon | Active | Discount coupons |
| Favorite | Active | Wishlist functionality |
| Offer | Active | Promotional offers |
| Order | Active | Order management |
| Payment | Active | Payment processing |
| Review | Active | Product reviews |
| User | Active | Customer management |


**Document Maintained By:** Dv.Abdulaziz Alameri
**Review Cycle:** Quarterly  
**Next Review:** March 2025
