# Developer Guide

## Architecture Overview

Digital Marketing SaaS follows a **service-oriented architecture** built on Laravel 13. The application is designed around three core principles:

1. **Agency-Scoped Multi-Tenancy** — Every data operation is scoped to an agency via `agency_id`
2. **Event-Driven Processing** — Domain events trigger listeners for notifications, caching, and agent actions
3. **AI Agent Orchestration** — Autonomous agents collaborate through a shared orchestrator with budget tracking and health monitoring

### Request Lifecycle

```
HTTP Request
    ↓
Middleware (SecurityHeaders, HstsMiddleware, EnsureAgencyAccess, EnforceQuota, ApplyWhiteLabel)
    ↓
Controller (validates via FormRequest)
    ↓
Service Layer (business logic)
    ↓
Model (Eloquent with global scopes)
    ↓
Database (MySQL/SQLite)
    ↓
Event Dispatched → Listeners (notifications, cache clearing, agent triggers)
    ↓
Queue Jobs (async processing)
```

## Directory Structure

```
DigitalMarketingSaaS/
├── app/
│   ├── Enums/                    # PHP 8.1+ backed enums
│   │   ├── AiActionType.php
│   │   ├── AiContentType.php
│   │   ├── CampaignStatus.php
│   │   ├── InvoiceStatus.php
│   │   ├── PostStatus.php
│   │   ├── SubscriptionStatus.php
│   │   ├── UserRole.php
│   │   └── WorkflowStatus.php
│   ├── Events/                   # Domain events
│   │   ├── AgentWorkflowCompleted.php
│   │   ├── AiGenerationCompleted.php
│   │   ├── CampaignStatusChanged.php
│   │   ├── ClientCreated.php
│   │   ├── InvoicePaid.php
│   │   ├── PostPublished.php
│   │   ├── PostScheduled.php
│   │   ├── PostFailed.php
│   │   └── SubscriptionUpgraded.php
│   ├── Http/
│   │   ├── Controllers/          # HTTP controllers
│   │   │   ├── AI/
│   │   │   ├── Email/
│   │   │   ├── Social/
│   │   │   └── ...
│   │   ├── Middleware/           # Custom middleware
│   │   │   ├── AgentRateLimit.php
│   │   │   ├── ApplyWhiteLabel.php
│   │   │   ├── EnsureAgencyAccess.php
│   │   │   ├── EnforceQuota.php
│   │   │   ├── HstsMiddleware.php
│   │   │   └── SecurityHeaders.php
│   │   ├── Requests/             # Form request validation
│   │   │   ├── Api/
│   │   │   ├── Social/
│   │   │   └── ...
│   │   └── Resources/            # API resources (transformers)
│   ├── Jobs/                     # Queue jobs
│   │   ├── AI/
│   │   │   └── GenerateContent.php
│   │   ├── DataExportJob.php
│   │   ├── DataDeletionJob.php
│   │   ├── GenerateReportJob.php
│   │   ├── RunAgentWorkflowJob.php
│   │   ├── Scheduler/
│   │   │   └── RunWorkflows.php
│   │   └── Social/
│   │       ├── FetchPlatformMetrics.php
│   │       ├── ProcessScheduledPost.php
│   │       ├── ProcessScheduledPostsJob.php
│   │       ├── PublishPost.php
│   │       └── RetryFailedPostsJob.php
│   ├── Listeners/                # Event listeners
│   │   ├── Agent/
│   │   │   ├── CampaignStatusChangedAgentListener.php
│   │   │   ├── ClientCreatedAgentListener.php
│   │   │   ├── PostPublishedAgentListener.php
│   │   │   └── SubscriptionUpgradedAgentListener.php
│   │   ├── Billing/
│   │   │   ├── LogInvoiceActivity.php
│   │   │   └── LogSubscriptionUpgrade.php
│   │   ├── Social/
│   │   │   ├── ClearPostCache.php
│   │   │   ├── LogPostActivity.php
│   │   │   └── SendPostNotification.php
│   │   └── SendWorkflowNotificationListener.php
│   ├── Models/                   # Eloquent models
│   │   ├── Agency.php
│   │   ├── AgencySetting.php
│   │   ├── AgentCostLog.php
│   │   ├── AgentPerformanceLog.php
│   │   ├── AgentSharedKnowledge.php
│   │   ├── AgentWorkflowExecution.php
│   │   ├── AiContentLog.php
│   │   ├── Campaign.php
│   │   ├── Client.php
│   │   ├── Comment.php
│   │   ├── ConsentRecord.php
│   │   ├── ContentAsset.php
│   │   ├── ContentInsight.php
│   │   ├── ContentTemplate.php
│   │   ├── CustomField.php
│   │   ├── CustomFieldValue.php
│   │   ├── DataDeletionRequest.php
│   │   ├── DataExportRequest.php
│   │   ├── EmailCampaign.php
│   │   ├── EmailCampaignRecipient.php
│   │   ├── EmailTemplate.php
│   │   ├── Feature.php
│   │   ├── FeatureFlag.php
│   │   ├── Form.php
│   │   ├── FormResponse.php
│   │   ├── InboxMessage.php
│   │   ├── InboxTriage.php
│   │   ├── Invoice.php
│   │   ├── InvoiceItem.php
│   │   ├── LandingPage.php
│   │   ├── MediaAsset.php
│   │   ├── Plan.php
│   │   ├── Platform.php
│   │   ├── Report.php
│   │   ├── SocialAccount.php
│   │   ├── SocialPost.php
│   │   ├── User.php
│   │   ├── Webhook.php
│   │   ├── WebhookLog.php
│   │   ├── WhiteLabelSetting.php
│   │   ├── Workflow.php
│   │   ├── WorkflowExecution.php
│   │   ├── WorkflowLog.php
│   │   ├── WorkflowTemplate.php
│   │   ├── WorkflowVersion.php
│   │   └── WorkflowWebhookLog.php
│   ├── Services/                 # Business logic services
│   │   ├── AI/
│   │   │   ├── Agent/            # AI agent system
│   │   │   │   ├── AbstractAgent.php
│   │   │   │   ├── AgentBudgetMiddleware.php
│   │   │   │   ├── AgentContext.php
│   │   │   │   ├── AgentCostTracker.php
│   │   │   │   ├── AgentFeedbackService.php
│   │   │   │   ├── AgentHealthMonitor.php
│   │   │   │   ├── AgentInterface.php
│   │   │   │   ├── AgentMemory.php
│   │   │   │   ├── AgentOrchestrator.php
│   │   │   │   ├── AgentResult.php
│   │   │   │   ├── AgentTask.php
│   │   │   │   └── SecurityAuditAgent.php
│   │   │   ├── Gateway/          # AI provider gateway
│   │   │   │   ├── AiGateway.php
│   │   │   │   ├── AiRequest.php
│   │   │   │   ├── AiResponse.php
│   │   │   │   ├── Exceptions/
│   │   │   │   └── Providers/
│   │   │   │       ├── AnthropicProvider.php
│   │   │   │       ├── GoogleProvider.php
│   │   │   │       └── OpenAiProvider.php
│   │   │   ├── AgencyAIAssistantService.php
│   │   │   ├── AiContentService.php
│   │   │   └── AiRecommendationService.php
│   │   ├── Analytics/
│   │   │   └── AnalyticsService.php
│   │   ├── Billing/
│   │   │   └── StripeGateway.php
│   │   ├── Calendar/
│   │   │   └── ContentCalendarService.php
│   │   ├── Email/
│   │   │   ├── EmailCampaignService.php
│   │   │   └── SmtpEmailService.php
│   │   ├── GDPR/
│   │   │   └── GDPRComplianceService.php
│   │   ├── Reporting/
│   │   │   └── EnterpriseReportingService.php
│   │   ├── Social/
│   │   │   ├── PlatformRateLimitService.php
│   │   │   ├── SocialApiService.php
│   │   │   ├── SocialListeningService.php
│   │   │   ├── SocialPostService.php
│   │   │   └── TwitterApiService.php
│   │   ├── Telegram/
│   │   │   └── TelegramBotService.php
│   │   ├── WhiteLabel/
│   │   │   └── WhiteLabelService.php
│   │   ├── Workflow/
│   │   │   └── WorkflowEngine.php
│   │   ├── BulkOperationService.php
│   │   ├── ContentPerformancePredictor.php
│   │   ├── ContentQualityScorer.php
│   │   ├── ExportService.php
│   │   ├── FeatureFlagService.php
│   │   ├── MediaUploadService.php
│   │   ├── QuotaService.php
│   │   ├── SmartSchedulingService.php
│   │   └── VersionService.php
│   └── Providers/
│       └── EventServiceProvider.php
├── config/                       # Configuration files
├── database/
│   ├── factories/                # Model factories
│   ├── migrations/               # Database migrations
│   └── seeders/                  # Database seeders
├── docs/                         # Documentation
│   ├── DEVELOPER.md
│   ├── DEPLOYMENT.md
│   └── USER_GUIDE.md
├── public/                       # Public assets
├── resources/
│   ├── css/
│   ├── js/
│   └── views/                    # Blade templates
├── routes/
│   ├── api.php
│   ├── console.php
│   ├── docs.php
│   ├── telegram.php
│   ├── version.php
│   └── web.php
├── tests/
│   ├── TestCase.php
│   ├── Unit/
│   │   ├── Cache/
│   │   ├── Console/
│   │   ├── Models/
│   │   ├── Notifications/
│   │   ├── Observers/
│   │   ├── Queue/
│   │   ├── Requests/
│   │   └── Services/
│   └── Feature/
└── vendor/                       # Composer dependencies
```

