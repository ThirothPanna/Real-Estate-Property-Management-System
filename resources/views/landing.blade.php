<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f6f8f5">
    <title>NEKJOUL IMANAGE — A better way to rent</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --ink:#17251f; --muted:#64736b; --green:#1c7954; --green-dark:#145b3e; --line:#e4ebe5; --soft:#f5f8f5; }
        * { box-sizing:border-box; }
        html { scroll-behavior:smooth; }
        body { margin:0; color:var(--ink); background:#fff; font-family:Inter,"Segoe UI",system-ui,sans-serif; line-height:1.55; }
        a { color:inherit; }
        .site-nav { position:sticky; top:0; z-index:20; border-bottom:1px solid rgba(23,37,31,.08); background:rgba(255,255,255,.9); backdrop-filter:blur(14px); }
        .nav-inner { display:flex; width:min(1280px,100%); min-height:74px; align-items:center; justify-content:space-between; gap:24px; margin:0 auto; padding:12px 32px; }
        .brand img { display:block; width:auto; max-width:230px; height:38px; }
        .nav-links,.nav-actions { display:flex; align-items:center; gap:26px; }
        .nav-links a { color:#53625a; font-size:13px; font-weight:650; text-decoration:none; transition:color .2s; }
        .nav-links a:hover { color:var(--green); }
        .nav-actions { gap:9px; }
        .button { display:inline-flex; min-height:46px; align-items:center; justify-content:center; gap:9px; padding:0 20px; border:1px solid transparent; border-radius:999px; font-size:13px; font-weight:750; text-decoration:none; transition:transform .18s,background .18s,border-color .18s,box-shadow .18s; }
        .button:hover { transform:translateY(-2px); }
        .button-primary { color:#fff; background:var(--green); box-shadow:0 9px 22px rgba(28,121,84,.18); }
        .button-primary:hover { background:var(--green-dark); box-shadow:0 12px 26px rgba(28,121,84,.25); }
        .button-quiet { border-color:var(--line); color:#33433a; background:#fff; }
        .button-quiet:hover { border-color:#b9d2c1; color:var(--green); }
        .button svg { width:17px; height:17px; }

        .hero { overflow:hidden; padding:58px 32px 74px; background:radial-gradient(ellipse at 14% 2%,#e8f2e9 0,transparent 43%),linear-gradient(135deg,#f8faf7 0%,#fff 66%); }
        .hero-grid { display:grid; width:min(1216px,100%); grid-template-columns:.9fr 1.1fr; align-items:center; gap:58px; margin:0 auto; }
        .eyebrow { display:inline-flex; align-items:center; gap:9px; padding:8px 13px; border:1px solid #d7e7da; border-radius:999px; color:var(--green); background:#f4f9f4; font-size:11px; font-weight:750; letter-spacing:.045em; text-transform:uppercase; }
        .eyebrow-dot { width:7px; height:7px; border-radius:50%; background:#54a675; box-shadow:0 0 0 4px #ddefe2; }
        h1 { max-width:620px; margin:23px 0 18px; color:var(--ink); font-size:clamp(45px,5.5vw,72px); font-weight:760; line-height:1.01; letter-spacing:-.06em; }
        h1 span { color:var(--green); }
        .hero-copy { max-width:535px; margin:0; color:var(--muted); font-size:16px; line-height:1.8; }
        .hero-actions { display:flex; flex-wrap:wrap; gap:11px; margin-top:27px; }
        .hero-points { display:flex; flex-wrap:wrap; gap:12px 21px; margin-top:23px; color:#627067; font-size:11px; font-weight:600; }
        .hero-points span { display:inline-flex; align-items:center; gap:7px; }
        .hero-points svg { width:15px; height:15px; color:var(--green); }
        .hero-photo-wrap { position:relative; min-width:0; }
        .hero-photo { display:block; width:100%; aspect-ratio:1.42; border:1px solid rgba(23,37,31,.08); border-radius:25px; object-fit:cover; object-position:center; box-shadow:0 28px 65px rgba(19,43,31,.18); }
        .photo-caption { position:absolute; right:18px; bottom:18px; display:flex; max-width:calc(100% - 36px); align-items:center; gap:11px; padding:12px 15px; border:1px solid rgba(255,255,255,.8); border-radius:15px; background:rgba(255,255,255,.92); box-shadow:0 12px 30px rgba(0,0,0,.13); backdrop-filter:blur(12px); }
        .caption-icon { display:grid; width:38px; height:38px; flex:none; place-items:center; border-radius:12px; color:var(--green); background:#e9f3eb; }
        .caption-icon svg { width:19px; height:19px; }
        .caption-title { color:var(--ink); font-size:12px; font-weight:800; }
        .caption-subtitle { margin-top:2px; color:var(--muted); font-size:10px; }

        .section { padding:83px 32px; }
        .section-inner { width:min(1152px,100%); margin:0 auto; }
        .section-heading { max-width:660px; margin:0 auto 39px; text-align:center; }
        .section-heading .eyebrow { margin-bottom:14px; }
        .section-heading h2 { margin:0; color:var(--ink); font-size:clamp(30px,4vw,45px); font-weight:740; line-height:1.1; letter-spacing:-.05em; }
        .section-heading p { margin:13px auto 0; color:var(--muted); font-size:15px; line-height:1.7; }
        .feature-section { background:var(--soft); }
        .feature-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:15px; }
        .feature-card { min-height:200px; padding:24px; border:1px solid var(--line); border-radius:17px; background:#fff; transition:transform .2s,border-color .2s,box-shadow .2s; }
        .feature-card:hover { transform:translateY(-4px); border-color:#c5ddcc; box-shadow:0 16px 34px rgba(23,37,31,.07); }
        .feature-icon { display:grid; width:46px; height:46px; place-items:center; margin-bottom:19px; border-radius:14px; color:var(--green); background:#edf5ef; }
        .feature-icon svg { width:23px; height:23px; }
        .feature-card h3 { margin:0 0 7px; color:#203129; font-size:15px; font-weight:760; }
        .feature-card p { margin:0; color:var(--muted); font-size:13px; line-height:1.7; }

        .audience-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:18px; }
        .audience-card { position:relative; overflow:hidden; padding:30px; border:1px solid var(--line); border-radius:20px; background:linear-gradient(145deg,#fff,#f7faf7); }
        .audience-card.landlord { background:linear-gradient(145deg,#f2f7f2,#fff); }
        .audience-label { color:var(--green); font-size:11px; font-weight:800; letter-spacing:.08em; text-transform:uppercase; }
        .audience-card h3 { margin:10px 0; font-size:23px; letter-spacing:-.035em; }
        .audience-card p { max-width:440px; margin:0; color:var(--muted); font-size:14px; line-height:1.7; }
        .audience-link { display:inline-flex; align-items:center; gap:7px; margin-top:22px; color:var(--green); font-size:13px; font-weight:750; text-decoration:none; }
        .audience-link:hover { color:var(--green-dark); }
        .audience-link svg { width:16px; height:16px; }

        .steps-section { background:#f7f9f6; }
        .step-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:20px; }
        .step-card { position:relative; padding:27px 24px; border:1px solid var(--line); border-radius:17px; background:#fff; }
        .step-number { display:grid; width:39px; height:39px; place-items:center; border-radius:13px; color:#fff; background:var(--green); font-size:14px; font-weight:800; }
        .step-card h3 { margin:19px 0 7px; font-size:15px; }
        .step-card p { margin:0; color:var(--muted); font-size:13px; line-height:1.7; }

        .cta-section { padding:76px 32px; background:radial-gradient(ellipse at center,#e8f2e9 0%,#f7f9f7 63%); }
        .cta-card { width:min(820px,100%); margin:0 auto; padding:45px 30px; border:1px solid #e0e9e1; border-radius:24px; background:rgba(255,255,255,.86); box-shadow:0 20px 55px rgba(20,37,31,.07); text-align:center; }
        .cta-card h2 { margin:0; font-size:clamp(29px,4vw,40px); letter-spacing:-.05em; }
        .cta-card p { margin:12px auto 22px; color:var(--muted); font-size:14px; }
        .cta-actions { display:flex; justify-content:center; flex-wrap:wrap; gap:10px; }
        .footer { padding:35px 32px; color:#d4ded7; background:#14271f; }
        .footer-inner { display:flex; width:min(1216px,100%); align-items:center; justify-content:space-between; gap:22px; margin:0 auto; }
        .footer-brand { display:flex; align-items:center; gap:11px; color:#fff; font-size:12px; font-weight:800; letter-spacing:.03em; }
        .footer-brand img { width:30px; height:30px; border-radius:8px; }
        .footer-links { display:flex; flex-wrap:wrap; gap:19px; }
        .footer-links a { color:#d4ded7; font-size:12px; text-decoration:none; }
        .footer-links a:hover { color:#a5d7b6; }
        .footer-copy { color:#aab8af; font-size:11px; }

        @media (max-width:900px) {
            .hero-grid { grid-template-columns:1fr; gap:34px; }
            .hero-text { max-width:690px; }
            .hero-photo { aspect-ratio:1.65; }
            .feature-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
            .nav-links { gap:16px; }
        }
        @media (max-width:620px) {
            .nav-inner { min-height:64px; gap:10px; padding:10px 16px; }
            .brand img { max-width:155px; height:auto; }
            .nav-links { display:none; }
            .nav-actions { gap:6px; }
            .button { min-height:40px; padding:0 14px; font-size:12px; }
            .hero { padding:42px 19px 51px; }
            h1 { margin-top:19px; font-size:clamp(43px,12vw,58px); }
            .hero-copy { font-size:14px; }
            .hero-actions { gap:8px; }
            .hero-photo { aspect-ratio:1.08; border-radius:19px; }
            .photo-caption { right:10px; bottom:10px; max-width:calc(100% - 20px); padding:9px 11px; }
            .caption-icon { width:33px; height:33px; }
            .hero-points { gap:10px 14px; font-size:10px; }
            .section { padding:62px 19px; }
            .section-heading { margin-bottom:28px; }
            .section-heading p { font-size:14px; }
            .feature-grid,.audience-grid,.step-grid { grid-template-columns:1fr; }
            .feature-card { min-height:0; padding:21px; }
            .audience-card { padding:23px; }
            .cta-section { padding:58px 19px; }
            .cta-card { padding:34px 20px; }
            .footer { padding:28px 19px; }
            .footer-inner { align-items:flex-start; flex-direction:column; }
        }
        @media (prefers-reduced-motion:reduce) { *,*::before,*::after { scroll-behavior:auto !important; transition:none !important; animation:none !important; } }
    </style>
</head>
<body>
    <header class="site-nav">
        <div class="nav-inner">
            <a class="brand" href="/" aria-label="NEKJOUL IMANAGE home">
                <img src="{{ asset('images/logo-full-removebg-preview.png') }}" alt="NEKJOUL IMANAGE">
            </a>
            <nav class="nav-links" aria-label="Main navigation">
                <a href="#features">Features</a>
                <a href="#for-you">For you</a>
                <a href="#how-it-works">How it works</a>
            </nav>
            <div class="nav-actions">
                <a class="button button-quiet" href="{{ route('login') }}">Sign in</a>
                <a class="button button-primary" href="{{ route('register') }}">Get started</a>
            </div>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="hero-grid">
                <div class="hero-text">
                    <div class="eyebrow"><span class="eyebrow-dot"></span> A simpler way to manage rentals</div>
                    <h1>Renting, made<br><span>more human.</span></h1>
                    <p class="hero-copy">Find a place, stay in sync with your landlord, and keep rent details, maintenance requests, and documents together in one calm workspace.</p>
                    <div class="hero-actions">
                        <a class="button button-primary" href="{{ route('register') }}">Create your account
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                        </a>
                        <a class="button button-quiet" href="{{ route('login') }}">I already have an account</a>
                    </div>
                    <div class="hero-points">
                        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg>For tenants and landlords</span>
                        <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 4 4L19 6"/></svg>Your rental details together</span>
                    </div>
                </div>

                <div class="hero-photo-wrap">
                    <img class="hero-photo" src="{{ asset('images/31dc743f-fcbd-4238-a4c6-34b906275793.png') }}" alt="Modern apartment building glowing at dusk">
                    <div class="photo-caption">
                        <span class="caption-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m3 10 9-7 9 7M5 9v12h14V9M9 21v-7h6v7"/></svg></span>
                        <span><span class="caption-title">Home, in good hands</span><br><span class="caption-subtitle">The details of renting, all together</span></span>
                    </div>
                </div>
            </div>
        </section>

        <section class="section feature-section" id="features">
            <div class="section-inner">
                <div class="section-heading">
                    <span class="eyebrow">One workspace, less hassle</span>
                    <h2>The everyday rental stuff, made easier.</h2>
                    <p>Keep the useful details close at hand, and make it easier to stay on the same page.</p>
                </div>
                <div class="feature-grid">
                    <article class="feature-card">
                        <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18M7 15h3"/></svg></div>
                        <h3>Rent details &amp; records</h3><p>See rent information, payment records, and receipts together.</p>
                    </article>
                    <article class="feature-card">
                        <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a5 5 0 0 0-6.6 6.6L3 18a2.1 2.1 0 0 0 3 3l5.1-5.1a5 5 0 0 0 6.6-6.6L14 13l-3-3z"/></svg></div>
                        <h3>Maintenance requests</h3><p>Send a request, add the details, and follow its progress.</p>
                    </article>
                    <article class="feature-card">
                        <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M8 13h8M8 17h8"/></svg></div>
                        <h3>Shared documents</h3><p>Keep leases, receipts, and other rental files easy to access.</p>
                    </article>
                    <article class="feature-card">
                        <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8A8.5 8.5 0 0 1 8.7 3.9a8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8z"/></svg></div>
                        <h3>Direct messaging</h3><p>Ask questions and share photos or voice messages in a private chat.</p>
                    </article>
                    <article class="feature-card">
                        <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18M18 17V9M13 17V5M8 17v-3"/></svg></div>
                        <h3>A clearer overview</h3><p>Get key property, tenancy, request, and payment information at a glance.</p>
                    </article>
                    <article class="feature-card">
                        <div class="feature-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4M12 15v3"/></svg></div>
                        <h3>Separate accounts</h3><p>Tenant and landlord spaces keep each person’s rental tasks organized.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section" id="for-you">
            <div class="section-inner">
                <div class="section-heading">
                    <span class="eyebrow">Made for both sides</span>
                    <h2>Better communication makes a better home.</h2>
                    <p>One shared place for the rental relationship, with useful tools for each role.</p>
                </div>
                <div class="audience-grid">
                    <article class="audience-card">
                        <div class="audience-label">For tenants</div>
                        <h3>Find a place. Stay in the loop.</h3>
                        <p>Browse available homes, ask landlords questions, send maintenance requests, and keep your rental documents nearby.</p>
                        <a class="audience-link" href="{{ route('register') }}">Get started as a tenant <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
                    </article>
                    <article class="audience-card landlord">
                        <div class="audience-label">For landlords</div>
                        <h3>Keep your properties in order.</h3>
                        <p>Manage property details, connect with tenants, review requests, and keep key records together.</p>
                        <a class="audience-link" href="{{ route('register') }}">Get started as a landlord <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
                    </article>
                </div>
            </div>
        </section>

        <section class="section steps-section" id="how-it-works">
            <div class="section-inner">
                <div class="section-heading">
                    <span class="eyebrow">A straightforward start</span>
                    <h2>Get set up, then get on with your day.</h2>
                    <p>Your rental workspace is ready when you are.</p>
                </div>
                <div class="step-grid">
                    <article class="step-card"><span class="step-number">01</span><h3>Create your account</h3><p>Choose tenant or landlord and set up your own space.</p></article>
                    <article class="step-card"><span class="step-number">02</span><h3>Connect around a property</h3><p>Find a rental or connect with a tenant through your property.</p></article>
                    <article class="step-card"><span class="step-number">03</span><h3>Keep things moving</h3><p>Message, manage requests, and keep rental details easy to find.</p></article>
                </div>
            </div>
        </section>

        <section class="cta-section">
            <div class="cta-card">
                <span class="eyebrow">A calmer rental routine</span>
                <h2>Make room for easier renting.</h2>
                <p>Bring your rental details, conversations, and day-to-day tasks together.</p>
                <div class="cta-actions">
                    <a class="button button-primary" href="{{ route('register') }}">Create your account</a>
                    <a class="button button-quiet" href="{{ route('login') }}">Sign in</a>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-inner">
            <div class="footer-brand"><img src="{{ asset('images/logo-icon.png') }}" alt=""> NEKJOUL IMANAGE</div>
            <nav class="footer-links" aria-label="Footer navigation"><a href="#features">Features</a><a href="#for-you">For tenants &amp; landlords</a><a href="mailto:support@nekjoul.com">Contact support</a></nav>
            <div class="footer-copy">&copy; {{ date('Y') }} NEKJOUL IMANAGE</div>
        </div>
    </footer>
</body>
</html>
