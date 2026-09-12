<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Pricing plans for DigitalMarketingSaaS - Choose the perfect plan for your agency">
    <title>Pricing - DigitalMarketingSaaS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style nonce="{{ $cspNonce ?? '' }}">
        :root { --primary: #6366f1; --primary-dark: #4f46e5; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; }
        .pricing-header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            padding: 80px 0 120px;
            text-align: center;
        }
        .pricing-card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: white;
            padding: 2rem;
            height: 100%;
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
        }
        .pricing-card:hover { transform: translateY(-4px); box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
        .pricing-card.featured {
            border: 2px solid var(--primary);
            transform: scale(1.02);
        }
        .pricing-card.featured:hover { transform: scale(1.03); }
        .badge-popular {
            position: absolute;
            top: -12px;
            right: 1.5rem;
            background: var(--primary);
            color: white;
            padding: 0.25rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .plan-name { font-weight: 700; font-size: 1.25rem; color: #1e293b; }
        .plan-price { font-size: 3rem; font-weight: 800; color: #1e293b; }
        .plan-price span { font-size: 1rem; font-weight: 500; color: #64748b; }
        .plan-desc { color: #64748b; margin-bottom: 1.5rem; }
        .feature-list { list-style: none; padding: 0; margin: 1.5rem 0; }
        .feature-list li {
            padding: 0.5rem 0;
            display: flex;
            align-items: center;
            color: #334155;
        }
        .feature-list li i { margin-right: 0.75rem; }
        .feature-list li .fa-check { color: #10b981; }
        .feature-list li .fa-minus { color: #cbd5e1; }
        .btn-plan {
            width: 100%;
            padding: 0.875rem;
            border-radius: 8px;
            font-weight: 600;
            border: none;
        }
        .btn-plan-primary { background: var(--primary); color: white; }
        .btn-plan-primary:hover { background: var(--primary-dark); color: white; }
        .btn-plan-outline { background: transparent; border: 2px solid var(--primary); color: var(--primary); }
        .btn-plan-outline:hover { background: var(--primary); color: white; }
        .comparison-table th, .comparison-table td { padding: 0.75rem 1rem; vertical-align: middle; }
        .comparison-table thead th { background: #f1f5f9; font-weight: 600; }
        .comparison-table tbody tr:nth-child(odd) { background: #f8fafc; }
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
                    <li class="nav-item"><a class="nav-link" href="{{ route('public.features') }}">Features</a></li>
                    <li class="nav-item"><a class="nav-link active" href="{{ route('public.pricing') }}">Pricing</a></li>
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
    <section class="pricing-header">
        <h1 class="fw-bold">Simple, Transparent Pricing</h1>
        <p class="lead mb-0">Choose the plan that fits your needs. All plans include a 14-day free trial.</p>
    </section>

    <!-- Pricing Cards -->
    <section class="py-5" style="margin-top: -80px;">
        <div class="container">
            <div class="row g-4">
                <!-- Free -->
                <div class="col-md-3">
                    <div class="pricing-card">
                        <div class="plan-name">Free</div>
                        <div class="plan-price">$0<span>/mo</span></div>
                        <p class="plan-desc">Perfect for trying out the platform</p>
                        <hr>
                        <ul class="feature-list">
                            <li><i class="fas fa-check"></i> 1 User</li>
                            <li><i class="fas fa-check"></i> 100 Contacts</li>
                            <li><i class="fas fa-check"></i> 500 Emails/month</li>
                            <li><i class="fas fa-check"></i> Basic Analytics</li>
                            <li><i class="fas fa-check"></i> 1 Social Account</li>
                            <li><i class="fas fa-minus"></i> AI Content Generation</li>
                            <li><i class="fas fa-minus"></i> Workflow Automation</li>
                            <li><i class="fas fa-minus"></i> White-Label</li>
                        </ul>
                        <a href="{{ route('register') }}" class="btn btn-plan btn-plan-outline">Get Started</a>
                    </div>
                </div>

                <!-- Starter -->
                <div class="col-md-3">
                    <div class="pricing-card">
                        <div class="plan-name">Starter</div>
                        <div class="plan-price">$29<span>/mo</span></div>
                        <p class="plan-desc">Great for small businesses</p>
                        <hr>
                        <ul class="feature-list">
                            <li><i class="fas fa-check"></i> 3 Users</li>
                            <li><i class="fas fa-check"></i> 2,500 Contacts</li>
                            <li><i class="fas fa-check"></i> 10,000 Emails/month</li>
                            <li><i class="fas fa-check"></i> Advanced Analytics</li>
                            <li><i class="fas fa-check"></i> 5 Social Accounts</li>
                            <li><i class="fas fa-check"></i> 50 AI Generations/mo</li>
                            <li><i class="fas fa-check"></i> 5 Workflows</li>
                            <li><i class="fas fa-minus"></i> White-Label</li>
                        </ul>
                        <a href="{{ route('register') }}" class="btn btn-plan btn-plan-outline">Get Started</a>
                    </div>
                </div>

                <!-- Pro -->
                <div class="col-md-3">
                    <div class="pricing-card featured">
                        <span class="badge-popular">Most Popular</span>
                        <div class="plan-name">Pro</div>
                        <div class="plan-price">$79<span>/mo</span></div>
                        <p class="plan-desc">For growing agencies</p>
                        <hr>
                        <ul class="feature-list">
                            <li><i class="fas fa-check"></i> 10 Users</li>
                            <li><i class="fas fa-check"></i> 25,000 Contacts</li>
                            <li><i class="fas fa-check"></i> Unlimited Emails</li>
                            <li><i class="fas fa-check"></i> AI-Powered Analytics</li>
                            <li><i class="fas fa-check"></i> 20 Social Accounts</li>
                            <li><i class="fas fa-check"></i> Unlimited AI Generations</li>
                            <li><i class="fas fa-check"></i> Unlimited Workflows</li>
                            <li><i class="fas fa-check"></i> White-Label</li>
                        </ul>
                        <a href="{{ route('register') }}" class="btn btn-plan btn-plan-primary">Get Started</a>
                    </div>
                </div>

                <!-- Enterprise -->
                <div class="col-md-3">
                    <div class="pricing-card">
                        <div class="plan-name">Enterprise</div>
                        <div class="plan-price">$199<span>/mo</span></div>
                        <p class="plan-desc">For large organizations</p>
                        <hr>
                        <ul class="feature-list">
                            <li><i class="fas fa-check"></i> Unlimited Users</li>
                            <li><i class="fas fa-check"></i> Unlimited Contacts</li>
                            <li><i class="fas fa-check"></i> Unlimited Emails</li>
                            <li><i class="fas fa-check"></i> Custom Reports</li>
                            <li><i class="fas fa-check"></i> Unlimited Accounts</li>
                            <li><i class="fas fa-check"></i> Unlimited AI + Priority</li>
                            <li><i class="fas fa-check"></i> Custom Workflows</li>
                            <li><i class="fas fa-check"></i> White-Label + API</li>
                        </ul>
                        <a href="{{ route('public.contact') }}" class="btn btn-plan btn-plan-outline">Contact Sales</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Feature Comparison Table -->
    <section class="py-5">
        <div class="container">
            <h3 class="text-center fw-bold mb-4">Compare Plans</h3>
            <div class="table-responsive">
                <table class="table comparison-table">
                    <thead>
                        <tr>
                            <th>Feature</th>
                            <th class="text-center">Free</th>
                            <th class="text-center">Starter</th>
                            <th class="text-center">Pro</th>
                            <th class="text-center">Enterprise</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Users</td>
                            <td class="text-center">1</td>
                            <td class="text-center">3</td>
                            <td class="text-center">10</td>
                            <td class="text-center">Unlimited</td>
                        </tr>
                        <tr>
                            <td>Contacts</td>
                            <td class="text-center">100</td>
                            <td class="text-center">2,500</td>
                            <td class="text-center">25,000</td>
                            <td class="text-center">Unlimited</td>
                        </tr>
                        <tr>
                            <td>Emails/month</td>
                            <td class="text-center">500</td>
                            <td class="text-center">10,000</td>
                            <td class="text-center">Unlimited</td>
                            <td class="text-center">Unlimited</td>
                        </tr>
                        <tr>
                            <td>Social Accounts</td>
                            <td class="text-center">1</td>
                            <td class="text-center">5</td>
                            <td class="text-center">20</td>
                            <td class="text-center">Unlimited</td>
                        </tr>
                        <tr>
                            <td>AI Content Generation</td>
                            <td class="text-center"><i class="fas fa-minus text-muted"></i></td>
                            <td class="text-center">50/mo</td>
                            <td class="text-center">Unlimited</td>
                            <td class="text-center">Unlimited</td>
                        </tr>
                        <tr>
                            <td>Workflow Automation</td>
                            <td class="text-center"><i class="fas fa-minus text-muted"></i></td>
                            <td class="text-center">5</td>
                            <td class="text-center">Unlimited</td>
                            <td class="text-center">Custom</td>
                        </tr>
                        <tr>
                            <td>White-Label</td>
                            <td class="text-center"><i class="fas fa-minus text-muted"></i></td>
                            <td class="text-center"><i class="fas fa-minus text-muted"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        </tr>
                        <tr>
                            <td>API Access</td>
                            <td class="text-center"><i class="fas fa-minus text-muted"></i></td>
                            <td class="text-center"><i class="fas fa-minus text-muted"></i></td>
                            <td class="text-center">Read-only</td>
                            <td class="text-center">Full</td>
                        </tr>
                        <tr>
                            <td>Priority Support</td>
                            <td class="text-center"><i class="fas fa-minus text-muted"></i></td>
                            <td class="text-center"><i class="fas fa-minus text-muted"></i></td>
                            <td class="text-center">Email</td>
                            <td class="text-center">24/7 Phone</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="py-5" style="background: #f1f5f9;">
        <div class="container">
            <h3 class="text-center fw-bold mb-4">Frequently Asked Questions</h3>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="accordion" id="pricingFaq">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Can I switch plans at any time?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#pricingFaq">
                                <div class="accordion-body">Yes! You can upgrade or downgrade at any time. Changes take effect immediately, and we'll prorate the difference.</div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Is there a free trial?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#pricingFaq">
                                <div class="accordion-body">All paid plans come with a 14-day free trial. No credit card required to start.</div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    What payment methods do you accept?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#pricingFaq">
                                <div class="accordion-body">We accept all major credit cards (Visa, Mastercard, American Express) and PayPal. Enterprise plans can pay via invoice.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-public">
        <div class="container text-center">
            <p class="mb-0">&copy; {{ date('Y') }} DigitalMarketingSaaS. All rights reserved.</p>
        </div>
    </footer>

    <script nonce="{{ $cspNonce ?? '' }}" src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
