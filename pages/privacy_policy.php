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
    <title>Privacy Policy – PaddyCure</title>
    <link rel="stylesheet" href="../public/styles/styles.css">
    <link rel="stylesheet" href="../public/styles/fonts.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>/public/images/paddyCureLogo2.png" type="image/x-icon">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --forest: #1B3A2D; --deep: #122A20; --sage: #4A7C59; --moss: #6B9E6F;
            --lime: #A8C97F; --straw: #F5EDD3; --white: #FAFDF7; --muted: #8BAF8E;
            --serif: 'Playfair Display', Georgia, serif;
            --sans: 'DM Sans', sans-serif;
        }
        body { background: var(--white); color: var(--forest); font-family: var(--sans); font-weight: 300; }

        /* ── PAGE HERO ── */
        .page-hero {
            background: var(--deep); padding: 100px 8% 72px;
            position: relative; overflow: hidden;
        }
        .page-hero::before {
            content: ''; position: absolute; inset: 0;
            background-image: repeating-linear-gradient(160deg, rgba(107,158,111,.06) 0, rgba(107,158,111,.06) 1px, transparent 1px, transparent 60px);
        }
        .page-hero::after {
            content: ''; position: absolute; inset: 0;
            background: radial-gradient(ellipse 60% 60% at 80% 50%, rgba(74,124,89,.2) 0%, transparent 70%);
        }
        .hero-inner { position: relative; z-index: 1; }
        .hero-label {
            font-size: 10px; font-weight: 500; letter-spacing: .28em; text-transform: uppercase;
            color: var(--lime); display: flex; align-items: center; gap: 12px; margin-bottom: 18px;
        }
        .hero-label::before { content: ''; display: block; width: 28px; height: 1px; background: var(--lime); }
        .hero-title {
            font-family: var(--serif); font-size: clamp(2.4rem, 5vw, 3.8rem);
            font-weight: 700; color: var(--white); line-height: 1.1; margin-bottom: 16px;
        }
        .hero-title em { font-style: italic; color: var(--lime); }
        .hero-meta { font-size: 13px; color: rgba(250,253,247,.45); margin-top: 12px; }

        /* ── LAYOUT ── */
        .legal-layout {
            display: grid; grid-template-columns: 220px 1fr; gap: 56px;
            max-width: 1100px; margin: 0 auto; padding: 72px 8% 100px;
        }

        /* ── SIDEBAR NAV ── */
        .legal-nav { position: sticky; top: 100px; align-self: start; }
        .legal-nav-title { font-size: 10px; font-weight: 500; letter-spacing: .2em; text-transform: uppercase; color: var(--muted); margin-bottom: 14px; }
        .legal-nav a {
            display: block; font-size: 13px; color: #5a7a60; text-decoration: none;
            padding: 7px 0 7px 14px; border-left: 2px solid #e0ead8;
            transition: color .2s, border-color .2s; line-height: 1.4;
        }
        .legal-nav a:hover { color: var(--forest); border-color: var(--sage); }
        .legal-nav a.active { color: var(--forest); border-color: var(--lime); font-weight: 500; }

        /* ── CONTENT ── */
        .legal-content { min-width: 0; }
        .policy-section { margin-bottom: 52px; scroll-margin-top: 100px; }
        .policy-section:last-child { margin-bottom: 0; }
        .policy-number {
            font-size: 10px; font-weight: 500; letter-spacing: .2em; text-transform: uppercase;
            color: var(--muted); margin-bottom: 8px;
        }
        .policy-title {
            font-family: var(--serif); font-size: 1.55rem; font-weight: 400;
            color: var(--forest); margin-bottom: 16px; line-height: 1.3;
        }
        .policy-body { font-size: 15px; line-height: 1.85; color: #3a5a45; }
        .policy-body p { margin-bottom: 14px; }
        .policy-body p:last-child { margin-bottom: 0; }
        .policy-body ul { padding-left: 20px; margin-bottom: 14px; }
        .policy-body ul li { margin-bottom: 6px; }
        .policy-body a { color: var(--sage); text-decoration: underline; }
        .policy-body a:hover { color: var(--forest); }
        .policy-divider { border: none; border-top: 1px solid #e8f0e0; margin-bottom: 52px; }

        /* ── HIGHLIGHT BOX ── */
        .policy-highlight {
            background: rgba(168,201,127,.12); border: 1px solid rgba(168,201,127,.3);
            border-radius: 8px; padding: 20px 24px; margin-bottom: 16px;
            font-size: 14px; line-height: 1.7; color: var(--forest);
        }
        .policy-highlight strong { color: var(--forest); }

        /* ── CONTACT CARD ── */
        .contact-card {
            background: var(--deep); border-radius: 10px; padding: 32px 36px;
            display: flex; align-items: center; justify-content: space-between; gap: 24px; flex-wrap: wrap;
        }
        .contact-card-text h4 { font-family: var(--serif); font-size: 1.3rem; color: var(--white); margin-bottom: 6px; }
        .contact-card-text p { font-size: 14px; color: rgba(250,253,247,.55); }
        .contact-card-btn {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--lime); color: var(--deep); padding: 12px 24px;
            border-radius: 4px; font-family: var(--sans); font-size: 13px; font-weight: 500;
            text-decoration: none; white-space: nowrap; transition: background .2s, transform .2s;
        }
        .contact-card-btn:hover { background: #bcd98e; transform: translateY(-2px); }

        @media(max-width: 768px) {
            .legal-layout { grid-template-columns: 1fr; }
            .legal-nav { display: none; }
        }
    </style>
</head>
<body>

<?php include('../components/header_navigation_bar.php'); ?>

<!-- HERO -->
<section class="page-hero">
    <div class="hero-inner">
        <p class="hero-label">Legal</p>
        <h1 class="hero-title">Privacy <em>Policy</em></h1>
        <p class="hero-meta">Last updated: <?= date('F j, Y') ?> &nbsp;·&nbsp; Effective immediately</p>
    </div>
</section>

<div class="legal-layout">

    <!-- SIDEBAR -->
    <aside class="legal-nav">
        <div class="legal-nav-title">Contents</div>
        <a href="#intro"      class="active">1. Introduction</a>
        <a href="#collect">2. Data We Collect</a>
        <a href="#use">3. How We Use Your Data</a>
        <a href="#ai">4. AI Processing</a>
        <a href="#sharing">5. Sharing Your Data</a>
        <a href="#storage">6. Data Storage & Security</a>
        <a href="#rights">7. Your Rights</a>
        <a href="#cookies">8. Cookies</a>
        <a href="#children">9. Children's Privacy</a>
        <a href="#changes">10. Policy Changes</a>
        <a href="#contact">11. Contact Us</a>
    </aside>

    <!-- CONTENT -->
    <main class="legal-content">

        <div class="policy-highlight">
            <strong>Summary:</strong> PaddyCare collects only what is necessary to provide AI-powered paddy disease detection. We do not sell your data. Your leaf images are processed solely for diagnosis and are not shared with third parties without your consent.
        </div>

        <!-- 1 -->
        <section class="policy-section" id="intro">
            <div class="policy-number">Section 01</div>
            <h2 class="policy-title">Introduction</h2>
            <div class="policy-body">
                <p>PaddyCare ("we", "our", or "us") is an AI-powered paddy disease detection platform built to help Sri Lankan farmers identify and treat crop diseases quickly and accurately. We are committed to protecting your privacy and handling your personal data with transparency.</p>
                <p>This Privacy Policy explains what information we collect, why we collect it, how it is used, and your rights regarding that data. By using PaddyCare, you agree to the practices described in this policy.</p>
            </div>
        </section>
        <hr class="policy-divider">

        <!-- 2 -->
        <section class="policy-section" id="collect">
            <div class="policy-number">Section 02</div>
            <h2 class="policy-title">Data We Collect</h2>
            <div class="policy-body">
                <p>We collect the following types of information:</p>
                <ul>
                    <li><strong>Account information</strong> — your first name, last name, email address, contact number, and password (stored encrypted) when you register.</li>
                    <li><strong>Leaf images</strong> — photos you upload for disease detection. These are processed by our AI model to generate a diagnosis.</li>
                    <li><strong>Community posts</strong> — text, images, and tags you voluntarily share in the community section.</li>
                    <li><strong>Contact messages</strong> — your name, email, subject, and message when you use our contact form.</li>
                    <li><strong>Usage data</strong> — pages visited, features used, and timestamps, collected automatically to improve the platform.</li>
                </ul>
            </div>
        </section>
        <hr class="policy-divider">

        <!-- 3 -->
        <section class="policy-section" id="use">
            <div class="policy-number">Section 03</div>
            <h2 class="policy-title">How We Use Your Data</h2>
            <div class="policy-body">
                <p>Your data is used to:</p>
                <ul>
                    <li>Provide and operate the disease detection service</li>
                    <li>Authenticate your account and maintain session security</li>
                    <li>Display your posts in the community feed</li>
                    <li>Respond to your contact form submissions</li>
                    <li>Improve the accuracy and performance of our AI model (using anonymised image data only)</li>
                    <li>Send important service-related notifications</li>
                </ul>
                <p>We do not use your data for advertising or sell it to third parties.</p>
            </div>
        </section>
        <hr class="policy-divider">

        <!-- 4 -->
        <section class="policy-section" id="ai">
            <div class="policy-number">Section 04</div>
            <h2 class="policy-title">AI Processing of Leaf Images</h2>
            <div class="policy-body">
                <p>When you upload a leaf image, it is sent to our AI detection server (running locally at <code>127.0.0.1:5000</code> during development, or our hosted inference server in production) for analysis. The image is processed to identify potential diseases and return a diagnosis result.</p>
                <p>Images are not permanently stored on our AI server after processing. Diagnosis results may be saved to your account history for your reference.</p>
                <p>We may use anonymised, non-identifiable image data to retrain and improve the AI model over time. No image will be linked to your identity in this process.</p>
            </div>
        </section>
        <hr class="policy-divider">

        <!-- 5 -->
        <section class="policy-section" id="sharing">
            <div class="policy-number">Section 05</div>
            <h2 class="policy-title">Sharing Your Data</h2>
            <div class="policy-body">
                <p>We do not sell, rent, or trade your personal information. We may share data only in the following circumstances:</p>
                <ul>
                    <li><strong>With your consent</strong> — e.g. community posts you choose to make public</li>
                    <li><strong>Service providers</strong> — trusted hosting or infrastructure providers who are contractually bound to protect your data</li>
                    <li><strong>Legal obligations</strong> — if required by law, court order, or government authority</li>
                    <li><strong>Safety</strong> — to protect the rights, property, or safety of PaddyCare, its users, or the public</li>
                </ul>
            </div>
        </section>
        <hr class="policy-divider">

        <!-- 6 -->
        <section class="policy-section" id="storage">
            <div class="policy-number">Section 06</div>
            <h2 class="policy-title">Data Storage &amp; Security</h2>
            <div class="policy-body">
                <p>Your data is stored in a secured MySQL database. Passwords are hashed and never stored in plain text. We use industry-standard practices to protect against unauthorised access, alteration, or disclosure of your data.</p>
                <p>While we take reasonable precautions, no method of internet transmission is 100% secure. We encourage you to use a strong, unique password for your PaddyCare account.</p>
                <p>Data is retained as long as your account is active. You may request deletion at any time (see Section 7).</p>
            </div>
        </section>
        <hr class="policy-divider">

        <!-- 7 -->
        <section class="policy-section" id="rights">
            <div class="policy-number">Section 07</div>
            <h2 class="policy-title">Your Rights</h2>
            <div class="policy-body">
                <p>You have the right to:</p>
                <ul>
                    <li><strong>Access</strong> — request a copy of the personal data we hold about you</li>
                    <li><strong>Correction</strong> — update or correct inaccurate information</li>
                    <li><strong>Deletion</strong> — request that we delete your account and associated data</li>
                    <li><strong>Restriction</strong> — ask us to limit how we process your data</li>
                    <li><strong>Portability</strong> — receive your data in a structured, machine-readable format</li>
                    <li><strong>Withdraw consent</strong> — at any time, for any processing based on consent</li>
                </ul>
                <p>To exercise any of these rights, contact us using the details in Section 11.</p>
            </div>
        </section>
        <hr class="policy-divider">

        <!-- 8 -->
        <section class="policy-section" id="cookies">
            <div class="policy-number">Section 08</div>
            <h2 class="policy-title">Cookies</h2>
            <div class="policy-body">
                <p>PaddyCare uses session cookies to keep you logged in and maintain your preferences during your visit. We do not use third-party advertising or tracking cookies.</p>
                <p>You can disable cookies in your browser settings, though this may affect the functionality of the platform (for example, you will not be able to stay logged in).</p>
            </div>
        </section>
        <hr class="policy-divider">

        <!-- 9 -->
        <section class="policy-section" id="children">
            <div class="policy-number">Section 09</div>
            <h2 class="policy-title">Children's Privacy</h2>
            <div class="policy-body">
                <p>PaddyCare is intended for use by farmers and agricultural professionals. We do not knowingly collect personal information from children under the age of 13. If we become aware that a child under 13 has provided us with personal information, we will delete it promptly.</p>
            </div>
        </section>
        <hr class="policy-divider">

        <!-- 10 -->
        <section class="policy-section" id="changes">
            <div class="policy-number">Section 10</div>
            <h2 class="policy-title">Policy Changes</h2>
            <div class="policy-body">
                <p>We may update this Privacy Policy from time to time to reflect changes in our practices or legal requirements. When we do, we will update the "Last updated" date at the top of this page.</p>
                <p>We encourage you to review this page periodically. Continued use of PaddyCare after changes are posted constitutes your acceptance of the updated policy.</p>
            </div>
        </section>
        <hr class="policy-divider">

        <!-- 11 -->
        <section class="policy-section" id="contact">
            <div class="policy-number">Section 11</div>
            <h2 class="policy-title">Contact Us</h2>
            <div class="policy-body">
                <p>If you have any questions, concerns, or requests regarding this Privacy Policy or your personal data, please reach out to us:</p>
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
// Highlight active section in sidebar as user scrolls
const sections = document.querySelectorAll('.policy-section');
const navLinks  = document.querySelectorAll('.legal-nav a');

const observer = new IntersectionObserver(entries => {
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