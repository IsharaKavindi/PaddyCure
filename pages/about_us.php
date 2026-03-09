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
    <title>About Us – PaddyCare</title>
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
        body { background: var(--white); color: var(--forest); font-family: var(--sans); font-weight: 300; overflow-x: hidden; }

        /* ── HERO ── */
        .hero {
            min-height: 70vh; background: var(--deep);
            position: relative; display: flex; align-items: center; overflow: hidden;
        }
        .hero::before {
            content: ''; position: absolute; inset: 0;
            background-image: repeating-linear-gradient(160deg, rgba(107,158,111,.06) 0, rgba(107,158,111,.06) 1px, transparent 1px, transparent 60px);
        }
        .hero::after {
            content: ''; position: absolute; inset: 0;
            background: radial-gradient(ellipse 65% 65% at 75% 50%, rgba(74,124,89,.25) 0%, transparent 70%);
        }
        .hero-blob { position: absolute; border-radius: 50%; filter: blur(80px); opacity: .15; animation: drift 8s ease-in-out infinite alternate; }
        .hero-blob:nth-child(1){ width:500px;height:500px;background:var(--moss);top:-100px;right:-100px;animation-delay:0s }
        .hero-blob:nth-child(2){ width:280px;height:280px;background:var(--lime);bottom:0;left:5%;animation-delay:3s }
        @keyframes drift { from{transform:translate(0,0)}to{transform:translate(20px,-24px)} }

        .hero-content { position: relative; z-index: 1; padding: 120px 8% 100px; animation: fadeUp 1s ease both; }
        @keyframes fadeUp { from{opacity:0;transform:translateY(28px)}to{opacity:1;transform:translateY(0)} }
        .hero-label {
            font-size: 10px; font-weight: 500; letter-spacing: .28em; text-transform: uppercase;
            color: var(--lime); display: flex; align-items: center; gap: 12px; margin-bottom: 20px;
        }
        .hero-label::before { content:''; display:block; width:28px; height:1px; background:var(--lime) }
        .hero-title { font-family: var(--serif); font-size: clamp(3rem,6vw,5rem); font-weight: 700; color: var(--white); line-height: 1.08; margin-bottom: 20px; }
        .hero-title em { font-style: italic; color: var(--lime); display: block; }
        .hero-sub { font-size: 17px; line-height: 1.75; color: rgba(250,253,247,.6); max-width: 520px; }

        /* ── SECTION SHARED ── */
        .section-label {
            font-size: 10px; font-weight: 500; letter-spacing: .28em; text-transform: uppercase;
            color: var(--sage); display: flex; align-items: center; gap: 12px; margin-bottom: 18px;
        }
        .section-label::before { content:''; display:block; width:28px; height:1px; background:var(--sage) }
        .section-title { font-family: var(--serif); font-size: clamp(2rem,4vw,3rem); font-weight: 400; color: var(--forest); line-height: 1.2; margin-bottom: 16px; }
        .section-title em { font-style: italic; color: var(--sage); }

        /* ── MISSION ── */
        .mission { padding: 110px 8%; background: var(--white); }
        .mission-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }
        .mission-text .section-desc { font-size: 15px; line-height: 1.85; color: #3a5a45; margin-bottom: 16px; }
        .mission-visual {
            background: var(--forest); border-radius: 12px; padding: 52px 44px;
            display: flex; flex-direction: column; gap: 28px; position: relative; overflow: hidden;
        }
        .mission-visual::before {
            content:''; position:absolute; inset:0;
            background: repeating-linear-gradient(90deg, rgba(168,201,127,.04) 0, rgba(168,201,127,.04) 1px, transparent 1px, transparent 60px);
        }
        .mv-stat { position: relative; z-index: 1; }
        .mv-num { font-family: var(--serif); font-size: 3rem; font-weight: 700; color: var(--lime); line-height: 1; margin-bottom: 4px; }
        .mv-label { font-size: 12px; letter-spacing: .1em; text-transform: uppercase; color: rgba(250,253,247,.5); }
        .mv-divider { border: none; border-top: 1px solid rgba(168,201,127,.12); }

        /* ── HOW WE BUILT IT ── */
        .built { padding: 110px 8%; background: var(--straw); }
        .built-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 2px; background: #d8e8c8; margin-top: 64px; }
        .built-card { background: var(--straw); padding: 48px 36px; position: relative; overflow: hidden; transition: background .3s; }
        .built-card:hover { background: var(--white); }
        .built-num { font-family: var(--serif); font-size: 4.5rem; font-weight: 700; color: rgba(74,124,89,.1); line-height:1; position:absolute; top:20px; right:24px; transition: color .3s; }
        .built-card:hover .built-num { color: rgba(74,124,89,.17); }
        .built-icon { width:50px;height:50px;background:var(--forest);border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:24px; }
        .built-icon svg { width:22px;height:22px;stroke:var(--lime);fill:none;stroke-width:1.5;stroke-linecap:round;stroke-linejoin:round }
        .built-title { font-family: var(--serif); font-size: 1.3rem; color: var(--forest); margin-bottom: 12px; }
        .built-desc { font-size: 14px; line-height: 1.72; color: #5a7a60; }

        /* ── TEAM ── */
        .team { padding: 110px 8%; background: var(--white); }
        .team-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px,1fr)); gap: 24px; margin-top: 64px; }
        .team-card {
            background: var(--white); border: 1px solid #e0ead8; border-radius: 10px;
            overflow: hidden; transition: box-shadow .25s, transform .25s;
        }
        .team-card:hover { box-shadow: 0 10px 40px rgba(27,58,45,.1); transform: translateY(-4px); }
        .team-avatar {
            width: 100%; height: 200px; object-fit: cover; display: block;
            background: linear-gradient(135deg, #d4e6c3, #e8f0d8);
        }
        .team-avatar-placeholder {
            width: 100%; height: 200px;
            background: linear-gradient(135deg, var(--forest), var(--sage));
            display: flex; align-items: center; justify-content: center;
            font-family: var(--serif); font-size: 3rem; color: var(--lime); font-weight: 700;
        }
        .team-info { padding: 22px 22px 26px; }
        .team-name { font-family: var(--serif); font-size: 1.15rem; color: var(--forest); margin-bottom: 4px; }
        .team-role {
            font-size: 10px; font-weight: 500; letter-spacing: .14em; text-transform: uppercase;
            color: var(--sage); margin-bottom: 10px;
        }
        .team-bio { font-size: 13px; line-height: 1.65; color: #5a7a60; }

        /* ── VALUES ── */
        .values { padding: 110px 8%; background: var(--forest); position: relative; overflow: hidden; }
        .values::before {
            content:''; position:absolute; inset:0;
            background-image: repeating-linear-gradient(90deg, rgba(168,201,127,.04) 0, rgba(168,201,127,.04) 1px, transparent 1px, transparent 80px);
        }
        .values .section-label { color: var(--lime); }
        .values .section-label::before { background: var(--lime); }
        .values .section-title { color: var(--white); }
        .values .section-title em { color: var(--lime); }
        .values-grid { display: grid; grid-template-columns: repeat(auto-fill,minmax(260px,1fr)); gap: 16px; margin-top: 56px; position: relative; }
        .value-card {
            background: rgba(250,253,247,.05); border: 1px solid rgba(168,201,127,.12);
            border-radius: 8px; padding: 30px 26px; transition: background .3s, border-color .3s, transform .25s;
        }
        .value-card:hover { background: rgba(168,201,127,.1); border-color: rgba(168,201,127,.3); transform: translateY(-5px); }
        .value-icon { font-size: 2rem; margin-bottom: 14px; }
        .value-title { font-family: var(--serif); font-size: 1.1rem; color: var(--white); margin-bottom: 8px; }
        .value-desc { font-size: 13px; line-height: 1.65; color: rgba(250,253,247,.5); }

        /* ── CTA ── */
        .cta-strip {
            padding: 90px 8%; background: var(--lime);
            display: flex; align-items: center; justify-content: space-between; gap: 32px; flex-wrap: wrap;
        }
        .cta-title { font-family: var(--serif); font-size: clamp(1.8rem,3.5vw,2.8rem); font-weight: 700; color: var(--deep); line-height: 1.2; margin-bottom: 8px; }
        .cta-sub { font-size: 15px; color: rgba(18,42,32,.65); }
        .btn-dark {
            display: inline-flex; align-items: center; gap: 10px;
            background: var(--deep); color: var(--lime); padding: 15px 32px;
            font-family: var(--sans); font-size: 13px; font-weight: 500; letter-spacing: .1em;
            text-decoration: none; border-radius: 4px; white-space: nowrap;
            transition: background .25s, transform .2s;
        }
        .btn-dark:hover { background: var(--forest); transform: translateY(-2px); }

        @media(max-width:900px){
            .mission-grid { grid-template-columns: 1fr; }
            .built-grid { grid-template-columns: 1fr; }
        }
        @media(max-width:600px){
            .cta-strip { flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>

<?php include('../components/header_navigation_bar.php'); ?>

<!-- ── HERO ── -->
<section class="hero">
    <div class="hero-blob"></div>
    <div class="hero-blob"></div>
    <div class="hero-content">
        <p class="hero-label">Our Story</p>
        <h1 class="hero-title">
            Built for Sri Lanka's
            <em>Paddy Farmers.</em>
        </h1>
        <p class="hero-sub">
            PaddyCare was created with one mission — to put the power of AI in the hands of every farmer, making crop disease detection fast, accurate, and accessible to all.
        </p>
    </div>
</section>

<!-- ── MISSION ── -->
<section class="mission">
    <div class="mission-grid">
        <div class="mission-text">
            <p class="section-label">Our Mission</p>
            <h2 class="section-title">Protecting Harvests with <em>Intelligence</em></h2>
            <p class="section-desc">
                Rice is the backbone of Sri Lankan agriculture. Yet each year, thousands of acres of paddy are lost to preventable diseases — simply because farmers lack the tools to identify them early enough.
            </p>
            <p class="section-desc">
                PaddyCare bridges that gap. By combining deep learning with an accessible web platform, we give farmers the ability to photograph an affected leaf, receive an instant diagnosis, and act before the damage spreads.
            </p>
            <p class="section-desc">
                We believe technology should serve the people who feed the nation. PaddyCare is free to use, designed for low-bandwidth environments, and built with the Sri Lankan farmer in mind.
            </p>
        </div>
        <div class="mission-visual">
            <div class="mv-stat">
                <div class="mv-num">95%</div>
                <div class="mv-label">AI Detection Accuracy</div>
            </div>
            <hr class="mv-divider">
            <div class="mv-stat">
                <div class="mv-num">10+</div>
                <div class="mv-label">Paddy Diseases Detected</div>
            </div>
            <hr class="mv-divider">
            <div class="mv-stat">
                <div class="mv-num">&lt;5s</div>
                <div class="mv-label">Average Diagnosis Time</div>
            </div>
            <hr class="mv-divider">
            <div class="mv-stat">
                <div class="mv-num">Free</div>
                <div class="mv-label">For All Farmers</div>
            </div>
        </div>
    </div>
</section>

<!-- ── HOW WE BUILT IT ── -->
<section class="built">
    <p class="section-label">Technology</p>
    <h2 class="section-title">How We <em>Built</em> PaddyCare</h2>
    <div class="built-grid">
        <div class="built-card">
            <span class="built-num">01</span>
            <div class="built-icon">
                <svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
            </div>
            <h3 class="built-title">Deep Learning Model</h3>
            <p class="built-desc">Our AI model was trained on thousands of annotated paddy leaf images covering the most common diseases found in Sri Lankan and South Asian paddy fields. It runs on a Python Flask server.</p>
        </div>
        <div class="built-card">
            <span class="built-num">02</span>
            <div class="built-icon">
                <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            </div>
            <h3 class="built-title">Simple Image Upload</h3>
            <p class="built-desc">Farmers simply photograph an affected leaf with any smartphone. No special equipment required. The photo is submitted through our web interface and results return in under 5 seconds.</p>
        </div>
        <div class="built-card">
            <span class="built-num">03</span>
            <div class="built-icon">
                <svg viewBox="0 0 24 24"><path d="M9 12l2 2 4-4"/><path d="M21 12c0 4.97-4.03 9-9 9S3 16.97 3 12 7.03 3 12 3s9 4.03 9 9z"/></svg>
            </div>
            <h3 class="built-title">Actionable Diagnosis</h3>
            <p class="built-desc">Results include the disease name, confidence level, and recommended treatment steps — giving farmers clear, actionable guidance to protect their crop immediately.</p>
        </div>
    </div>
</section>

<!-- ── TEAM ── -->
<section class="team">
    <p class="section-label">The People</p>
    <h2 class="section-title">Meet the <em>Team</em></h2>
    <div class="team-grid">

        <div class="team-card">
            <div class="team-avatar-placeholder">K</div>
            <div class="team-info">
                <div class="team-name">Kamal Silva</div>
                <div class="team-role">Project Lead & Full Stack Developer</div>
                <p class="team-bio">Designed and built the PaddyCare platform, integrating the AI backend with the PHP web interface.</p>
            </div>
        </div>

        <div class="team-card">
            <div class="team-avatar-placeholder">A</div>
            <div class="team-info">
                <div class="team-name">Amara Perera</div>
                <div class="team-role">AI / ML Engineer</div>
                <p class="team-bio">Developed and trained the deep learning model responsible for paddy disease classification.</p>
            </div>
        </div>

        <div class="team-card">
            <div class="team-avatar-placeholder">N</div>
            <div class="team-info">
                <div class="team-name">Nimal Fernando</div>
                <div class="team-role">Agricultural Advisor</div>
                <p class="team-bio">Provided domain expertise on paddy diseases, treatment protocols, and Sri Lankan farming practices.</p>
            </div>
        </div>

        <div class="team-card">
            <div class="team-avatar-placeholder">S</div>
            <div class="team-info">
                <div class="team-name">Sithumi Jayawardena</div>
                <div class="team-role">UI / UX Designer</div>
                <p class="team-bio">Crafted the user experience to ensure PaddyCare is intuitive and accessible for farmers of all backgrounds.</p>
            </div>
        </div>

    </div>
</section>

<!-- ── VALUES ── -->
<section class="values">
    <p class="section-label">What We Stand For</p>
    <h2 class="section-title">Our <em>Values</em></h2>
    <div class="values-grid">
        <div class="value-card">
            <div class="value-icon">🌾</div>
            <h3 class="value-title">Farmer First</h3>
            <p class="value-desc">Every decision we make starts with the farmer. Accessibility, simplicity, and usefulness in the field are non-negotiable.</p>
        </div>
        <div class="value-card">
            <div class="value-icon">🔬</div>
            <h3 class="value-title">Scientific Accuracy</h3>
            <p class="value-desc">We validate our AI results against agricultural research and continuously improve our model with real-world field data.</p>
        </div>
        <div class="value-card">
            <div class="value-icon">🤝</div>
            <h3 class="value-title">Community Knowledge</h3>
            <p class="value-desc">The community feed lets farmers share experiences, ask questions, and learn from each other — knowledge shared is knowledge multiplied.</p>
        </div>
        <div class="value-card">
            <div class="value-icon">🔒</div>
            <h3 class="value-title">Data Privacy</h3>
            <p class="value-desc">Your images and personal information are yours. We do not sell data or share it without consent. See our Privacy Policy for full details.</p>
        </div>
    </div>
</section>

<!-- ── CTA ── -->
<section class="cta-strip">
    <div>
        <h2 class="cta-title">Ready to protect your harvest?</h2>
        <p class="cta-sub">Join farmers across Sri Lanka using PaddyCare to detect diseases early.</p>
    </div>
    <?php if (!isset($_SESSION['user_id'])): ?>
        <a class="btn-dark" href="<?= BASE_URL ?>/pages/auth/register.php">
            Get Started Free
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
    <?php else: ?>
        <a class="btn-dark" href="http://127.0.0.1:5000">
            Scan a Leaf Now
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0018 9h-1.26A8 8 0 103 16.3"/></svg>
        </a>
    <?php endif; ?>
</section>

<?php include('../components/footer.php'); ?>
</body>
</html>