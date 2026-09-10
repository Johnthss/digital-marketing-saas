<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="DigitalMarketingSaaS - All-in-one marketing platform for agencies and businesses">
    <title>DigitalMarketingSaaS - Grow Your Business</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #10b981;
        }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .hero-section {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%);
            color: white;
            padding: 120px 0;
        }
        .hero-section h1 { font-size: 3.5rem; font-weight: 700; margin-bottom: 1.5rem; }
        .hero-section p.lead { font-size: 1.25rem; opacity: 0.95; }
        .feature-card {
            border: none;
            border-radius: 12px;
            padding: 2rem;
            height: 100%;
            transition: transform 0.2s, box-shadow 0.2s;
            background: #f8fafc;
        }
        .feature-card:hover { transform: translateY(-4px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .feature-icon {
            width: 56px; height: 56px;
            border-radius: 12px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .testimonial-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 2rem;
            height: 100%;
            background: white;
        }
        .testimonial-card .stars { color: #f59e0b; margin-bottom: 0.75rem; }
        .testimonial-card .author { font-weight: 600; color: #1e293b; }
        .testimonial-card .role { font-size: 0.875rem; color: #64748b; }
        .cta-section {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: white;
            padding: 80px 0;
        }
        .btn-cta {
            background: var(--primary);
            color: white;
            padding: 0.875rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            border: none;
        }
        .btn-cta:hover { background: var(--primary-dark); color: white; }
        .btn-cta-outline {
            background: transparent;
            color: white;
            padding: 0.875rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            border: 2px solid rgba(255,255,255,0.3);
        }
        .btn-cta-outline:hover { border-color: white; color: white; }
        .navbar-public {
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .navbar-public .nav-link { color: #1e293b; font-weight: 500; }
        .navbar-public .nav-link:hover { color: var(--primary); }
        .footer-public {
            background: #0f172a;
            color: #94a3b8;
            padding: 3rem 0;
        }
        .footer-public a { color: #94a3b8; text-decoration: none; }
        .footer-public a:hover { color: white; }
        .badge-stat {
            display: inline-block;
            background: rgba(255,255,255,0.15);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            margin: 0.25rem;
        }
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
                    <li class="nav-item"><a class="nav-link" href="{{ route('public.pricing') }}">Pricing</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('public.docs') }}">Docs</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('public.blog') }}">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('public.contact') }}">Contact</a></li>
                </ul>
                <div class="d-flex">
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary me-2">Log In</a>
                    <a href="{{ route('register') }}" class="btn btn-cta">Get Started</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1>All the Marketing Tools Your Agency Needs</h1>
            <p class="lead mb-4">Automate campaigns, create AI-powered content, manage social media, and track analytics — all from one powerful platform.</p>
            <div class="mb-4">
                <a href="{{ route('register') }}" class="btn btn-light btn-lg me-3 px-4 py-3 fw-semibold">
                    <i class="fas fa-play me-2"></i>Start Free Trial
                </a>
                <a href="{{ route('public.features') }}" class="btn btn-outline-light btn-lg px-4 py-3">
                    <i class="fas fa-eye me-2"></i>See Features
                </a>
            </div>
            <div>
                <span class="badge-stat"><i class="fas fa-check me-1"></i> No credit card required</span>
                <span class="badge-stat"><i class="fas fa-check me-1"></i> 14-day free trial</span>
                <span class="badge-stat"><i class="fas fa-check me-1"></i> Cancel anytime</span>
            </div>
        </div>
    </section>

    <!-- Features Grid -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Everything You Need to Scale</h2>
                <p class="text-muted">Powerful tools that help agencies and businesses grow faster</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-robot"></i></div>
                        <h5 class="fw-semibold">AI Content Generation</h5>
                        <p class="text-muted mb-0">Generate compelling copy, blog posts, and social media captions with AI that understands your brand voice.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-share-alt"></i></div>
                        <h5 class="fw-semibold">Social Media Management</h5>
                        <p class="text-muted mb-0">Schedule, publish, and analyze posts across all major social platforms from a single dashboard.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-envelope-open-text"></i></div>
                        <h5 class="fw-semibold">Email Campaigns</h5>
                        <p class="text-muted mb-0">Design beautiful emails, automate sequences, and track performance with real-time analytics.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                        <h5 class="fw-semibold">Advanced Analytics</h5>
                        <p class="text-muted mb-0">Get actionable insights with custom dashboards, ROI tracking, and AI-powered recommendations.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-users"></i></div>
                        <h5 class="fw-semibold">Team Collaboration</h5>
                        <p class="text-muted mb-0">Assign roles, manage approvals, and collaborate with your team and clients seamlessly.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-magic"></i></div>
                        <h5 class="fw-semibold">Workflow Automation</h5>
                        <p class="text-muted mb-0">Build custom automation workflows that save hours of manual work every week.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-5" style="background: #f1f5f9;">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Loved by Marketers Worldwide</h2>
                <p class="text-muted">Join thousands of agencies and businesses growing with our platform</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="stars">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <p class="mb-3">"This platform transformed how we manage our clients. The AI content tools alone save us 20+ hours per week."</p>
                        <p class="author mb-0">Sarah Johnson</p>
                        <p class="role mb-0">CEO, GrowthHub Agency</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="stars">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <p class="mb-3">"We switched from 5 different tools to this single platform. Our team is more productive and our clients are happier."</p>
                        <p class="author mb-0">Michael Chen</p>
                        <p class="role mb-0">Director, Nexus Digital</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="stars">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <p class="mb-3">"The analytics and reporting features are incredible. We can finally show clients real ROI from their campaigns."</p>
                        <p class="author mb-0">Emily Rodriguez</p>
                        <p class="role mb-0">Founder, Spark Media</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container text-center">
            <h2 class="fw-bold mb-3">Ready to Transform Your Marketing?</h2>
            <p class="mb-4" style="opacity: 0.85;">Join 2,500+ agencies already using DigitalMarketingSaaS to grow their business.</p>
            <div>
                <a href="{{ route('register') }}" class="btn btn-light btn-lg me-3 px-4 py-3 fw-semibold">
                    <i class="fas fa-rocket me-2"></i>Start Your Free Trial
                </a>
                <a href="{{ route('public.contact') }}" class="btn btn-cta-outline btn-lg px-4 py-3">
                    <i class="fas fa-envelope me-2"></i>Contact Sales
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-public">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h6 class="text-white mb-3"><i class="fas fa-rocket me-2"></i>DigitalMarketingSaaS</h6>
                    <p>All-in-one marketing platform built for agencies and businesses that want to scale.</p>
                </div>
                <div class="col-md-2">
                    <h6 class="text-white mb-3">Product</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('public.features') }}">Features</a></li>
                        <li><a href="{{ route('public.pricing') }}">Pricing</a></li>
                        <li><a href="{{ route('public.docs') }}">Documentation</a></li>
                    </ul>
                </div>
                <div class="col-md-2">
                    <h6 class="text-white mb-3">Company</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('public.blog') }}">Blog</a></li>
                        <li><a href="{{ route('public.contact') }}">Contact</a></li>
                        <li><a href="#">Careers</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 class="text-white mb-3">Stay Updated</h6>
                    <p>Get the latest marketing tips and product updates.</p>
                    <div class="d-flex">
                        <input type="email" class="form-control me-2" placeholder="Enter your email">
                        <button class="btn btn-primary">Subscribe</button>
                    </div>
                </div>
            </div>
            <hr style="border-color: #1e293b;">
            <div class="text-center">
                <p class="mb-0">&copy; {{ date('Y') }} DigitalMarketingSaaS. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