## Key Patterns

### Agency-Scoped Multi-Tenancy

Every model that belongs to an agency uses the `agency_id` column. The `EnsureAgencyAccess` middleware automatically injects the current user's agency context.

**Global Scope Example:**
```php
class SocialPost extends Model
{
    protected static function booted(): void
    {
        static::addGlobalScope('agency', function (Builder $builder) {
            $builder->where('agency_id', auth()->user()->agency_id);
        });
    }
}
```

**Middleware Enforcement:**
```php
// routes/api.php
Route::middleware(['auth:sanctum', 'agency'])->group(function () {
    Route::apiResource('campaigns', CampaignController::class);
    Route::apiResource('clients', ClientController::class);
    // ...
});
```

**Best Practices:**
- Always use `auth()->user()->agency_id` for queries, never trust client-provided agency IDs
- Apply the `agency` middleware to all tenant-scoped routes
- Use `FormRequest` classes to validate all input
- Encrypt sensitive tokens (social media access tokens) before storage

### Event-Driven Architecture

Domain events are dispatched when significant state changes occur. Listeners handle side effects asynchronously.

**Available Events:**
| Event | When Dispatched |
|-------|-----------------|
| `PostPublished` | Social post successfully published |
| `PostScheduled` | Post scheduled for future publishing |
| `PostFailed` | Post publication failed |
| `CampaignStatusChanged` | Campaign status updated |
| `ClientCreated` | New client added |
| `InvoicePaid` | Payment received via Stripe |
| `SubscriptionUpgraded` | Plan changed |
| `AiGenerationCompleted` | AI content generation finished |
| `AgentWorkflowCompleted` | Agent workflow execution done |

