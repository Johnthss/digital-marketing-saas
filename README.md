# Digital Marketing Agency SaaS Platform

A comprehensive multi-tenant Laravel SaaS platform for digital marketing agencies. Built with Laravel 13, Spatie Permission, and AdminLTE 3.

## 🚀 Features

### Multi-Tenancy
- **Agency/Workspace Model** — Each user belongs to an agency
- **Role-Based Access Control** — Owner, Admin, Manager, Member roles
- **Spatie Permissions** — Fine-grained permissions system
- **Agency-Scoped Middleware** — Automatic data isolation
- **Plan-Based Feature Gating** — Free, Starter, Pro, Enterprise tiers

### Social Media Management
- **6 Platforms** — Facebook, Instagram, Twitter/X, LinkedIn, TikTok, Pinterest
- **OAuth Integration** — Connect and manage multiple accounts per platform
- **Post Creation & Scheduling** — Draft, schedule, publish workflow
- **Content Quality Scoring** — Rule-based scoring with length, hashtag, media analysis
- **Failed Post Recovery** — Retry mechanism with exponential backoff
- **Platform Rate Limiting** — Token-bucket rate limiter per account

### AI Content Engine
- **Multi-Provider Gateway** — OpenAI, Anthropic, Google with automatic failover
- **Task-Based Routing** — Optimized model selection per task type
- **Content Generation** — Posts, captions, headlines, emails, ad copy
- **Rewrite & Translate** — AI-powered content rewriting and translation
- **Hashtag Generation** — Platform-specific hashtag optimization
- **Content Ideas** — AI-generated content ideas with format and CTA suggestions
- **Cost Tracking** — Detailed AI usage and cost logging per agency

### Campaign Management
- **Campaign Types** — General, Product Launch, Seasonal, Awareness, Consideration, Conversion, Retention
- **Client Association** — Link campaigns to specific clients
- **Performance Tracking** — Views, likes, comments, shares, clicks aggregation
- **ROI Estimation** — Campaign ROI tracking and reporting

### Workflow Automation
- **Trigger-Based Workflows** — Post published, comment received, mention, schedule, cron
- **Action Types** — Notifications, auto-reply, AI generation, webhooks, email
- **Conditional Logic** — JSON-based condition rules
- **Execution History** — Full audit trail of workflow runs

### Social Inbox
- **Unified Inbox** — Comments, mentions, DMs across platforms
- **AI Triage** — Sentiment analysis, category classification, urgency scoring
- **Auto-Reply** — AI-generated responses with manual approval
- **Escalation** — Flag important messages for human review

### Business Modules
- **Client Management** — Full CRM with status, industry, notes
- **Content Library** — Reusable text, image, video, document assets
- **Landing Page Builder** — Visual builder with CTA, color customization
- **Form Builder** — Custom forms with public submission endpoints
- **Invoicing** — Line items, tax, multi-currency, PDF generation ready
- **Billing & Plans** — Stripe-ready subscription management

### AdminLTE UI
- **Responsive Design** — Mobile-first, works on all devices
- **30+ Pre-built Views** — Dashboard, CRUD pages, detail views
- **Consistent Navigation** — Tree sidebar with active state indicators
- **Real-Time Notifications** — Toastr flash messages
- **Data Tables** — Searchable, filterable, paginated tables

## 📊 Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                        Frontend (AdminLTE 3)                     │
├─────────────────────────────────────────────────────────────────┤
│                     Controllers (14 total)                        │
├─────────────────────────────────────────────────────────────────┤
│                        Services Layer                             │
│   QuotaService │ AiGateway │ SocialPostService │ QualityScorer   │
├─────────────────────────────────────────────────────────────────┤
│                    Middleware Pipeline                            │
│   EnsureAgencyAccess │ FeatureGate │ EnforceQuota                │
├─────────────────────────────────────────────────────────────────┤
│                     Eloquent Models (27)                          │
│  Agency │ User │ Social* │ Campaign │ Client │ Workflow │ AI   │
├─────────────────────────────────────────────────────────────────┤
│                    Database (40 tables)                           │
│  SQLite (dev) │ MySQL/PostgreSQL (production)                    │
├─────────────────────────────────────────────────────────────────┤
│                       Laravel 13 Core                             │
│  Spatie Permission │ Queue (Database) │ Scheduler │ Sanctum     │
└─────────────────────────────────────────────────────────────────┘
```

## 🛠️ Tech Stack

| Component | Technology |
|-----------|-----------|
| Framework | Laravel 13.30.1 |
| PHP | 8.4.24 |
| Database | SQLite (dev) / MySQL (prod) |
| Auth | Laravel Built-in + Spatie Permission |
| UI | AdminLTE 3.2 + Bootstrap 4 + Font Awesome 6 |
| Queue | Database driver |
| AI | OpenAI / Anthropic / Google (multi-provider) |
| Social | 6 platform OAuth + APIs |

## 📦 Database Tables (40)

### Core Domain
- `agencies` — Agency/workspace records
- `users` — Agency members with role-based access
- `plans` — Subscription plan definitions
- `features` — Feature flags for plan gating
- `agency_settings` — Per-agency key-value configuration

### Social Media
- `platforms` — Platform definitions (facebook, instagram, etc.)
- `social_accounts` — Connected social media accounts
- `social_posts` — Post records with status tracking
- `social_post_scheduled_logs` — Scheduled publish audit trail
- `social_platform_cache` — Platform API response cache

### Content & Marketing
- `campaigns` — Marketing campaign definitions
- `clients` — Client CRM records
- `content_assets` — Content library assets
- `content_templates` — Reusable content templates
- `content_insights` — Daily post analytics
- `landing_pages` — Landing page builder records
- `forms` — Form builder definitions
- `form_responses` — Form submission records
- `email_campaigns` — Email marketing campaigns

### AI & Automation
- `ai_content_logs` — AI generation history with cost tracking
- `workflows` — Automation workflow definitions
- `workflow_executions` — Workflow run history
- `workflow_logs` — Per-execution log entries

### Business
- `invoices` — Invoice records
- `invoice_items` — Invoice line items
- `client_subscriptions` — Client billing subscriptions
- `activity_logs` — Audit trail
- `inbox_messages` — Social inbox messages
- `inbox_triage` — AI triage results

## 🚀 Getting Started

### Requirements
- PHP 8.3+
- Composer
- SQLite (or MySQL/PostgreSQL)

### Installation

```bash
# Clone the repository
git clone https://github.com/your-org/digital-marketing-saas.git
cd digital-marketing-saas

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed plans (optional)
php artisan db:seed --class=PlanSeeder

