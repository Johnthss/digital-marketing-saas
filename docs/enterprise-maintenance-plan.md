# Enterprise Maintenance Plan — Digital Marketing SaaS

## Current State (2026-09-10)
- **560 tests, 0 failures** ✅
- **Pint: 0 violations** ✅
- **50 controllers, 45 models, 85 test files**
- **All using SQLite in-memory for tests**

## 10-Year Maintenance Roadmap

### Phase 1: Stabilization (Q1 2026)
- [x] Fix 8 test failures (agency_id hydration)
- [ ] Audit all controllers for `$user->agency` pattern
- [ ] Implement global scope for multi-tenancy
- [ ] Add missing test files for 8 controllers
- [ ] Implement repository pattern for complex queries

### Phase 2: Hardening (Q2 2026)
- [ ] Add comprehensive input validation audit
- [ ] Implement API rate limiting
- [ ] Add feature flag service
- [ ] Implement webhook delivery service
- [ ] Add queue monitoring

### Phase 3: Scale Preparation (Q3-Q4 2026)
- [ ] Implement repository pattern with caching
- [ ] Add DTOs for API responses
- [ ] Implement event store pattern
- [ ] Add comprehensive health checks
- [ ] Docker + CI/CD pipeline

### Phase 4: Enterprise Features (2027)
- [ ] Multi-region deployment
- [ ] Advanced analytics engine
- [ ] AI content performance predictor
- [ ] Smart scheduling service
- [ ] White-labeling module

### Phase 5: Platform Maturity (2028+)
- [ ] GraphQL API layer
- [ ] Real-time WebSocket features
- [ ] Mobile app API
- [ ] Partner/integrations marketplace
- [ ] SOC 2 compliance

## Key Architectural Decisions

### Multi-Tenancy: Agency-Scope Every Model
Every model MUST have `agency_id`. Every query MUST filter by `agency_id`. The `EnsureAgencyAccess` middleware guarantees this.

### Direct Attribute Access
ALWAYS use `$user->agency_id`, NEVER `$user->agency` (lazy relationship fails in tests).

### Authorization Pattern
```php
// In controllers:
$agencyId = $request->user()->agency_id;
$model = Model::findOrFail($id);
if ((int) $model->agency_id !== (int) $agencyId) {
    abort(403);
}
```

### Testing Pattern
```php
// In tests:
$agency = Agency::factory()->create();
$user = User::factory()->create(['agency_id' => $agency->id]);
$this->actingAs($user);
```

## Critical Rules
1. One class per file
2. Always validate user input
3. Use named routes (never `back()`)
4. Use `forceDelete()` when tests assert `assertDatabaseMissing`
5. Use `DB::table()` for authorization checks when model binding may fail
6. Clear view caches after view changes
7. Run full test suite before any commit
8. Never use `sleep()` in production code
9. Never use `dd()` or `dump()` in production
10. Always use `$user->agency_id` not `$user->agency`
