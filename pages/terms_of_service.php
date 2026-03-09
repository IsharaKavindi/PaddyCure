<?php
require('../config.php');
require('../utils/database.php');
session_start();
$conn = initialize_database();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Terms of Service – PaddyCare</title>
    <link rel="stylesheet" href="../public/styles/styles.css">
    <link rel="stylesheet" href="../public/styles/fonts.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>/public/images/paddyCureLogo2.png" type="image/x-icon">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --forest: #1B3A2D; --deep: #122A20; --sage: #4A7C59;
            --lime: #A8C97F; --straw: #F5EDD3; --white: #FAFDF7; --muted: #8BAF8E;
            --serif: 'Playfair Display', Georgia, serif;
            --sans: 'DM Sans', sans-serif;
        }
        body { background: var(--white); color: var(--forest); font-family: var(--sans); font-weight: 300; }

        .page-hero { background: var(--deep); padding: 100px 8% 72px; position: relative; overflow: hidden; }
        .page-hero::before { content:''; position:absolute; inset:0; background-image: repeating-linear-gradient(160deg, rgba(107,158,111,.06) 0, rgba(107,158,111,.06) 1px, transparent 1px, transparent 60px); }
        .page-hero::after { content:''; position:absolute; inset:0; background: radial-gradient(ellipse 60% 60% at 80% 50%, rgba(74,124,89,.2) 0%, transparent 70%); }
        .hero-inner { position: relative; z-index: 1; }
        .hero-label { font-size:10px; font-weight:500; letter-spacing:.28em; text-transform:uppercase; color:var(--lime); display:flex; align-items:center; gap:12px; margin-bottom:18px; }
        .hero-label::before { content:''; display:block; width:28px; height:1px; background:var(--lime); }
        .hero-title { font-family:var(--serif); font-size:clamp(2.4rem,5vw,3.8rem); font-weight:700; color:#fff; line-height:1.1; margin-bottom:16px; }
        .hero-title em { font-style:italic; color:var(--lime); }
        .hero-meta { font-size:13px; color:rgba(250,253,247,.45); margin-top:12px; }

        .legal-layout { display:grid; grid-template-columns:220px 1fr; gap:56px; max-width:1100px; margin:0 auto; padding:72px 8% 100px; }

        .legal-nav { position:sticky; top:100px; align-self:start; }
        .legal-nav-title { font-size:10px; font-weight:500; letter-spacing:.2em; text-transform:uppercase; color:var(--muted); margin-bottom:14px; }
        .legal-nav a { display:block; font-size:13px; color:#5a7a60; text-decoration:none; padding:7px 0 7px 14px; border-left:2px solid #e0ead8; transition:color .2s, border-color .2s; line-height:1.4; }
        .legal-nav a:hover { color:var(--forest); border-color:var(--sage); }
        .legal-nav a.active { color:var(--forest); border-color:var(--lime); font-weight:500; }

        .legal-content { min-width:0; }
        .policy-section { margin-bottom:52px; scroll-margin-top:100px; }
        .policy-section:last-child { margin-bottom:0; }
        .policy-number { font-size:10px; font-weight:500; letter-spacing:.2em; text-transform:uppercase; color:var(--muted); margin-bottom:8px; }
        .policy-title { font-family:var(--serif); font-size:1.55rem; font-weight:400; color:var(--forest); margin-bottom:16px; line-height:1.3; }
        .policy-body { font-size:15px; line-height:1.85; color:#3a5a45; }
        .policy-body p { margin-bottom:14px; }
        .policy-body p:last-child { margin-bottom:0; }
        .policy-body ul { padding-left:20px; margin-bottom:14px; }
        .policy-body ul li { margin-bottom:6px; }
        .policy-body a { color:var(--sage); text-decoration:underline; }
        .policy-divider { border:none; border-top:1px solid #e8f0e0; margin-bottom:52px; }

        .policy-highlight { background:rgba(168,201,127,.12); border:1px solid rgba(168,201,127,.3); border-radius:8px; padding:20px 24px; margin-bottom:32px; font-size:14px; line-height:1.7; color:var(--forest); }
        .policy-highlight strong { color:var(--forest); }

        .contact-card { background:var(--deep); border-radius:10px; padding:32px 36px; display:flex; align-items:center; justify-content:space-between; gap:24px; flex-wrap:wrap; }
        .contact-card-text h4 { font-family:var(--serif); font-size:1.3rem; color:#fff; margin-bottom:6px; }
        .contact-card-text p { font-size:14px; color:rgba(250,253,247,.55); }
        .contact-card-btn { display:inline-flex; align-items:center; gap:8px; background:var(--lime); color:var(--deep); padding:12px 24px; border-radius:4px; font-family:var(--sans); font-size:13px; font-weight:500; text-decoration:none; white-space:nowrap; transition:background .2s, transform .2s; }
        .contact-card-btn:hover { background:#bcd98e; transform:translateY(-2px); }

        @media(max-width:768px) { .legal-layout { grid-template-columns:1fr; } .legal-nav { display:none; } }
    </style>
</head>
<body>

<?php include('../components/header_navigation_bar.php'); ?>

<section class="page-hero">
    <div class="hero-inner">
        <p class="hero-label">Legal</p>
        <h1 class="hero-title">Terms of <em>Service</em></h1>
        <p class="hero-meta">Last updated: <?= date('F j, Y') ?> &nbsp;·&nbsp; Effective immediately</p>
    </div>
</section>

<div class="legal-layout">

    <aside class="legal-nav">
        <div class="legal-nav-title">Contents</div>
        <a href="#acceptance"   class="active">1. Acceptance</a>
        <a href="#use">2. Use of Service</a>
        <a href="#account">3. Your Account</a>
        <a href="#ai">4. AI Disclaimer</a>
        <a href="#content">5. User Content</a>
        <a href="#prohibited">6. Prohibited Conduct</a>
        <a href="#ip">7. Intellectual Property</a>
        <a href="#liability">8. Limitation of Liability</a>
        <a href="#termination">9. Termination</a>
        <a href="#changes">10. Changes to Terms</a>
        <a href="#contact">11. Contact</a>
    </aside>

    <main class="legal-content">

        <div class="policy-highlight">
            <strong>Summary:</strong> PaddyCare is a free AI tool to help Sri Lankan farmers detect paddy diseases. Our AI results are guidance only — not a substitute for professional agricultural advice. Use the platform responsibly.
        </div>

        <section class="policy-section" id="acceptance">
            <div class="policy-number">Section 01</div>
            <h2 class="policy-title">Acceptance of Terms</h2>
            <div class="policy-body">
                <p>By accessing or using PaddyCare, you agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use the platform.</p>
                <p>These terms apply to all users, including visitors, registered farmers, and administrators.</p>
            </div>
        </section>
        <hr class="policy-divider">

        <section class="policy-section" id="use">
            <div class="policy-number">Section 02</div>
            <h2 class="policy-title">Use of Service</h2>
            <div class="policy-body">
                <p>PaddyCare provides an AI-powered paddy disease detection tool, a community forum for farmers, and educational articles about paddy farming. The platform is free to use for all registered users.</p>
                <p>You agree to use PaddyCare only for lawful purposes related to paddy farming and agricultural improvement. The service is intended primarily for farmers and agricultural professionals in Sri Lanka and South Asia.</p>
            </div>
        </section>
        <hr class="policy-divider">

        <section class="policy-section" id="account">
            <div class="policy-number">Section 03</div>
            <h2 class="policy-title">Your Account</h2>
            <div class="policy-body">
                <p>You are responsible for maintaining the confidentiality of your account credentials. Do not share your password with anyone.</p>
                <p>You must provide accurate information when registering. PaddyCare reserves the right to suspend or delete accounts that provide false information or violate these terms.</p>
                <p>You are responsible for all activity that occurs under your account.</p>
            </div>
        </section>
        <hr class="policy-divider">

        <section class="policy-section" id="ai">
            <div class="policy-number">Section 04</div>
            <h2 class="policy-title">AI Diagnosis Disclaimer</h2>
            <div class="policy-body">
                <p>PaddyCare's AI disease detection tool is provided as a <strong>decision-support aid only</strong>. Results are based on image analysis and are not guaranteed to be 100% accurate.</p>
                <p>AI-generated diagnoses should not be used as a sole basis for significant farming decisions. Always consult a qualified agricultural officer or extension officer before applying pesticides or treatments, especially at scale.</p>
                <p>PaddyCare is not liable for crop loss, financial loss, or any other damages arising from reliance on AI-generated diagnoses.</p>
            </div>
        </section>
        <hr class="policy-divider">

        <section class="policy-section" id="content">
            <div class="policy-number">Section 05</div>
            <h2 class="policy-title">User Content</h2>
            <div class="policy-body">
                <p>When you post content in the community feed (text, images, or comments), you grant PaddyCare a non-exclusive licence to display that content on the platform.</p>
                <p>You retain ownership of your content. You are solely responsible for ensuring your posts do not violate any laws or third-party rights.</p>
                <p>PaddyCare reserves the right to remove any content that violates these terms or is deemed inappropriate.</p>
            </div>
        </section>
        <hr class="policy-divider">

        <section class="policy-section" id="prohibited">
            <div class="policy-number">Section 06</div>
            <h2 class="policy-title">Prohibited Conduct</h2>
            <div class="policy-body">
                <p>You must not:</p>
                <ul>
                    <li>Upload images or content that is offensive, harmful, or unrelated to paddy farming</li>
                    <li>Attempt to reverse-engineer, hack, or disrupt the platform</li>
                    <li>Use the platform to spam other users or distribute malware</li>
                    <li>Impersonate another person or organisation</li>
                    <li>Scrape or bulk-download data from PaddyCare without permission</li>
                    <li>Use the platform for commercial advertising without prior consent</li>
                </ul>
            </div>
        </section>
        <hr class="policy-divider">

        <section class="policy-section" id="ip">
            <div class="policy-number">Section 07</div>
            <h2 class="policy-title">Intellectual Property</h2>
            <div class="policy-body">
                <p>All PaddyCare branding, design, code, articles, and AI models are the intellectual property of PaddyCare and its developers. You may not copy, reproduce, or distribute any part of the platform without written permission.</p>
                <p>Articles and educational content published by admins are owned by PaddyCare. Community posts remain the property of their respective authors.</p>
            </div>
        </section>
        <hr class="policy-divider">

        <section class="policy-section" id="liability">
            <div class="policy-number">Section 08</div>
            <h2 class="policy-title">Limitation of Liability</h2>
            <div class="policy-body">
                <p>PaddyCare is provided "as is" without any warranties, express or implied. We do not guarantee uninterrupted or error-free operation of the platform.</p>
                <p>To the fullest extent permitted by law, PaddyCare and its developers shall not be liable for any indirect, incidental, or consequential damages arising from your use of the platform, including crop loss or financial loss based on AI diagnosis results.</p>
            </div>
        </section>
        <hr class="policy-divider">

        <section class="policy-section" id="termination">
            <div class="policy-number">Section 09</div>
            <h2 class="policy-title">Termination</h2>
            <div class="policy-body">
                <p>PaddyCare reserves the right to suspend or terminate your account at any time if you violate these terms, without prior notice.</p>
                <p>You may also delete your account at any time by contacting us. Upon deletion, your personal data will be removed in accordance with our <a href="<?= BASE_URL ?>/pages/privacy_policy.php">Privacy Policy</a>.</p>
            </div>
        </section>
        <hr class="policy-divider">

        <section class="policy-section" id="changes">
            <div class="policy-number">Section 10</div>
            <h2 class="policy-title">Changes to Terms</h2>
            <div class="policy-body">
                <p>We may update these Terms of Service from time to time. The updated date at the top of this page will reflect when changes were last made.</p>
                <p>Continued use of PaddyCare after changes are posted means you accept the revised terms. We recommend checking this page periodically.</p>
            </div>
        </section>
        <hr class="policy-divider">

        <section class="policy-section" id="contact">
            <div class="policy-number">Section 11</div>
            <h2 class="policy-title">Contact</h2>
            <div class="policy-body">
                <p>If you have any questions about these Terms of Service, please contact us:</p>
            </div>
            <div class="contact-card" style="margin-top:24px">
                <div class="contact-card-text">
                    <h4>Get in touch with PaddyCare</h4>
                    <p>We typically respond within 1–2 business days.</p>
                </div>
                <a href="<?= BASE_URL ?>/pages/contact/contact.php" class="contact-card-btn">
                    Contact Us
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>
        </section>

    </main>
</div>

<?php include('../components/footer.php'); ?>

<script>
const sections = document.querySelectorAll('.policy-section');
const navLinks  = document.querySelectorAll('.legal-nav a');
const observer  = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            navLinks.forEach(a => a.classList.remove('active'));
            const active = document.querySelector(`.legal-nav a[href="#${entry.target.id}"]`);
            if (active) active.classList.add('active');
        }
    });
}, { rootMargin: '-30% 0px -60% 0px' });
sections.forEach(s => observer.observe(s));
</script>
</body>
</html>