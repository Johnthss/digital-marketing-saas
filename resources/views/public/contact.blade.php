<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Contact DigitalMarketingSaaS">
    <title>Contact - DigitalMarketingSaaS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --primary: #6366f1; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; }
        .contact-header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            padding: 60px 0;
            text-align: center;
        }
        .contact-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            border: 1px solid #e2e8f0;
        }
        .contact-info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }
        .contact-info-item i {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: #eef2ff;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }
        .navbar-public { background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .footer-public { background: #0f172a; color: #94a3b8; padding: 2rem 0; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-public sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('public.landing') }}">
                <i class="fas fa-rocket me-2" style="color: var(--primary);"></i>DigitalMarketingSaaS
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('public.features') }}">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('public.pricing') }}">Pricing</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('public.docs') }}">Docs</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('public.blog') }}">Blog</a></li>
                    <li class="nav-item"><a class="nav-link active" href="{{ route('public.contact') }}">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="contact-header">
        <h1 class="fw-bold">Get in Touch</h1>
        <p class="lead mb-0">Have questions? We'd love to hear from you.</p>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-7">
                    <div class="contact-card">
                        <h4 class="fw-bold mb-4">Send us a Message</h4>
                        <form>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" placeholder="John">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" placeholder="Doe">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" placeholder="john@example.com">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Subject</label>
                                <select class="form-select">
                                    <option>General Inquiry</option>
                                    <option>Sales Question</option>
                                    <option>Technical Support</option>
                                    <option>Partnership</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Message</label>
                                <textarea class="form-control" rows="5" placeholder="How can we help?"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg" style="background: var(--primary); border: none;">
                                <i class="fas fa-paper-plane me-2"></i>Send Message
                            </button>
                        </form>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="contact-card">
                        <h4 class="fw-bold mb-4">Contact Information</h4>
                        <div class="contact-info-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <strong>Email</strong><br>
                                <span class="text-muted">hello@digitalmarketsaas.com</span>
                            </div>
                        </div>
                        <div class="contact-info-item">
                            <i class="fas fa-phone"></i>
                            <div>
                                <strong>Phone</strong><br>
                                <span class="text-muted">+1 (555) 123-4567</span>
                            </div>
                        </div>
                        <div class="contact-info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <strong>Office</strong><br>
                                <span class="text-muted">123 Marketing Street<br>San Francisco, CA 94105</span>
                            </div>
                        </div>
                        <div class="contact-info-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <strong>Hours</strong><br>
                                <span class="text-muted">Mon-Fri: 9am - 6pm PST</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer-public">
        <div class="container text-center">
            <p class="mb-0">&copy; {{ date('Y') }} DigitalMarketingSaaS. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