**Example Event Dispatch:**
```php
// In a service
event(new PostPublished($post, $user));

// Listener handles notifications, cache clearing, activity logging
class SendPostNotification
{
    public function handle(PostPublished $event): void
    {
        $event->user->notify(new PostPublishedNotification($event->post));
    }
}
```

**Agent Listeners:**
Special listeners trigger AI agent workflows in response to events:
- `ClientCreatedAgentListener` — Triggers lead generation workflow
- `PostPublishedAgentListener` — Updates social strategy agent
- `CampaignStatusChangedAgentListener` — Adjusts campaign optimization
- `SubscriptionUpgradedAgentListener` — Updates agent budget allocations

### AI Agent Orchestration

The agent system uses a coordinator pattern where an `AgentOrchestrator` manages multiple specialized agents.

**Agent Architecture:**
```
AgentOrchestrator
    ├── AgentInterface (contract)
    ├── AbstractAgent (base class)
    │   ├── AgentContext (state)
    │   ├── AgentMemory (persistence)
    │   ├── AgentCostTracker (budget)
    │   └── AgentBudgetMiddleware (rate limiting)
    ├── SecurityAuditAgent
    ├── LeadGenerationAgent
    ├── SocialMediaStrategyAgent
    └── WeeklyReportAgent
```

