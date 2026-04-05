# ⚙️ Subscription Lifecycle Engine

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white"/>
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white"/>
  <img src="https://img.shields.io/badge/Sanctum-Auth-38BDF8?style=for-the-badge&logo=laravel&logoColor=white"/>
  <img src="https://img.shields.io/badge/REST-API-00D09C?style=for-the-badge"/>
  <img src="https://img.shields.io/badge/License-MIT-yellow?style=for-the-badge"/>
</p>

<p align="center">
  A production-ready Laravel REST API that manages the full subscription lifecycle — from plan creation to payment processing and cancellation — with role-based access control.
</p>

---

## 📋 Table of Contents

- [Overview](#-overview)
- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Getting Started](#-getting-started)
- [Environment Variables](#-environment-variables)
- [API Reference](#-api-reference)
  - [Auth](#-auth)
  - [Plans](#-plans)
  - [Subscriptions](#-subscriptions)
  - [Payments](#-payments)
  - [Admin Routes](#-admin-only-routes)
- [Role System](#-role-system)
- [Project Structure](#-project-structure)
- [License](#-license)

---

## 🧩 Overview

**Subscription Lifecycle Engine** is a backend API built with Laravel 12 that handles the entire subscription flow for SaaS or service-based platforms. It provides clear separation between **user** and **admin** roles, covering plan discovery, subscription management, and payment state transitions.

---

## ✨ Features

- 🔐 Token-based authentication via **Laravel Sanctum**
- 📦 Full **CRUD for Plans** (admin-only mutations)
- 🔄 Subscription lifecycle: **create → pay → fail → cancel**
- 👥 **Role-based access control** with `admin` middleware
- 📬 Clean RESTful API design following Laravel conventions
- 🛡️ Protected routes with Sanctum middleware

---

## 🛠 Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 12 |
| Language | PHP 8.2+ |
| Authentication | Laravel Sanctum |
| Database | MySQL / PostgreSQL |
| API Format | RESTful JSON |

---

## 🚀 Getting Started

### Prerequisites

- PHP >= 8.2
- Composer
- MySQL or PostgreSQL
- Laravel 12

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/AbdalrhmanAbdoAlhade/subscription-lifecycle-engine.git
cd subscription-lifecycle-engine

# 2. Install dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Configure your database in .env, then run migrations
php artisan migrate

# 6. (Optional) Seed the database
php artisan db:seed

# 7. Start the development server
php artisan serve
```

---

## ⚙️ Environment Variables

```env
APP_NAME="Subscription Lifecycle Engine"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=subscription_engine
DB_USERNAME=root
DB_PASSWORD=
```

---

## 📡 API Reference

> **Base URL:** `http://localhost:8000/api`  
> All protected routes require the header: `Authorization: Bearer {{token}}`

---

### 🔑 Auth

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `POST` | `/register` | ❌ Public | Register a new user |
| `POST` | `/login` | ❌ Public | Login and get access token |
| `GET` | `/me` | ✅ Required | Get authenticated user info |
| `POST` | `/logout` | ✅ Required | Revoke current token |

#### Register
```http
POST /api/register
Content-Type: application/json

{
  "name": "Abdalrhman",
  "email": "abdo@example.com",
  "password": "password",
  "password_confirmation": "password"
}
```

#### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "abdo@example.com",
  "password": "password"
}
```

**Response:**
```json
{
  "token": "1|abc123...",
  "user": { "id": 1, "name": "Abdalrhman", "email": "abdo@example.com" }
}
```

---

### 📦 Plans

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| `GET` | `/plans` | ✅ | Any | List all plans |
| `GET` | `/plans/{id}` | ✅ | Any | Get single plan |
| `POST` | `/plans` | ✅ | Admin | Create a new plan |
| `PUT` | `/plans/{id}` | ✅ | Admin | Update a plan |
| `DELETE` | `/plans/{id}` | ✅ | Admin | Delete a plan |

#### Example Plan Object
```json
{
  "id": 1,
  "name": "Pro",
  "price": 99.00,
  "duration_days": 30,
  "features": ["Unlimited access", "Priority support"],
  "created_at": "2025-01-01T00:00:00Z"
}
```

---

### 🔄 Subscriptions

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| `GET` | `/subscriptions` | ✅ | Any | List user's subscriptions |
| `POST` | `/subscriptions` | ✅ | Any | Subscribe to a plan |
| `GET` | `/subscriptions/{id}` | ✅ | Any | Get subscription details |
| `POST` | `/subscriptions/{id}/cancel` | ✅ | Admin | Cancel a subscription |

#### Create Subscription
```http
POST /api/subscriptions
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "plan_id": 1
}
```

---

### 💳 Payments

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `POST` | `/subscriptions/{id}/pay` | ✅ | Mark subscription as paid |
| `POST` | `/subscriptions/{id}/fail` | ✅ | Mark payment as failed |

#### Subscription Lifecycle Flow

```
[Created] ──► [Pay] ──► [Active]
                └──► [Fail] ──► [Failed]
[Active]  ──► [Cancel] ──► [Cancelled]   (Admin only)
```

---

### 🛡️ Admin-Only Routes

These routes require the `admin` middleware in addition to Sanctum authentication.

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/plans` | Create plan |
| `PUT` | `/plans/{id}` | Update plan |
| `DELETE` | `/plans/{id}` | Delete plan |
| `POST` | `/subscriptions/{id}/cancel` | Cancel subscription |

---

## 👥 Role System

The app uses a two-tier role system enforced via middleware:

| Role | Access |
|------|--------|
| **User** | Register, login, view plans, manage own subscriptions, trigger pay/fail |
| **Admin** | All user access + create/update/delete plans + cancel any subscription |

> The `admin` middleware is stacked on top of `auth:sanctum`, so both layers must pass for admin routes.

---

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── AuthController.php
│   │       ├── PlanController.php
│   │       ├── SubscriptionController.php
│   │       └── PaymentController.php
│   └── Middleware/
│       └── AdminMiddleware.php
├── Models/
│   ├── User.php
│   ├── Plan.php
│   ├── Subscription.php
│   └── Payment.php
routes/
└── api.php
```

---

## 📄 License

This project is open-sourced under the [MIT License](LICENSE).

---

<p align="center">
  Built with ❤️ by <a href="https://github.com/AbdalrhmanAbdoAlhade">Abdalrhman Abdalnabe</a> · <a href="https://abdalrhman-abdo-alhade.vercel.app">Portfolio</a>
</p>