# Digital Marketing SaaS

Enterprise-grade multi-tenant digital marketing platform built with Laravel 13.

## Features

- **Multi-tenancy** with agency/workspace scoping
- **Social Media Management** — posts, scheduling, analytics
- **Email Marketing** — campaigns, templates, tracking
- **AI Content Generation** — multi-provider gateway (OpenAI, Anthropic, Google)
- **Workflow Automation** — visual drag-and-drop builder
- **Analytics & Reports** — custom reports with PDF/CSV/Excel export
- **Client Management** — CRM with tagging and custom fields
- **Invoicing** — Stripe integration, subscription billing
- **Media Library** — upload, organize, GDPR-compliant
- **White-Labeling** — custom branding, domains, CSS
- **Team Collaboration** — comments, activity feeds, notifications
- **GDPR Compliance** — data export, deletion, consent management

## Tech Stack

- **Backend:** PHP 8.4, Laravel 13
- **Frontend:** AdminLTE 3.2, Bootstrap 4, Chart.js
- **Database:** SQLite (dev), MySQL (production)
- **Queue:** Database
- **Cache:** File/Redis

## Installation

```bash
git clone https://github.com/webbixray/digital-marketing-saas.git
cd digital-marketing-saas
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan serve
```

## Demo Credentials

- **Email:** owner@agency.com
- **Password:** password123

## Testing

```bash
php artisan test
# or
php vendor/bin/phpunit
```

## License

MIT