# Start development server
php artisan serve
```

### Default Login
- Email: `admin@example.com`
- Password: `password`

## 📋 Subscription Plans

| Feature | Free ($0) | Starter ($29) | Pro ($79) | Enterprise ($199) |
|---------|-----------|---------------|-----------|-------------------|
| Users | 1 | 3 | 10 | Unlimited |
| Social Accounts | 1 | 3 | 10 | Unlimited |
| Posts/Month | 30 | 100 | 500 | Unlimited |
| Campaigns | 1 | 3 | 10 | Unlimited |
| Clients | 0 | 5 | 25 | Unlimited |
| AI Generations | 20 | 100 | 500 | Unlimited |
| Landing Pages | 0 | 2 | 10 | Unlimited |
| Workflows | ❌ | ❌ | ❌ | ✅ |
| Social Inbox | ❌ | ❌ | ❌ | ✅ |
| Custom Branding | ❌ | ❌ | ❌ | ✅ |
| API Access | ❌ | ❌ | ❌ | ✅ |

## 🔒 Security

- All data scoped by agency_id middleware
- Feature gates prevent unauthorized access to paid features
- Quota enforcement on all billable operations
- Encrypted social media access tokens
- CSRF protection on all forms
- Rate limiting on API endpoints
- Activity logging for audit trail

## 🤖 AI Integration

### Provider Fallback Chain
```
Request → OpenAI (gpt-4o) → Anthropic (claude-3-5-sonnet) → Google (gemini-1.5-pro)
```

### Task-Based Routing
| Task | Preferred Model | Fallback |
|------|-----------------|----------|
| `reasoning` | gpt-4o | claude → gemini |
| `fast` | gpt-4o-mini | haiku → flash |
| `creative` | gpt-4o | claude → gemini |
| `analysis` | gpt-4o | claude → gemini |
| `embedding` | text-embedding-3-small | text-embedding-004 |

## 📁 File Structure

```
app/
├── Enums/                  # 10 Enum classes
├── Http/
│   ├── Controllers/        # 14 Controllers + Auth/
│   ├── Middleware/         # 3 Custom middleware
│   └── Requests/           # Form Request classes
├── Models/                 # 27 Eloquent models
├── Services/
│   ├── AI/
│   │   ├── Gateway/        # AI provider gateway
│   │   │   ├── AiGateway.php
│   │   │   ├── AiRequest.php
│   │   │   ├── AiResponse.php
│   │   │   ├── Contracts/
│   │   │   ├── Enums/
│   │   │   ├── Exceptions/
│   │   │   └── Providers/  # OpenAI, Anthropic, Google
│   │   └── AiContentService.php
│   ├── Social/
│   │   ├── SocialPostService.php
│   │   └── PlatformRateLimitService.php
│   ├── QuotaService.php
│   └── ContentQualityScorer.php
└── Observers/              # Model observers for quotas, cache

database/
├── factories/              # Model factories
└── migrations/             # 7 migration files (40 tables)

resources/views/
├── layouts/app.blade.php   # AdminLTE master layout
├── auth/                   # Login & Register
├── dashboard/              # Dashboard with stats & quotas
├── social/                 # Posts, Accounts, Inbox
├── campaigns/              # Campaign CRUD
├── clients/                # Client CRM
├── ai/                     # AI content generator
├── workflows/              # Automation builder
├── content/                # Content library
├── landing-pages/          # Landing page builder
├── invoices/               # Invoice management
├── agency/                 # Settings, Team, Billing
└── public/                 # Public-facing pages

routes/web.php              # 93 routes (97 including Closure)
```

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --filter=AgencyTest
php artisan test --filter=SocialPostTest
```

## 📝 License

MIT License. See [LICENSE](LICENSE) for details.

---

Built with ❤️ by the Digital Marketing Agency Dev Team
