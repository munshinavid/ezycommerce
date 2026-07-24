<p align="center">
  <h1 align="center">🛒 EzyCommerce</h1>
  <p align="center">
    A full-stack, multi-role e-commerce platform built from scratch with PHP — no framework.
    <br />
    Custom routing · Role-based access · Docker deployment · CI/CD pipeline
  </p>
</p>

<p align="center">
  <a href="https://ezycommerce.munshinavid.me"><img src="https://img.shields.io/badge/🌐_Live-ezycommerce.munshinavid.me-0ea5e9?style=for-the-badge" alt="Live" /></a>
  <a href="https://www.php.net/"><img src="https://img.shields.io/badge/PHP_8.2-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP" /></a>
  <a href="https://www.mysql.com/"><img src="https://img.shields.io/badge/MySQL_8-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" /></a>
  <a href="https://developer.mozilla.org/en-US/docs/Web/JavaScript"><img src="https://img.shields.io/badge/JavaScript_ES6+-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" alt="JavaScript" /></a>
  <a href="https://www.docker.com/"><img src="https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white" alt="Docker" /></a>
  <a href="https://github.com/features/actions"><img src="https://img.shields.io/badge/CI/CD-GitHub_Actions-2088FF?style=for-the-badge&logo=githubactions&logoColor=white" alt="CI/CD" /></a>
</p>

<p align="center">
  <a href="#-features">Features</a>
  ·
  <a href="#-architecture">Architecture</a>
  ·
  <a href="#-api-reference">API Reference</a>
  ·
  <a href="#-getting-started">Getting Started</a>
</p>

<br />

<!-- SCREENSHOT: Replace with a full-width hero screenshot of the storefront homepage -->
<!-- ![EzyCommerce Homepage](screenshots/homepage.png) -->
> 📸 **Add homepage screenshot here** — `screenshots/homepage.png`

---

## 📌 About

EzyCommerce is a production-grade e-commerce platform supporting **4 distinct user roles** — each with its own isolated portal, dashboard, and API layer. The entire application is built **without any PHP framework**, featuring a hand-crafted Front Controller routing system, custom database abstraction, and RESTful JSON APIs.

The project is **live on a Linux VPS**, containerized with Docker Compose, and deployed via a GitHub Actions CI/CD pipeline.

### Why This Project?

| What | How |
|---|---|
| No framework dependency | Routing, auth, DB layer, error handling — all built from scratch |
| Not a basic CRUD app | Multi-role portals, discount engine, transactional orders, CI/CD |
| Production deployed | Live on VPS with Docker, automated deployments, health checks |

---

## ✨ Features

### 🛍️ Customer Portal
- Product catalog with search, category filtering, and sorting
- Multi-level discount display (product-specific & category-inherited)
- Shopping cart with real-time stock validation
- Wishlist management
- Order placement with transactional processing
- User profile management

<!-- SCREENSHOT: Customer storefront showing product grid with discount badges -->
<!-- ![Customer Storefront](screenshots/customer-storefront.png) -->
> 📸 **Add customer storefront screenshot here** — `screenshots/customer-storefront.png`

### 🔧 Admin Dashboard
- Analytics dashboard (total orders, users, revenue, products)
- Sales data charting with date-range queries
- Full product CRUD with image uploads
- User management across all roles
- Order lifecycle management (Pending → Shipped → Delivered)
- Discount engine — apply to all products, specific products, or categories

<!-- SCREENSHOT: Admin dashboard showing analytics cards and charts -->
<!-- ![Admin Dashboard](screenshots/admin-dashboard.png) -->
> 📸 **Add admin dashboard screenshot here** — `screenshots/admin-dashboard.png`

### 🏪 Vendor Portal
- Vendor-specific product catalog management
- Order fulfillment tracking (vendor_status per order item)
- Vendor-scoped discount creation
- Sales dashboard with vendor-specific metrics

