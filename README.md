# Simple Ecommerce

A small Laravel + Vue (Inertia) demo app with a product catalog, cart, and stock-aware checkout.

## Features
- Product catalog with images, prices, and stock counts
- Authenticated cart flow (add, update quantities, remove)
- Checkout creates orders, validates stock, and decrements inventory in a transaction
- Low stock alert email when inventory crosses a threshold
- Daily sales report email scheduled at 20:00

## Tech Stack
- Laravel 12, PHP 8.2+
- Vue 3 + Inertia, TypeScript, Tailwind CSS, shadcn-vue
- Vite tooling
- Database-backed queues and scheduler

## Quick Start

### Requirements
- PHP 8.2+ and Composer
- Node.js 18+ and npm
- SQLite (default) or another supported database

### Setup
Option 1: one-shot setup script
```bash
composer run setup
```

Option 2: manual
```bash
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run dev
```

### Run locally
```bash
composer run dev
```
This starts the PHP server, the queue worker, and Vite in one terminal.

For the scheduled daily report, run in another terminal:
```bash
php artisan schedule:work
```

Visit `http://localhost:8000`.

## Demo Data
- Seeded user: `test@example.com` / `password`
- Seeded products: flags (see `database/seeders/DatabaseSeeder.php`)

## Emails and Notifications
- Low stock alerts are queued and sent when inventory drops to the threshold.
- Daily sales report is queued from the scheduler at 20:00.
- Admin email defaults to `admin@example.com` in `app/Jobs/SendLowStockNotification.php` and `app/Jobs/SendDailySalesReport.php`.
- Low stock threshold default is `5` in `app/Jobs/SendLowStockNotification.php`.
- Default mailer is `log` in `.env.example`; switch to SMTP to deliver emails.

## Useful Commands
- `composer run dev` - server, queue worker, Vite
- `composer run test` - lint and tests
- `composer run lint` - PHP formatting via Pint
- `npm run lint` - JS/TS lint
- `npm run format` - Prettier formatting

## Project Structure (high level)
- `app/Http/Controllers` - product and cart flows
- `app/Jobs` and `app/Mail` - low stock alerts and daily sales report
- `resources/js/pages` - Vue pages for products and cart