**Creating a New Agent:**
```php
class MyCustomAgent extends AbstractAgent
{
    public function getName(): string
    {
        return 'my-custom-agent';
    }

    public function execute(AgentTask $task): AgentResult
    {
        // 1. Validate budget
        $this->budgetMiddleware->check($task);

        // 2. Build context
        $context = $this->context->forAgency($task->agencyId);

        // 3. Execute logic
        $result = $this->doWork($context);

        // 4. Track cost
        $this->costTracker->log($task, $result);

        return $result;
    }
}
```

**Agent Workflows:**
Workflows are multi-step agent orchestrations:
- `LeadGenerationWorkflow` — Identifies and qualifies leads
- `SocialMediaStrategyWorkflow` — Analyzes and recommends strategy
- `WeeklyReportWorkflow` — Generates comprehensive reports

**AI Gateway Pattern:**
The `AiGateway` provides a unified interface to multiple AI providers:
```php
$gateway = app(AiGateway::class);
$response = $gateway->generate(
    AiRequest::create('Write a tweet about AI marketing')
        ->withProvider('openai')
        ->withModel('gpt-4o')
        ->withMaxTokens(280)
);
```

## Development Setup

### Local Development

1. **Clone and install:**
   ```bash
   git clone https://github.com/webbixray/digital-marketing-saas.git
   cd digital-marketing-saas
   composer install
   npm install
   ```

2. **Configure environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Set up database:**
   ```bash
   touch database/database.sqlite
   php artisan migrate --force
   php artisan db:seed --force
   ```

4. **Build assets:**
   ```bash
   npm run dev    # Development with hot reload
   npm run build  # Production build
   ```

5. **Start services:**
   ```bash
   php artisan serve          # Application server
   php artisan queue:work     # Queue worker
   php artisan schedule:run   # Task scheduler (or use cron)
   ```

### Using Laravel Herd (macOS)

```bash
herd link digital-marketing-saas
herd secure digital-marketing-saas
# Access at https://digital-marketing-saas.test
```

### Using Laravel Sail (Docker)

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --force
./vendor/bin/sail npm run dev
```

## Coding Standards

### General Rules

1. **Follow PSR-12** — Enforced by Laravel Pint
2. **Use strict typing** — `declare(strict_types=1);` in all PHP files
3. **Type everything** — Parameters, return types, properties
4. **No raw SQL** — Use Eloquent or Query Builder with parameter binding
5. **No logic in controllers** — Controllers only delegate to services
6. **Use Form Requests** — All input validation in dedicated request classes
7. **Use API Resources** — Transform models for API responses

### Naming Conventions

| Element | Convention | Example |
|---------|------------|---------|
| Classes | PascalCase | `SocialPostService` |
| Methods | camelCase | `publishPost()` |
| Variables | camelCase | `$socialPost` |
| Constants | UPPER_SNAKE_CASE | `MAX_RETRY_ATTEMPTS` |
| Routes | kebab-case | `social-posts` |
| Views | kebab-case | `social-posts.index` |
| Config keys | snake_case | `social_posts.enabled` |
| Database columns | snakeCase | `agency_id` |

### Service Pattern

```php
class SocialPostService
{
    public function __construct(
        private readonly PlatformRateLimitService $rateLimiter,
        private readonly SocialApiService $api,
        private readonly QuotaService $quota,
    ) {}