<!-- SCREENSHOT: Vendor dashboard or product management view -->
<!-- ![Vendor Portal](screenshots/vendor-portal.png) -->
> 📸 **Add vendor portal screenshot here** — `screenshots/vendor-portal.png`

### 🚚 Logistics Portal
- Shipping management with status tracking
- Return request processing (Pending → Approved/Rejected)
- Logistics dashboard with shipment metrics

<!-- SCREENSHOT: Logistics shipping management view -->
<!-- ![Logistics Portal](screenshots/logistics-portal.png) -->
> 📸 **Add logistics portal screenshot here** — `screenshots/logistics-portal.png`

### 🔐 Authentication & Security
- Bcrypt password hashing (`password_hash` / `password_verify`)
- Session-based authentication with server-side verification
- Role-based access control (RBAC) enforced on every protected endpoint
- Input validation and prepared statements (SQL injection prevention)

---

## 🏗️ Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                        Client (Browser)                     │
│                   HTML / CSS / Vanilla JS                    │
└──────────────────────────┬──────────────────────────────────┘
                           │  fetch() / JSON
                           ▼
┌─────────────────────────────────────────────────────────────┐
│              public/index.php (Front Controller)            │
│         URL parsing · Route matching · Asset fallback       │
└──────────────────────────┬──────────────────────────────────┘
                           │
            ┌──────────────┼──────────────┐
            ▼              ▼              ▼
     ┌────────────┐ ┌────────────┐ ┌────────────┐
     │  Customer   │ │   Admin    │ │  Vendor /  │
     │ Controllers │ │ Controllers│ │ Logistics  │
     │  & Views    │ │  & Views   │ │Controllers │
     └──────┬─────┘ └──────┬─────┘ └──────┬─────┘
            │              │              │
            └──────────────┼──────────────┘
                           ▼
              ┌──────────────────────┐
              │   Database Class     │
              │ (mysqli + prepared   │
              │  statements)         │
              └──────────┬───────────┘
                         ▼
              ┌──────────────────────┐
              │     MySQL 8.0        │
              │   (14 tables,        │
              │    15+ foreign keys) │
              └──────────────────────┘
```

### Project Structure

```
ezycommerce/
├── public/
│   └── index.php              # Front Controller (single entry point)
├── Customer/
│   ├── controllers/           # AuthController, CartController, HomeController, etc.
│   ├── models/                # Database class, UserModel, CartModel, etc.
│   ├── views/                 # Storefront pages (index, cart, product, profile)
│   └── scripts/               # Client-side JavaScript
├── Admin/
│   ├── controllers/           # DashboardAPI, ProductAPI, OrderController, etc.
│   ├── models/                # Admin Database class
│   └── views/                 # Admin panel pages
├── Vendor/
│   ├── controllers/           # ProductsAPI, OrderAPI, DiscountAPI
│   └── views/                 # Vendor dashboard & management pages
├── Logistics/
│   ├── controllers/           # ShippingAPI, ReturnAPI, DashboardAPI
│   └── views/                 # Logistics management pages
├── config/
│   └── db.php                 # Centralized database configuration
├── utils/
│   ├── ErrorHandler.php       # Global error/exception handler
│   └── UrlHelper.php          # Environment-aware URL normalization
├── db/
│   ├── migrations/            # Phinx migration files
│   └── seeds/                 # Database seeders
├── docker-compose.yml         # Multi-service container orchestration
├── Dockerfile                 # PHP 8.2 Apache image configuration
├── schema.sql                 # Full database schema (auto-seeds on first boot)
├── phinx.php                  # Migration tool configuration
└── .github/workflows/
    └── deploy.yml             # CI/CD pipeline definition
```

---

## 🗄️ Database Schema

14 normalized tables with enforced referential integrity:

```
roles ──┐
        ├──> users ──┬──> orders ──┬──> order_items
        │            │             ├──> shipping
        │            │             ├──> payments
        │            │             └──> returns
        │            ├──> cart ────> cart_items
        │            ├──> wishlist
        │            ├──> customerdetails
        │            └──> vendors
        │
