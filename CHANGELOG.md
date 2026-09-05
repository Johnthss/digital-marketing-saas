# Changelog

All notable changes to the **Digital Marketing SaaS Platform** will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Added
- Initial platform architecture and setup
- Multi-tenancy with agency/workspace isolation
- Spatie Permission RBAC (Owner/Admin/Manager/Member)
- AdminLTE 3.2 professional UI
- Social media account management (6 platforms)
- Social post creation, scheduling, publishing
- Campaign management (7 campaign types)
- Client CRM
- Invoice generation and tracking
- AI Content Studio (multi-provider: OpenAI, Anthropic, Google)
- Workflow automation engine
- Webhook system with HMAC signing
- Form builder and landing page builder
- Activity logging and audit trail
- Analytics dashboard
- Global search across all modules
- Notification system
- Comprehensive test suite (138 tests, all passing)
- Demo seeder with realistic agency data

---

## [1.0.0] - 2026-09-05

### Added
- **Platform Launch** — Genesis release
- **Dashboard** — Stats cards, quota usage, activity feed, quick actions, upcoming posts
- **Social Media** — Connect accounts, create posts, schedule, publish, retry failed
- **Campaigns** — Create campaigns, associate clients, change status
- **Clients** — Full CRM with search, filter, CRUD
- **Invoices** — Create, mark paid, edit, delete with line items
- **AI Content** — Generate posts, captions, hashtags, headlines, ad copy, emails
- **Workflows** — Automation rules with triggers and actions
- **Webhooks** — Register endpoints, select events, HMAC-signed payloads
- **Forms** — Build forms, public render, submission tracking
- **Landing Pages** — Create pages with custom colors, CTA, public render
- **Activity Log** — Audit trail with user attribution
- **Search** — Global search across all modules
- **API** — RESTful API with Sanctum auth, v1 endpoints
- **Testing** — 138 tests (Unit, Feature, UAT, Security)
- **Demo Data** — Realistic agency dataset with login credentials

### Security
- Multi-tenant data isolation (agency_id scoping)
- CSRF protection on all forms
- RBAC with Spatie Permission
- Webhook HMAC-SHA256 signing
- Input validation via Form Requests

---

## [0.9.0] - 2026-09-04 (Beta)

### Added
- Alpha release with core modules
- Social posting and scheduling
- Client management
- Invoice tracking
- Basic AI content generation
- Test suite foundation
