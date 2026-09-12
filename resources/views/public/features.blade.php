<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Features of DigitalMarketingSaaS - AI-powered marketing tools for agencies">
    <title>Features - DigitalMarketingSaaS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style nonce="{{ \ ?? ' }}" nonce="{{ \ ?? ' }}">
        :root { --primary: #6366f1; --primary-dark: #4f46e5; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; }
        .features-header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
        }
        .feature-section { padding: 4rem 0; }
        .feature-section:nth-child(even) { background: #f1f5f9; }
        .feature-screenshot {
            background: #e2e8f0;
            border-radius: 12px;
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-size: 1.1rem;
            border: 2px dashed #cbd5e1;
        }
        .feature-screenshot i { font-size: 3rem; margin-bottom: 0.5rem; display: block; }
        .feature-list-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        .feature-list-item i {
            color: #10b981;
            margin-right: 0.75rem;
            margin-top: 0.25rem;
        }
        .navbar-public {
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .footer-public { background: #0f172a; color: #94a3b8; padding: 2rem 0; }
        .footer-public a { color: #94a3b8; text-decoration: none; }
        .footer-public a:hover { color: white; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-public sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('public.landing') }}">
                <i class="fas fa-rocket me-2" style="color: var(--primary);"></i>DigitalMarketingSaaS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="publicNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link active" href="{{ route('public.features') }}">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('public.pricing') }}">Pricing</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('public.docs') }}">Docs</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('public.blog') }}">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('public.contact') }}">Contact</a></li>
                </ul>
                <div class="d-flex">
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary me-2">Log In</a>
                    <a href="{{ route('register') }}" class="btn btn-primary" style="background: var(--primary); border: none;">Get Started</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <section class="features-header">
        <h1 class="fw-bold">Powerful Features for Modern Marketers</h1>
        <p class="lead mb-0">Everything you need to manage, automate, and grow your marketing efforts.</p>
    </section>

    <!-- AI Content Generation -->
    <section class="feature-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="fw-bold mb-3">AI Content Generation</h2>
                    <p class="text-muted mb-4">Create high-quality content in seconds with AI that understands your brand voice and audience.</p>
                    <div class="feature-list-item">
                        <i class="fas fa-check-circle"></i>
                        <div><strong>Blog Post Writer</strong> — Generate SEO-optimized articles from a simple prompt.</div>
                    </div>
                    <div class="feature-list-item">
                        <i class="fas fa-check-circle"></i>
                        <div><strong>Social Media Captions</strong> — Platform-specific captions that drive engagement.</div>
                    </div>
                    <div class="feature-list-item">
                        <i class="fas fa-check-circle"></i>
                        <div><strong>Email Copy</strong> — Subject lines and body copy that boost open rates.</div>
                    </div>
                    <div class="feature-list-item">
                        <i class="fas fa-check-circle"></i>
                        <div><strong>Ad Copy</strong> — High-converting ad text for Google, Facebook, and LinkedIn.</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="feature-screenshot">
                        <div class="text-center">
                            <i class="fas fa-robot"></i>
                            <p>AI Content Generation Screenshot</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Social Media Management -->
    <section class="feature-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 order-md-2">
                    <h2 class="fw-bold mb-3">Social Media Management</h2>
                    <p class="text-muted mb-4">Manage all your social accounts from one place. Schedule, publish, and analyze with ease.</p>
                    <div class="feature-list-item">
                        <i class="fas fa-check-circle"></i>
                        <div><strong>Multi-Platform Publishing</strong> — Post to Facebook, Instagram, Twitter, LinkedIn, and TikTok.</div>
                    </div>
                    <div class="feature-list-item">
                        <i class="fas fa-check-circle"></i>
                        <div><strong>Visual Calendar</strong> — Drag-and-drop scheduling with a beautiful content calendar.</div>
                    </div>
                    <div class="feature-list-item">
                        <i class="fas fa-check-circle"></i>
                        <div><strong>Engagement Inbox</strong> — Reply to comments and messages from all platforms in one place.</div>
                    </div>
                    <div class="feature-list-item">
                        <i class="fas fa-check-circle"></i>
                        <div><strong>Competitor Analysis</strong> — Track competitor performance and discover opportunities.</div>
                    </div>
                </div>
                <div class="col-md-6 order-md-1">
                    <div class="feature-screenshot">
                        <div class="text-center">
                            <i class="fas fa-share-alt"></i>
                            <p>Social Media Dashboard Screenshot</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Email Campaigns -->
    <section class="feature-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="fw-bold mb-3">Email Campaigns</h2>
                    <p class="text-muted mb-4">Build, send, and track email campaigns that convert. No design skills required.</p>
                    <div class="feature-list-item">
                        <i class="fas fa-check-circle"></i>
                        <div><strong>Drag & Drop Builder</strong> — Create beautiful emails with our intuitive builder.</div>
                    </div>
                    <div class="feature-list-item">
                        <i class="fas fa-check-circle"></i>
                        <div><strong>Automation Sequences</strong> — Set up drip campaigns and behavioral triggers.</div>
                    </div>
                    <div class="feature-list-item">
                        <i class="fas fa-check-circle"></i>
                        <div><strong>A/B Testing</strong> — Test subject lines, content, and send times for optimal results.</div>
                    </div>
                    <div class="feature-list-item">
                        <i class="fas fa-check-circle"></i>
                        <div><strong>Deliverability Tools</strong> — Built-in spam testing and sender reputation monitoring.</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="feature-screenshot">
                        <div class="text-center">
                            <i class="fas fa-envelope-open-text"></i>
                            <p>Email Campaign Builder Screenshot</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Analytics & Reporting -->
    <section class="feature-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 order-md-2">
                    <h2 class="fw-bold mb-3">Analytics & Reporting</h2>
                    <p class="text-muted mb-4">Get real-time insights and create client-ready reports with AI-powered analytics.</p>
                    <div class="feature-list-item">
                        <i class="fas fa-check-circle"></i>
                        <div><strong>Custom Dashboards</strong> — Build dashboards with the metrics that matter to you.</div>
                    </div>
                    <div class="feature-list-item">
                        <i class="fas fa-check-circle"></i>
                        <div><strong>ROI Tracking</strong> — Connect revenue data to see true campaign ROI.</div>
                    </div>
                    <div class="feature-list-item">
                        <i class="fas fa-check-circle"></i>
                        <div><strong>Automated Reports</strong> — Schedule and send branded reports to clients automatically.</div>
                    </div>
                    <div class="feature-list-item">
                        <i class="fas fa-check-circle"></i>
                        <div><strong>AI Insights</strong> — Get actionable recommendations powered by machine learning.</div>
                    </div>
                </div>
                <div class="col-md-6 order-md-1">
                    <div class="feature-screenshot">
                        <div class="text-center">
                            <i class="fas fa-chart-line"></i>
                            <p>Analytics Dashboard Screenshot</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Workflow Automation -->
    <section class="feature-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="fw-bold mb-3">Workflow Automation</h2>
                    <p class="text-muted mb-4">Automate repetitive tasks and build custom workflows that save hours every week.</p>
                    <div class="feature-list-item">
                        <i class="fas fa-check-circle"></i>
                        <div><strong>Visual Builder</strong> — Create automations with a drag-and-drop interface.</div>
                    </div>
                    <div class="feature-list-item">
                        <i class="fas fa-check-circle"></i>
                        <div><strong>Pre-built Templates</strong> — Start with proven automation templates for common use cases.</div>
                    </div>
                    <div class="feature-list-item">
                        <i class="fas fa-check-circle"></i>
                        <div><strong>Conditional Logic</strong> — Build complex if/then workflows with branching logic.</div>
                    </div>
                    <div class="feature-list-item">
                        <i class="fas fa-check-circle"></i>
                        <div><strong>Webhook Triggers</strong> — Connect with 1000+ apps via webhooks and integrations.</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="feature-screenshot">
                        <div class="text-center">
                            <i class="fas fa-magic"></i>
                            <p>Workflow Builder Screenshot</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team & Client Management -->
    <section class="feature-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 order-md-2">
                    <h2 class="fw-bold mb-3">Team & Client Management</h2>
                    <p class="text-muted mb-4">Collaborate with your team and manage clients with role-based access control.</p>
                    <div class="feature-list-item">
                        <i class="fas fa-check-circle"></i>
                        <div><strong>Role-Based Access</strong> — Control who sees and does what with granular permissions.</div>
                    </div>
                    <div class="feature-list-item">
                        <i class="fas fa-check-circle"></i>
                        <div><strong>Client Portals</strong> — Give clients their own login to view reports and approve content.</div>
                    </div>
                    <div class="feature-list-item">
                        <i class="fas fa-check-circle"></i>
                        <div><strong>Approval Workflows</strong> — Set up multi-step approval processes for content and campaigns.</div>
                    </div>
                    <div class="feature-list-item">
                        <i class="fas fa-check-circle"></i>
                        <div><strong>Activity Logs</strong> — Track every action for accountability and compliance.</div>
                    </div>
                </div>
                <div class="col-md-6 order-md-1">
                    <div class="feature-screenshot">
                        <div class="text-center">
                            <i class="fas fa-users"></i>
                            <p>Team Management Screenshot</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-5" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); color: white;">
        <div class="container text-center">
            <h2 class="fw-bold mb-3">Ready to Get Started?</h2>
            <p class="mb-4" style="opacity: 0.85;">Start your 14-day free trial today. No credit card required.</p>
            <a href="{{ route('register') }}" class="btn btn-light btn-lg px-4 py-3 fw-semibold">
                <i class="fas fa-rocket me-2"></i>Start Free Trial
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-public">
        <div class="container text-center">
            <p class="mb-0">&copy; {{ date('Y') }} DigitalMarketingSaaS. All rights reserved.</p>
        </div>
    </footer>

    <script nonce="{{ \ ?? ' }}" nonce="{{ \ ?? ' }}" src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