    public function publish(SocialPost $post): void
    {
        $this->quota->ensureCanPublish($post->agency);
        $this->rateLimiter->throttle($post->platform);

        $result = $this->api->publish($post);

        $post->update(['status' => PostStatus::Published]);

        event(new PostPublished($post, auth()->user()));
    }
}
```

### Form Request Pattern

```php
class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create_posts');
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:5000'],
            'platform_id' => ['required', 'exists:platforms,id'],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
            'media_ids' => ['nullable', 'array'],
            'media_ids.*' => ['exists:media_assets,id'],
        ];
    }
}
```

## Testing Guide

### Running Tests

```bash
# All tests
php artisan test

# Specific suite
php artisan test --testsuite=Unit

# Specific file
php artisan test tests/Unit/Services/WorkflowEngineTest.php

# With coverage
php artisan test --coverage
```

### Test Configuration

Tests use SQLite in-memory with the `RefreshDatabase` trait:

```php
class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DemoSeeder::class);
    }
}
```

### Writing Unit Tests

```php
class WorkflowEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_workflow_executes_all_nodes(): void
    {
        $workflow = Workflow::factory()->create();
        $engine = app(WorkflowEngine::class);

        $result = $engine->execute($workflow, ['trigger' => 'test']);

        $this->assertTrue($result->isSuccessful());
        $this->assertCount(3, $result->getExecutedNodes());
    }

    public function test_workflow_fails_on_invalid_node(): void
    {
        $this->expectException(InvalidNodeException::class);

        $workflow = Workflow::factory()->create(['nodes' => 'invalid']);
        $engine = app(WorkflowEngine::class);
        $engine->execute($workflow);
    }
}
```

### Writing Feature Tests

```php
class CampaignControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_campaign(): void
    {
        $user = User::factory()->create();
        $agency = Agency::factory()->create();
        $user->agencies()->attach($agency);

        $response = $this->actingAs($user)
            ->postJson('/api/campaigns', [
                'name' => 'Test Campaign',
                'platform_id' => 1,
                'budget' => 1000,
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Test Campaign');

        $this->assertDatabaseHas('campaigns', [
            'name' => 'Test Campaign',
            'agency_id' => $agency->id,
        ]);
    }
}
```

### Testing Events and Listeners

```php
class PostPublishedEventTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_published_dispatches_event(): void
    {
        Event::fake();

        $post = SocialPost::factory()->published()->create();

        Event::assertDispatched(PostPublished::class, function ($event) use ($post) {
            return $event->post->id === $post->id;
        });
    }

    public function test_post_published_triggers_agent(): void
    {
        Event::fake([PostPublished::class]);

        $post = SocialPost::factory()->published()->create();

        Event::assertListening(PostPublished::class, PostPublishedAgentListener::class);
    }
}
```

### Testing Queue Jobs

```php
class PublishPostJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_publish_post_job_sends_to_platform(): void
    {
        Bus::fake();

        $post = SocialPost::factory()->scheduled()->create();

        PublishPost::dispatch($post);

        Bus::assertDispatched(PublishPost::class);
    }
}
```

### Factories

All models have corresponding factories in `database/factories/`:

```php
$agency = Agency::factory()->create();
$user = User::factory()->for($agency)->create();
$post = SocialPost::factory()->for($agency)->for($user)->create();
$campaign = Campaign::factory()->for($agency)->create();
```

---

*For deployment instructions, see [DEPLOYMENT.md](DEPLOYMENT.md).*
*For user-facing documentation, see [USER_GUIDE.md](USER_GUIDE.md).*