discounts ──┬──> products
            └──> categories ──> products
```

| Table | Purpose |
|---|---|
| `roles` | RBAC role definitions (Customer, Admin, Vendor, Logistics) |
| `users` | All user accounts with hashed passwords and role FK |
| `vendors` | Vendor profiles linked to user accounts |
| `products` | Product catalog with category, vendor, and discount FKs |
| `categories` | Product categories with optional category-level discount |
| `discounts` | Configurable discounts (percentage/fixed, date-ranged, scoped) |
| `orders` | Order headers with status lifecycle tracking |
| `order_items` | Line items with `price_at_purchase` snapshot |
| `shipping` | Shipment tracking with logistics handler assignment |
| `returns` | Return requests with approval workflow |
| `cart` / `cart_items` | Persistent server-side shopping cart |
| `payments` | Payment records (COD, Credit Card, Mobile Banking) |
| `wishlist` | User wishlists with unique constraint per user-product pair |
| `customerdetails` | Billing/shipping addresses and contact info |

---

## 📡 API Reference

All APIs return JSON. Protected endpoints require an active session with the appropriate role.

### Customer APIs

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/api/auth?endpoint=login` | Authenticate user, create session |
| `POST` | `/api/auth?endpoint=register` | Register new customer account |
| `GET` | `/api/auth?endpoint=verify` | Verify active session |
| `GET` | `/api/home/products` | List products (paginated, filterable, sortable) |
| `GET` | `/api/home/products/{id}` | Get single product with discount resolution |
| `GET` | `/api/home/categories` | List all categories |
| `GET` | `/api/cart?action=fetchCart` | Get cart contents with discount calculations |
| `POST` | `/api/cart?action=addToCart` | Add item to cart (validates stock) |
| `POST` | `/api/cart?action=placeOrder` | Place order (transactional) |
| `GET` | `/api/home/users/{id}/wishlist` | Get user wishlist |
| `POST` | `/api/home/users/{id}/wishlist` | Add to wishlist |

### Admin APIs

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/admin/dashboard?action=stats` | Dashboard KPIs (orders, revenue, users) |
| `GET` | `/api/admin/dashboard?action=sales_data` | Sales chart data (configurable range) |
| `GET` | `/api/admin/dashboard?action=top_products` | Best-selling products |
| `GET/POST/PUT/DELETE` | `/api/admin/product` | Full product CRUD |
| `GET/POST/PUT/DELETE` | `/api/admin/discounts` | Discount management with scoping |
| `GET/POST` | `/api/admin/order` | Order management with status updates |
| `GET/POST` | `/api/admin/user` | User management across all roles |

### Vendor APIs

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET/POST/PUT/DELETE` | `/api/vendor/products` | Vendor-scoped product management |
| `GET` | `/api/vendor/dashboard` | Vendor-specific analytics |
| `GET/PUT` | `/api/vendor/orders` | Order fulfillment tracking |
| `GET/POST/PUT/DELETE` | `/api/vendor/discounts` | Vendor discount management |

### Logistics APIs

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET/PUT` | `/api/logistics/shipping` | Shipping status management |
| `GET` | `/api/logistics/dashboard` | Logistics KPIs |
| `GET/PUT` | `/api/logistics/returns` | Return request processing |

---

## 🚀 Getting Started

### Prerequisites

- [Docker](https://docs.docker.com/get-docker/) & [Docker Compose](https://docs.docker.com/compose/install/)
- Git

### Quick Start (Docker)

```bash
# Clone the repository
git clone https://github.com/munshinavid/ezycommerce.git
cd ezycommerce

# Create environment file
cp .env.example .env

# Start all services
docker-compose up -d --build

