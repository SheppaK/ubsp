# Universal Business Systems Platform (UBSP)

A modular enterprise web platform built with **Laravel 12** and **MySQL**. One login gives access to 11 independent business systems — similar to Microsoft 365 or Google Workspace.

## Modules

| Module | Description |
|--------|-------------|
| Electronics Tracker | Asset inventory, warranty, QR codes, maintenance & disposal |
| University Social | Campus social network with posts, events, groups, messaging |
| Balanced Scorecard | KPIs, objectives, targets, traffic-light performance |
| Marketplace | Buy/sell products with categories, wishlist, reviews |
| Boarding House Finder | Property search, maps, bookings, wishlist, messaging, payments, lease PDFs |
| Exchange & Commodity Tracker | Rates, fuel/food prices, alerts, trends |
| Weather Dashboard | City forecasts and location management |
| Clinic Management | Patients, doctors, appointments, records, billing |
| Monitoring & Evaluation | Projects, indicators, budgets, evidence uploads |
| Subscription Sharing | Shared plans, members, renewals, usage logs |
| Sports League | Leagues, teams, fixtures, standings, statistics |

## Features

- **Single sign-on** across all modules
- **Enable/disable modules** per organization (Super Admin / Administrator)
- **Role-based access control** (Spatie Permission): Super Admin, Administrator, Manager, Staff, Student, Customer, Landlord, Doctor, Seller, Buyer, Guest
- **Authentication**: Registration, login, forgot password, email verification, Google OAuth, two-factor authentication (TOTP)
- **Modern UI**: Glassmorphism, gradients, dark/light mode, collapsible sidebar, responsive layout

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0+ (or MariaDB)

## Installation

### 1. Clone and install dependencies

```bash
composer install
npm install
```

### 2. Environment

```bash
cp .env.example .env
php artisan key:generate
```

Configure MySQL in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ubsp
DB_USERNAME=root
DB_PASSWORD=your_password
```

Create the database:

```sql
CREATE DATABASE ubsp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Migrate and seed

```bash
php artisan migrate --seed
```

### 4. Build assets

```bash
npm run build
```

### 5. Run the application

```bash
php artisan serve
```

Visit `http://localhost:8000`

## Default Accounts

| Email | Password | Role |
|-------|----------|------|
| admin@ubsp.local | password | Super Admin |
| demo@ubsp.local | password | Staff (all module access) |

## Google OAuth (optional)

Add credentials to `.env`:

```env
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

## Boarding House Finder

Extended features under `/modules/boarding-house/`:

| Feature | Route | Notes |
|---------|-------|-------|
| Map search (Leaflet + clusters) | `/browse` | Click pin to highlight listing |
| Wishlist | `/wishlist` | Save favorite properties |
| Compare (up to 3) | `/compare` | Session-based side-by-side |
| Roommate matching | `/roommates` | Post & browse profiles |
| In-app messaging | `/messages` | One thread per booking request |
| Availability calendar | `/browse/{property}/rooms/{room}/calendar` | Booked/blocked dates |
| Landlord analytics | `/admin/analytics` | Occupancy, revenue, review charts |

### Boarding House environment variables

```env
BH_CAMPUS_NAME="Main Campus"
BH_CAMPUS_LAT=-17.7833
BH_CAMPUS_LNG=31.0333
GOOGLE_MAPS_API_KEY=
BH_HOLDING_FEE_AMOUNT=50
BH_HOLDING_FEE_CURRENCY=usd
STRIPE_KEY=
STRIPE_SECRET=
TWILIO_SID=
TWILIO_TOKEN=
TWILIO_FROM=
```

Emails (submitted / approved / rejected) use Laravel Mailables with `MAIL_MAILER=log` in dev. SMS uses a custom `twilio_sms` channel (mock/log when Twilio keys are absent).

## Architecture

```
app/
├── Http/Controllers/Modules/   # Module controllers (CRUD + dashboard)
├── Models/Modules/             # Module Eloquent models
├── Services/ModuleManager.php  # Module registry & access control
config/ubsp.php                 # Module definitions & roles
routes/modules.php              # Module routes (prefix: /modules/{slug})
database/migrations/            # Platform + all module tables
resources/views/
├── layouts/platform.blade.php  # Main app shell
├── platform/                   # Platform dashboard & module management
└── modules/{slug}/             # Per-module views
```

Each module uses prefixed database tables (e.g. `et_assets`, `cl_patients`, `sl_leagues`) and isolated routes under `/modules/{slug}/`.

## Module Management

Super Admins and Administrators can enable or disable modules at **Dashboard → Manage Modules**.

## License

MIT
