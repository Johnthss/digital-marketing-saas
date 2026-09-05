# Changelog

All notable changes to the **Digital Marketing SaaS Platform** will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Added
- Version and changelog system with `VersionService`
- `version:bump` Artisan command for semver bumps
- In-app changelog viewer at `/changelog`
- API endpoint `/api/version` for version checks
- Conventional commits guide for contributors
- Auto-generated release notes from commit messages

---

## [1.0.0] - 2026-09-05

### Added
- **Platform Launch** — Genesis release
- **Dashboard** — Stats cards, quota usage, activity feed, quick actions
- **Social Media** — Connect accounts, create posts, schedule, publish, retry
- **Campaigns** — Create campaigns, associate clients, change status
- **Clients** — Full CRM with search, filter, CRUD
- **Invoices** — Create, mark paid, edit, delete with line items
- **AI Content** — Generate posts, captions, hashtags, headlines, ad copy
- **Workflows** — Automation rules with triggers and actions
- **Webhooks** — Register endpoints, HMAC-signed payloads
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