# The app will be available at:
# App:          http://localhost:8080
# phpMyAdmin:   http://localhost:8081
```

The database schema and default admin account are auto-seeded on first boot.

### Default Admin Credentials

| Field | Value |
|-------|-------|
| Email | `admin@ezycommerce.com` |
| Password | `admin123` |

### Run Migrations & Seeders

```bash
# Run database migrations
docker exec -i ezycommerce_app php vendor/bin/phinx migrate

# Seed with sample data
docker exec -i ezycommerce_app php vendor/bin/phinx seed:run
```

### Local Development (XAMPP)

```bash
# Clone into your XAMPP htdocs directory
cd C:/xampp/htdocs
git clone https://github.com/YOUR_USERNAME/ezycommerce.git

# Install dependencies
cd ezycommerce
php composer.phar install

# Import the schema into MySQL
mysql -u root < schema.sql

# Access at: http://localhost/ezycommerce
```

---

## ⚙️ Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `DB_HOST` | Database host | `localhost` |
| `DB_PORT` | Database port | `3306` |
| `DB_NAME` | Database name | `ecomm` |
| `DB_USER` | Database username | `root` |
| `DB_PASS` | Database password | _(empty)_ |
| `APP_ENV` | Environment (`development` / `production`) | `development` |

---

## 🔄 CI/CD Pipeline

The project uses **GitHub Actions** for continuous deployment:

```
Push to main → Checkout → SSH into VPS → Git pull
→ Docker Compose rebuild → Run Phinx migrations
→ Health check (curl)
```

Secrets required in GitHub repository settings:
- `VPS_HOST` — Server IP address
- `VPS_USER` — SSH username
- `VPS_SSH_KEY` — Private SSH key
- `VPS_PORT` — SSH port (default: 22)

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| **Language** | PHP 8.2 |
| **Frontend** | HTML5, CSS3, Vanilla JavaScript (ES6+) |
| **Database** | MySQL 8.0 |
| **Server** | Apache (mod_rewrite) |
| **Containerization** | Docker & Docker Compose |
| **CI/CD** | GitHub Actions |
| **Migrations** | Phinx |
| **Package Manager** | Composer |
| **Deployment** | Linux VPS |

---

## 📸 Screenshots

<!-- 
Instructions: 
1. Create a "screenshots" folder in the project root
2. Add your screenshots with the filenames below
3. Uncomment the image lines and remove the placeholder text
-->

### Customer Storefront
<!-- ![Homepage](screenshots/homepage.png) -->
> 📸 Add screenshot: `screenshots/homepage.png`

### Product Detail Page
<!-- ![Product Detail](screenshots/product-detail.png) -->
> 📸 Add screenshot: `screenshots/product-detail.png`

### Shopping Cart
<!-- ![Shopping Cart](screenshots/cart.png) -->
> 📸 Add screenshot: `screenshots/cart.png`

### User Login / Register
<!-- ![Auth Pages](screenshots/auth.png) -->
> 📸 Add screenshot: `screenshots/auth.png`

### Admin Dashboard
<!-- ![Admin Dashboard](screenshots/admin-dashboard.png) -->
> 📸 Add screenshot: `screenshots/admin-dashboard.png`

### Admin Product Management
<!-- ![Product Management](screenshots/admin-products.png) -->
> 📸 Add screenshot: `screenshots/admin-products.png`

### Admin Discount Engine
<!-- ![Discount Management](screenshots/admin-discounts.png) -->
> 📸 Add screenshot: `screenshots/admin-discounts.png`

### Vendor Dashboard
<!-- ![Vendor Dashboard](screenshots/vendor-dashboard.png) -->
> 📸 Add screenshot: `screenshots/vendor-dashboard.png`

### Logistics Shipping Management
<!-- ![Logistics Shipping](screenshots/logistics-shipping.png) -->
> 📸 Add screenshot: `screenshots/logistics-shipping.png`

---

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

---

<p align="center">
  Built from scratch with PHP — no framework, all engineering.
</p>