<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>NEKJOUL IMANAGE — Rental Management Made Simple</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: "Segoe UI", system-ui, sans-serif;
            }
            html {
                scroll-behavior: smooth;
            }
            body {
                background: #fff;
                color: #111827;
                line-height: 1.55;
            }

            /* ============ HEADER ============ */
            .nav {
                position: sticky;
                top: 0;
                z-index: 100;
                background: rgba(255, 255, 255, 0.92);
                backdrop-filter: blur(10px);
                border-bottom: 1px solid #e5e7eb;
            }
            .nav-inner {
                max-width: 1200px;
                margin: 0 auto;
                padding: 16px 32px;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            .brand {
                display: flex;
                align-items: center;
                gap: 12px;
                font-size: 18px;
                font-weight: 800;
                color: #111827;
                letter-spacing: 0.5px;
                text-decoration: none;
            }
            .brand-icon {
                width: 38px;
                height: 38px;
                border-radius: 10px;
                background: #22c55e;
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .nav-links {
                display: flex;
                gap: 24px;
                align-items: center;
            }
            .nav-links a {
                font-size: 14px;
                color: #4b5563;
                text-decoration: none;
                font-weight: 500;
                transition: color 0.15s;
            }
            .nav-links a:hover {
                color: #16a34a;
            }
            .nav-actions {
                display: flex;
                gap: 10px;
                align-items: center;
            }

            .btn-nav-login {
                padding: 9px 18px;
                font-size: 14px;
                font-weight: 600;
                color: #374151;
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                text-decoration: none;
                transition:
                    background 0.15s,
                    color 0.15s;
            }
            .btn-nav-login:hover {
                background: #f9fafb;
                color: #16a34a;
                border-color: #bbf7d0;
            }

            .btn-nav-register {
                padding: 9px 18px;
                font-size: 14px;
                font-weight: 600;
                color: #fff;
                background: #22c55e;
                border: none;
                border-radius: 10px;
                text-decoration: none;
                transition:
                    transform 0.15s,
                    opacity 0.15s;
            }
            .btn-nav-register:hover {
                transform: translateY(-1px);
                opacity: 0.94;
            }

            /* ============ HERO ============ */
            .hero {
                background:
                    radial-gradient(
                        circle at 20% 10%,
                        rgba(34, 197, 94, 0.1),
                        transparent 40%
                    ),
                    radial-gradient(
                        circle at 90% 40%,
                        rgba(34, 197, 94, 0.08),
                        transparent 40%
                    ),
                    linear-gradient(180deg, #f9fafb 0%, #ffffff 100%);
                padding: 80px 32px 100px;
                text-align: center;
                position: relative;
                overflow: hidden;
            }
            .hero-inner {
                max-width: 840px;
                margin: 0 auto;
                position: relative;
                z-index: 2;
            }

            .hero-badge {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 6px 14px;
                border-radius: 30px;
                background: #dcfce7;
                color: #16a34a;
                font-size: 13px;
                font-weight: 600;
                margin-bottom: 24px;
            }
            .hero-badge .dot {
                width: 7px;
                height: 7px;
                border-radius: 50%;
                background: #22c55e;
                animation: pulse 2s infinite;
            }
            @keyframes pulse {
                0%,
                100% {
                    opacity: 1;
                    transform: scale(1);
                }
                50% {
                    opacity: 0.5;
                    transform: scale(1.2);
                }
            }

            .hero h1 {
                font-size: 56px;
                font-weight: 800;
                line-height: 1.1;
                letter-spacing: -1.5px;
                color: #111827;
                margin-bottom: 22px;
            }
            .hero h1 .accent {
                color: #22c55e;
            }
            .hero p {
                font-size: 19px;
                color: #6b7280;
                margin-bottom: 36px;
                line-height: 1.6;
                max-width: 620px;
                margin-left: auto;
                margin-right: auto;
            }

            .hero-cta {
                display: flex;
                gap: 14px;
                justify-content: center;
                flex-wrap: wrap;
            }
            .btn-primary-lg {
                padding: 15px 32px;
                font-size: 16px;
                font-weight: 700;
                color: #fff;
                background: #22c55e;
                border: none;
                border-radius: 12px;
                text-decoration: none;
                cursor: pointer;
                transition:
                    transform 0.15s,
                    opacity 0.15s,
                    box-shadow 0.15s;
                box-shadow: 0 10px 24px rgba(34, 197, 94, 0.3);
                display: inline-flex;
                align-items: center;
                gap: 10px;
            }
            .btn-primary-lg:hover {
                transform: translateY(-2px);
                box-shadow: 0 14px 32px rgba(34, 197, 94, 0.4);
            }

            .btn-outline-lg {
                padding: 15px 32px;
                font-size: 16px;
                font-weight: 700;
                color: #374151;
                background: #fff;
                border: 1.5px solid #e5e7eb;
                border-radius: 12px;
                text-decoration: none;
                cursor: pointer;
                transition:
                    border-color 0.15s,
                    color 0.15s,
                    transform 0.15s;
                display: inline-flex;
                align-items: center;
                gap: 10px;
            }
            .btn-outline-lg:hover {
                border-color: #bbf7d0;
                color: #16a34a;
                transform: translateY(-2px);
            }

            .hero-trust {
                margin-top: 40px;
                display: flex;
                gap: 28px;
                justify-content: center;
                flex-wrap: wrap;
                color: #6b7280;
                font-size: 13px;
            }
            .hero-trust span {
                display: flex;
                align-items: center;
                gap: 6px;
            }
            .hero-trust .check {
                color: #22c55e;
                font-weight: 800;
            }

            /* ============ SECTION ============ */
            section {
                padding: 90px 32px;
            }
            .section-inner {
                max-width: 1200px;
                margin: 0 auto;
            }

            .section-head {
                text-align: center;
                margin-bottom: 60px;
            }
            .section-head h2 {
                font-size: 40px;
                font-weight: 800;
                color: #111827;
                letter-spacing: -1px;
                margin-bottom: 14px;
            }
            .section-head p {
                font-size: 17px;
                color: #6b7280;
                max-width: 620px;
                margin: 0 auto;
            }

            /* ============ FEATURES ============ */
            .features {
                background: #f9fafb;
                border-top: 1px solid #e5e7eb;
                border-bottom: 1px solid #e5e7eb;
            }
            .features-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 24px;
            }
            .feature-card {
                background: #fff;
                border-radius: 16px;
                padding: 32px;
                border: 1px solid #e5e7eb;
                transition:
                    transform 0.2s,
                    box-shadow 0.2s,
                    border-color 0.2s;
            }
            .feature-card:hover {
                transform: translateY(-6px);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
                border-color: #bbf7d0;
            }
            .feature-icon {
                width: 56px;
                height: 56px;
                border-radius: 14px;
                background: #f0fdf4;
                color: #16a34a;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 26px;
                margin-bottom: 20px;
            }
            .feature-card h3 {
                font-size: 18px;
                font-weight: 700;
                color: #111827;
                margin-bottom: 8px;
            }
            .feature-card p {
                font-size: 14px;
                color: #6b7280;
                line-height: 1.6;
            }

            /* ============ STEPS ============ */
            .steps-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 32px;
            }
            .step-card {
                position: relative;
                text-align: center;
                padding: 20px;
            }
            .step-num {
                width: 64px;
                height: 64px;
                border-radius: 50%;
                background: #22c55e;
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 24px;
                font-weight: 800;
                margin: 0 auto 20px;
                box-shadow: 0 10px 24px rgba(34, 197, 94, 0.3);
            }
            .step-card h3 {
                font-size: 18px;
                font-weight: 700;
                margin-bottom: 10px;
                color: #111827;
            }
            .step-card p {
                font-size: 14px;
                color: #6b7280;
                line-height: 1.6;
            }
            .step-arrow {
                position: absolute;
                top: 52px;
                right: -16px;
                font-size: 24px;
                color: #d1d5db;
            }
            .step-card:last-child .step-arrow {
                display: none;
            }

            /* ============ STATS ============ */
            .stats-section {
                background: linear-gradient(135deg, #111827 0%, #1f2937 100%);
                color: #fff;
            }
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 32px;
                text-align: center;
            }
            .stat-item .value {
                font-size: 42px;
                font-weight: 800;
                color: #22c55e;
                line-height: 1;
                margin-bottom: 8px;
            }
            .stat-item .label {
                font-size: 14px;
                color: #9ca3af;
            }

            /* ============ CTA ============ */
            .cta-section {
                background:
                    radial-gradient(
                        circle at 50% 50%,
                        rgba(34, 197, 94, 0.1),
                        transparent 60%
                    ),
                    #f9fafb;
                text-align: center;
            }
            .cta-box {
                max-width: 720px;
                margin: 0 auto;
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 24px;
                padding: 60px 40px;
                box-shadow: 0 24px 60px rgba(0, 0, 0, 0.08);
            }
            .cta-box h2 {
                font-size: 36px;
                font-weight: 800;
                color: #111827;
                letter-spacing: -1px;
                margin-bottom: 14px;
            }
            .cta-box p {
                font-size: 16px;
                color: #6b7280;
                margin-bottom: 32px;
            }

            /* ============ FOOTER ============ */
            footer {
                background: #111827;
                color: #9ca3af;
                padding: 60px 32px 30px;
            }
            .footer-inner {
                max-width: 1200px;
                margin: 0 auto;
            }
            .footer-top {
                display: grid;
                grid-template-columns: 2fr 1fr 1fr 1fr;
                gap: 40px;
                margin-bottom: 40px;
            }
            .footer-brand {
                display: flex;
                align-items: center;
                gap: 12px;
                font-size: 18px;
                font-weight: 800;
                color: #fff;
                margin-bottom: 14px;
            }
            .footer-brand-icon {
                width: 34px;
                height: 34px;
                border-radius: 8px;
                background: #22c55e;
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 800;
            }
            .footer-about {
                font-size: 13px;
                line-height: 1.6;
                max-width: 320px;
            }
            .footer-col h4 {
                color: #fff;
                font-size: 14px;
                font-weight: 700;
                margin-bottom: 16px;
            }
            .footer-col a {
                display: block;
                font-size: 13px;
                color: #9ca3af;
                text-decoration: none;
                margin-bottom: 10px;
                transition: color 0.15s;
            }
            .footer-col a:hover {
                color: #22c55e;
            }
            .footer-bottom {
                border-top: 1px solid #1f2937;
                padding-top: 24px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 13px;
                flex-wrap: wrap;
                gap: 16px;
            }

            /* ============ RESPONSIVE ============ */
            @media (max-width: 900px) {
                .hero h1 {
                    font-size: 40px;
                }
                .features-grid,
                .steps-grid {
                    grid-template-columns: 1fr;
                }
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
                .footer-top {
                    grid-template-columns: 1fr 1fr;
                }
                .step-arrow {
                    display: none;
                }
                .nav-links {
                    display: none;
                }
            }
            @media (max-width: 600px) {
                .hero {
                    padding: 60px 20px 70px;
                }
                .hero h1 {
                    font-size: 32px;
                }
                .hero p {
                    font-size: 16px;
                }
                section {
                    padding: 60px 20px;
                }
                .section-head h2 {
                    font-size: 28px;
                }
                .cta-box {
                    padding: 40px 24px;
                }
                .cta-box h2 {
                    font-size: 24px;
                }
                .footer-top {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </head>
    <body>
        {{-- ============ NAV ============ --}}
        <header class="nav">
            <div class="nav-inner">
                <a href="/" class="brand">
                    <span class="brand-icon">
                        <svg
                            width="22"
                            height="22"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path
                                d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"
                            />
                            <polyline points="9 22 9 12 15 12 15 22" />
                        </svg>
                    </span>
                    NEKJOUL IMANAGE
                </a>

                <nav class="nav-links">
                    <a href="#features">Features</a>
                    <a href="#how">How it works</a>
                    <a href="#stats">Stats</a>
                </nav>

                <div class="nav-actions">
                    <a href="{{ route('login') }}" class="btn-nav-login"
                        >Sign In</a
                    >
                    <a href="{{ route('register') }}" class="btn-nav-register"
                        >Get Started</a
                    >
                </div>
            </div>
        </header>

        {{-- ============ HERO ============ --}}
        <section class="hero">
            <div class="hero-inner">
                <div class="hero-badge">
                    <span class="dot"></span>
                    Trusted by tenants &amp; landlords
                </div>

                <h1>
                    Rental management<br />
                    <span class="accent">made simple.</span>
                </h1>

                <p>
                    Pay rent, submit maintenance requests, upload lease
                    documents, and track everything from one clean dashboard. No
                    paperwork. No hassle.
                </p>

                <div class="hero-cta">
                    <a href="{{ route('register') }}" class="btn-primary-lg">
                        Create Free Account →
                    </a>
                    <a href="{{ route('login') }}" class="btn-outline-lg">
                        Sign In
                    </a>
                </div>

                <div class="hero-trust">
                    <span><span class="check">✓</span> Free to use</span>
                    <span><span class="check">✓</span> No credit card</span>
                    <span><span class="check">✓</span> Instant setup</span>
                </div>
            </div>
        </section>

        {{-- ============ FEATURES ============ --}}
        <section class="features" id="features">
            <div class="section-inner">
                <div class="section-head">
                    <h2>Everything you need</h2>
                    <p>
                        Purpose-built tools for tenants and landlords — no
                        clutter, no confusion.
                    </p>
                </div>

                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">💳</div>
                        <h3>Pay Rent Online</h3>
                        <p>
                            Pay securely with card. Get instant PDF receipts.
                            Track your full payment history.
                        </p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">🛠</div>
                        <h3>Maintenance Requests</h3>
                        <p>
                            Submit repair requests with priority levels. Track
                            status from pending to resolved.
                        </p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">📄</div>
                        <h3>Document Storage</h3>
                        <p>
                            Upload lease agreements, receipts, and documents.
                            Access them anytime, anywhere.
                        </p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">🔔</div>
                        <h3>Real-Time Notifications</h3>
                        <p>
                            Get notified the moment something happens —
                            payments, requests, uploads.
                        </p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">📊</div>
                        <h3>Payment Insights</h3>
                        <p>
                            See your year-over-year spending, next rent due
                            date, and total paid at a glance.
                        </p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">🔒</div>
                        <h3>Secure &amp; Private</h3>
                        <p>
                            Your data is encrypted, your files are stored
                            safely, and only you can access them.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ============ HOW IT WORKS ============ --}}
        <section id="how">
            <div class="section-inner">
                <div class="section-head">
                    <h2>Get started in 3 steps</h2>
                    <p>
                        From signup to your first rent payment — takes less than
                        5 minutes.
                    </p>
                </div>

                <div class="steps-grid">
                    <div class="step-card">
                        <div class="step-num">1</div>
                        <h3>Create your account</h3>
                        <p>
                            Sign up with your email in seconds. No credit card
                            required.
                        </p>
                        <div class="step-arrow">→</div>
                    </div>

                    <div class="step-card">
                        <div class="step-num">2</div>
                        <h3>Connect with your landlord</h3>
                        <p>
                            Once your landlord shares your lease, everything
                            appears in your dashboard.
                        </p>
                        <div class="step-arrow">→</div>
                    </div>

                    <div class="step-card">
                        <div class="step-num">3</div>
                        <h3>Manage everything</h3>
                        <p>
                            Pay rent, submit requests, upload documents — all in
                            one clean place.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ============ STATS ============ --}}
        <section class="stats-section" id="stats">
            <div class="section-inner">
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="value">100%</div>
                        <div class="label">Digital &amp; paperless</div>
                    </div>
                    <div class="stat-item">
                        <div class="value">24/7</div>
                        <div class="label">Access anytime</div>
                    </div>
                    <div class="stat-item">
                        <div class="value">&lt;5m</div>
                        <div class="label">To get started</div>
                    </div>
                    <div class="stat-item">
                        <div class="value">$0</div>
                        <div class="label">Free to use</div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ============ CTA ============ --}}
        <section class="cta-section">
            <div class="section-inner">
                <div class="cta-box">
                    <h2>Ready to get started?</h2>
                    <p>
                        Join thousands of tenants and landlords managing rentals
                        the modern way.
                    </p>

                    <div class="hero-cta">
                        <a
                            href="{{ route('register') }}"
                            class="btn-primary-lg"
                        >
                            Create Free Account →
                        </a>
                        <a href="{{ route('login') }}" class="btn-outline-lg">
                            Sign In
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- ============ FOOTER ============ --}}
        <footer>
            <div class="footer-inner">
                <div class="footer-top">
                    <div>
                        <div class="footer-brand">
                            <span class="footer-brand-icon">N</span>
                            NEKJOUL IMANAGE
                        </div>
                        <p class="footer-about">
                            Modern rental management for tenants and landlords.
                            Pay rent, submit requests, and manage documents —
                            all in one place.
                        </p>
                    </div>

                    <div class="footer-col">
                        <h4>Product</h4>
                        <a href="#features">Features</a>
                        <a href="#how">How it works</a>
                        <a href="#stats">Stats</a>
                    </div>

                    <div class="footer-col">
                        <h4>Account</h4>
                        <a href="{{ route('login') }}">Sign In</a>
                        <a href="{{ route('register') }}">Create Account</a>
                    </div>

                    <div class="footer-col">
                        <h4>Support</h4>
                        <a href="mailto:support@nekjoul.com">Email Support</a>
                        <a href="#">Help Center</a>
                        <a href="#">Contact</a>
                    </div>
                </div>

                <div class="footer-bottom">
                    <div>
                        &copy; {{ date("Y") }} NEKJOUL IMANAGE. All rights
                        reserved.
                    </div>
                    <div>Made with care for renters everywhere.</div>
                </div>
            </div>
        </footer>
    </body>
</html>
