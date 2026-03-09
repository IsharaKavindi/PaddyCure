<?php
require('config.php');
require('./utils/database.php');
session_start();

$conn = initialize_database();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['logged_out']) && $_GET['logged_out'] == 'true') {
        session_destroy();
        header('Location: ' . BASE_URL . '/index.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>PaddyCure – AI Paddy Disease Detection</title>
<link rel="stylesheet" href="public/styles/styles.css">
<link rel="stylesheet" href="public/styles/home.css">
<link rel="stylesheet" href="public/styles/fonts.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="shortcut icon" href="<?php echo BASE_URL; ?>/public/images/paddyCureLogo2.png" type="image/x-icon">

<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --forest:     #1B3A2D;
  --deep:       #122A20;
  --sage:       #4A7C59;
  --moss:       #6B9E6F;
  --lime:       #A8C97F;
  --wheat:      #E8D5A3;
  --straw:      #F5EDD3;
  --white:      #FAFDF7;
  --muted:      #8BAF8E;
  --serif:      'Playfair Display', Georgia, serif;
  --sans:       'DM Sans', sans-serif;
}

html { scroll-behavior: smooth; }

body {
  background: var(--white);
  color: var(--forest);
  font-family: var(--sans);
  font-weight: 300;
  overflow-x: hidden;
}

/* ═══════════════════════════════════
   HERO
═══════════════════════════════════ */
.hero {
  min-height: 90vh;
  background-color: var(--deep);
  position: relative;
  display: flex;
  align-items: center;
  overflow: hidden;
}

/* animated rice-field SVG background */
.hero-bg {
  position: absolute;
  inset: 0;
  z-index: 0;
  overflow: hidden;
}

/* diagonal crop rows */
.hero-bg::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image:
    repeating-linear-gradient(
      160deg,
      rgba(107,158,111,0.06) 0px,
      rgba(107,158,111,0.06) 1px,
      transparent 1px,
      transparent 60px
    );
}

.hero-bg::after {
  content: '';
  position: absolute;
  inset: 0;
  background:
    radial-gradient(ellipse 70% 60% at 70% 50%, rgba(74,124,89,0.22) 0%, transparent 70%),
    radial-gradient(ellipse 40% 50% at 20% 80%, rgba(168,201,127,0.1) 0%, transparent 60%);
}

/* floating grain blobs */
.grain-blob {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  opacity: 0.18;
  animation: drift 8s ease-in-out infinite alternate;
}
.grain-blob:nth-child(1){ width:500px;height:500px;background:var(--moss);top:-100px;right:-100px;animation-delay:0s; }
.grain-blob:nth-child(2){ width:300px;height:300px;background:var(--lime);bottom:0;left:10%;animation-delay:3s; }
.grain-blob:nth-child(3){ width:200px;height:200px;background:var(--wheat);top:30%;left:50%;animation-delay:5s; }

@keyframes drift {
  from { transform: translate(0,0) scale(1); }
  to   { transform: translate(20px, -30px) scale(1.05); }
}

/* paddy stalk illustration (pure CSS) */
.stalk-art {
  position: absolute;
  right: 5%;
  bottom: 0;
  width: 420px;
  height: 100%;
  z-index: 1;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  pointer-events: none;
}

.stalk-art svg {
  width: 100%;
  height: 85%;
  opacity: 0.22;
  filter: drop-shadow(0 0 30px rgba(168,201,127,0.3));
}

.hero-content {
  position:absolute;
	inset:0;
  z-index: 2;
  padding: 120px 8% 100px;
  max-width: 720px;
  animation: heroIn 1s ease both;
	max-width:1500px;
}

@keyframes heroIn {
  from { opacity: 0; transform: translateY(30px); }
  to   { opacity: 1; transform: translateY(0); }
}

.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(168,201,127,0.15);
  border: 1px solid rgba(168,201,127,0.3);
  border-radius: 100px;
  padding: 6px 16px 6px 8px;
  margin-bottom: 32px;
}

.badge-dot {
  width: 8px; height: 8px;
  border-radius: 50%;
  background: var(--lime);
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%,100%{ box-shadow: 0 0 0 0 rgba(168,201,127,0.6); }
  50%    { box-shadow: 0 0 0 6px rgba(168,201,127,0); }
}

.badge-text {
  font-size: 12px;
  font-weight: 500;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--lime);
}

.hero-title {
  font-family: var(--serif);
  font-size: clamp(3rem, 6vw, 5.2rem);
  font-weight: 700;
  line-height: 1.08;
  color: var(--white);
  margin-bottom: 24px;
	max-width: 900px;
}

.hero-title em {
  font-style: italic;
  color: var(--lime);
  display: block;
}

.hero-subtitle {
  font-size: 17px;
  line-height: 1.75;
  color: rgba(250,253,247,0.62);
  max-width: 480px;
  margin-bottom: 48px;
	max-width: 900px;
}

.hero-actions {
  display: flex;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
}

.btn-primary-green {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: var(--lime);
  color: var(--deep);
  padding: 15px 34px;
  font-family: var(--sans);
  font-size: 13px;
  font-weight: 500;
  letter-spacing: 0.08em;
  text-decoration: none;
  border-radius: 4px;
  transition: background 0.25s, transform 0.2s, gap 0.2s;
}
.btn-primary-green:hover {
  background: #bcd98e;
  transform: translateY(-2px);
  gap: 16px;
}

.btn-ghost {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: rgba(250,253,247,0.7);
  font-size: 14px;
  text-decoration: none;
  border-bottom: 1px solid rgba(250,253,247,0.2);
  padding-bottom: 2px;
  transition: color 0.2s, border-color 0.2s;
}
.btn-ghost:hover { color: var(--white); border-color: var(--lime); }

.hero-stats {
  position: absolute;
  bottom: 48px;
  left: 9%;
	bottom:10%;
  z-index: 2;
  display: flex;
  gap: 48px;
  animation: heroIn 1s ease 0.3s both;
}

.stat-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.stat-number {
  font-family: var(--serif);
  font-size: 2rem;
  font-weight: 700;
  color: var(--white);
  line-height: 1;
}

.stat-label {
  font-size: 11px;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: var(--muted);
}

.stat-divider {
  width: 1px;
  background: rgba(250,253,247,0.12);
  align-self: stretch;
}

/* ═══════════════════════════════════
   HOW IT WORKS
═══════════════════════════════════ */
.how-it-works {
  padding: 120px 8%;
  background: var(--white);
}

.section-label {
  font-size: 10px;
  font-weight: 500;
  letter-spacing: 0.28em;
  text-transform: uppercase;
  color: var(--sage);
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 20px;
}
.section-label::before {
  content: '';
  display: block;
  width: 28px; height: 1px;
  background: var(--sage);
}

.section-title {
  font-family: var(--serif);
  font-size: clamp(2.2rem, 4vw, 3.2rem);
  font-weight: 400;
  line-height: 1.2;
  color: var(--forest);
  margin-bottom: 16px;
}

.section-title em { font-style: italic; color: var(--sage); }

.section-desc {
  font-size: 16px;
  line-height: 1.75;
  color: #5a7a60;
  max-width: 540px;
  margin-bottom: 72px;
}

.steps-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 2px;
  background: #e0ead8;
}

.step-card {
  background: var(--white);
  padding: 52px 40px;
  position: relative;
  overflow: hidden;
  transition: background 0.3s;
}

.step-card:hover { background: var(--straw); }

.step-number {
  font-family: var(--serif);
  font-size: 5rem;
  font-weight: 700;
  color: rgba(74,124,89,0.1);
  line-height: 1;
  position: absolute;
  top: 24px;
  right: 28px;
  transition: color 0.3s;
}

.step-card:hover .step-number { color: rgba(74,124,89,0.18); }

.step-icon {
  width: 52px; height: 52px;
  background: var(--forest);
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  margin-bottom: 28px;
}

.step-icon svg {
  width: 24px; height: 24px;
  stroke: var(--lime);
  fill: none;
  stroke-width: 1.5;
  stroke-linecap: round; stroke-linejoin: round;
}

.step-title {
  font-family: var(--serif);
  font-size: 1.4rem;
  font-weight: 400;
  color: var(--forest);
  margin-bottom: 14px;
}

.step-desc {
  font-size: 14px;
  line-height: 1.7;
  color: #5a7a60;
}

/* ═══════════════════════════════════
   DISEASES SECTION
═══════════════════════════════════ */
.diseases-section {
  padding: 120px 8%;
  background: var(--forest);
  position: relative;
  overflow: hidden;
}

.diseases-section::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image: repeating-linear-gradient(
    90deg,
    rgba(168,201,127,0.04) 0px,
    rgba(168,201,127,0.04) 1px,
    transparent 1px,
    transparent 80px
  );
}

.diseases-header {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 32px;
  margin-bottom: 64px;
  position: relative;
}

.diseases-header .section-label { color: var(--lime); }
.diseases-header .section-label::before { background: var(--lime); }
.diseases-header .section-title { color: var(--white); margin-bottom: 0; }
.diseases-header .section-title em { color: var(--lime); }
.diseases-header .section-desc { color: rgba(250,253,247,0.55); margin-bottom: 0; }

.disease-cards {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  position: relative;
}

.disease-card {
  background: rgba(250,253,247,0.05);
  border: 1px solid rgba(168,201,127,0.12);
  border-radius: 8px;
  padding: 32px 26px;
  transition: background 0.3s, border-color 0.3s, transform 0.25s;
  cursor: default;
}

.disease-card:hover {
  background: rgba(168,201,127,0.1);
  border-color: rgba(168,201,127,0.35);
  transform: translateY(-6px);
}

.disease-severity {
  display: inline-block;
  font-size: 9px;
  font-weight: 500;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  padding: 4px 10px;
  border-radius: 100px;
  margin-bottom: 20px;
}
.sev-high   { background: rgba(220,80,60,0.2);  color: #ff9980; border: 1px solid rgba(220,80,60,0.3); }
.sev-med    { background: rgba(220,160,40,0.2); color: #ffd080; border: 1px solid rgba(220,160,40,0.3); }
.sev-low    { background: rgba(100,180,100,0.2);color: var(--lime); border: 1px solid rgba(100,180,100,0.3); }

.disease-icon {
  font-size: 2.2rem;
  margin-bottom: 16px;
}

.disease-name {
  font-family: var(--serif);
  font-size: 1.15rem;
  color: var(--white);
  margin-bottom: 10px;
}

.disease-desc {
  font-size: 13px;
  line-height: 1.65;
  color: rgba(250,253,247,0.5);
}

/* ═══════════════════════════════════
   PROMOTIONS / ALERTS SECTION
═══════════════════════════════════ */
.alerts-section {
  padding: 120px 8%;
  background: var(--straw);
}

.alerts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 24px;
  margin-top: 64px;
}

.alert-card {
  background: var(--white);
  border-radius: 10px;
  padding: 36px 32px;
  position: relative;
  overflow: hidden;
  border-top: 3px solid var(--sage);
  box-shadow: 0 4px 24px rgba(27,58,45,0.07);
  transition: transform 0.25s, box-shadow 0.25s;
}

.alert-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 40px rgba(27,58,45,0.13);
}

.alert-discount-badge {
  position: absolute;
  top: 28px; right: 28px;
  background: var(--forest);
  color: var(--lime);
  font-family: var(--serif);
  font-size: 1.3rem;
  font-weight: 700;
  padding: 8px 16px;
  border-radius: 6px;
}

.alert-icon {
  width: 44px; height: 44px;
  background: rgba(74,124,89,0.1);
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  margin-bottom: 20px;
}

.alert-icon svg {
  width: 20px; height: 20px;
  stroke: var(--sage);
  fill: none;
  stroke-width: 1.5;
  stroke-linecap: round; stroke-linejoin: round;
}

.alert-name {
  font-family: var(--serif);
  font-size: 1.35rem;
  color: var(--forest);
  margin-bottom: 10px;
  padding-right: 80px;
}

.alert-desc {
  font-size: 14px;
  line-height: 1.68;
  color: #5a7a60;
  margin-bottom: 24px;
}

.alert-dates {
  display: flex;
  flex-direction: column;
  gap: 6px;
  padding-top: 20px;
  border-top: 1px solid #e8f0e4;
}

.alert-date {
  font-size: 12px;
  color: var(--muted);
  letter-spacing: 0.04em;
}

/* ═══════════════════════════════════
   FEATURED TOOLS (was menu items)
═══════════════════════════════════ */
.tools-section {
  padding: 120px 8%;
  background: var(--white);
}

.tools-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 24px;
  margin-top: 64px;
}

/* ═══════════════════════════════════
   CTA STRIP
═══════════════════════════════════ */
.cta-strip {
  padding: 100px 8%;
  background: var(--lime);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 40px;
  flex-wrap: wrap;
}

.cta-text .cta-title {
  font-family: var(--serif);
  font-size: clamp(2rem, 4vw, 3rem);
  font-weight: 700;
  color: var(--deep);
  line-height: 1.2;
  margin-bottom: 10px;
}

.cta-text .cta-sub {
  font-size: 16px;
  color: rgba(18,42,32,0.7);
}

.btn-dark {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: var(--deep);
  color: var(--lime);
  padding: 16px 36px;
  font-family: var(--sans);
  font-size: 13px;
  font-weight: 500;
  letter-spacing: 0.1em;
  text-decoration: none;
  border-radius: 4px;
  transition: background 0.25s, transform 0.2s;
  white-space: nowrap;
}
.btn-dark:hover { background: var(--forest); transform: translateY(-2px); }

.view-all-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: transparent;
    border: 1.5px solid var(--lime);
    color: var(--lime);
    padding: 14px 32px;
    border-radius: 4px;
    font-family: var(--sans);
    font-size: 13px;
    font-weight: 500;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    text-decoration: none;
    transition: background 0.2s, color 0.2s, transform 0.2s;
}
.view-all-btn:hover {
    background: var(--lime);
    color: var(--deep);
    transform: translateY(-2px);
}
.view-all-btn svg {
    transition: transform 0.2s;
}
.view-all-btn:hover svg {
    transform: translateX(4px);
}

/* ═══════════════════════════════════
   RESPONSIVE
═══════════════════════════════════ */
@media (max-width: 1024px) {
  .disease-cards { grid-template-columns: repeat(2, 1fr); }
  .diseases-header { flex-direction: column; align-items: flex-start; }
}

@media (max-width: 768px) {
  .steps-grid { grid-template-columns: 1fr; gap: 2px; }
  .hero-stats { flex-wrap: wrap; gap: 28px; position: static; margin-top: 48px; }
  .disease-cards { grid-template-columns: 1fr; }
  .stalk-art { display: none; }
  .cta-strip { flex-direction: column; text-align: center; }
}
</style>
</head>
<body>

<?php require('components/header_navigation_bar.php'); ?>

<!-- ═══ HERO ═══ -->
<section class="hero">
  <div class="hero-bg">
    <div class="grain-blob"></div>
    <div class="grain-blob"></div>
    <div class="grain-blob"></div>
  </div>

  <!-- decorative paddy stalk SVG -->
  <div class="stalk-art">
    <svg viewBox="0 0 300 600" fill="none" xmlns="http://www.w3.org/2000/svg">
      <!-- main stalk -->
      <path d="M150 580 C148 400 155 200 150 20" stroke="#A8C97F" stroke-width="3" stroke-linecap="round"/>
      <!-- grain clusters -->
      <g opacity="0.9">
        <ellipse cx="150" cy="60" rx="6" ry="14" fill="#A8C97F" transform="rotate(-15 150 60)"/>
        <ellipse cx="164" cy="72" rx="5" ry="12" fill="#A8C97F" transform="rotate(10 164 72)"/>
        <ellipse cx="138" cy="75" rx="5" ry="12" fill="#A8C97F" transform="rotate(-30 138 75)"/>
        <ellipse cx="170" cy="88" rx="4" ry="11" fill="#A8C97F" transform="rotate(25 170 88)"/>
        <ellipse cx="132" cy="90" rx="4" ry="11" fill="#A8C97F" transform="rotate(-40 132 90)"/>
        <ellipse cx="155" cy="95" rx="5" ry="12" fill="#A8C97F" transform="rotate(5 155 95)"/>
        <ellipse cx="145" cy="100" rx="4" ry="11" fill="#A8C97F" transform="rotate(-20 145 100)"/>
      </g>
      <!-- leaves -->
      <path d="M150 200 Q190 170 210 130" stroke="#6B9E6F" stroke-width="2.5" stroke-linecap="round" fill="none"/>
      <path d="M150 280 Q110 250 90 200" stroke="#6B9E6F" stroke-width="2.5" stroke-linecap="round" fill="none"/>
      <path d="M150 360 Q185 330 200 295" stroke="#6B9E6F" stroke-width="2" stroke-linecap="round" fill="none"/>
      <path d="M150 420 Q118 395 105 360" stroke="#6B9E6F" stroke-width="2" stroke-linecap="round" fill="none"/>
    </svg>
  </div>

  <div class="hero-content">
    <div class="hero-badge">
      <span class="badge-dot"></span>
      <span class="badge-text">AI-Powered Agricultural Intelligence</span>
    </div>

    <h1 class="hero-title">
  		Detect Paddy Diseases
  		<em>Instantly.</em>
	</h1>

    <p class="hero-subtitle">
      Upload a photo and get instant, accurate diagnosis powered by advanced AI.
      Save your crop with personalized treatment recommendations before it's too late.
    </p>

    <div class="hero-actions">
      <?php if (!isset($_SESSION['user_id'])): ?>
        <a class="btn-primary-green" href="./pages/auth/register.php">
          Get Started Free
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
        <a class="btn-ghost" href="./pages/auth/login.php">Already have an account? Sign in</a>
      <?php else: ?>
        <button class="btn-primary-green"  onclick="window.location.href='http://127.0.0.1:5000'">
          Upload Your Image
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0018 9h-1.26A8 8 0 103 16.3"/></svg>
			</button>
      <?php endif; ?>
    </div>

    <div class="hero-stats">
      <div class="stat-item">
        <span class="stat-number">95%</span>
        <span class="stat-label">Accuracy Rate</span>
      </div>
      <div class="stat-divider"></div>
      <div class="stat-item">
        <span class="stat-number">10+</span>
        <span class="stat-label">Diseases Detected</span>
      </div>
      <div class="stat-divider"></div>
      <div class="stat-item">
        <span class="stat-number">&lt;5s</span>
        <span class="stat-label">Diagnosis Time</span>
      </div>
    </div>
  </div>
</section>

<!-- ═══ HOW IT WORKS ═══ -->
<section class="how-it-works">
  <p class="section-label">Simple Process</p>
  <h2 class="section-title">How <em>PaddyCare</em> Works</h2>
  <p class="section-desc">
    Three simple steps stand between you and a healthy harvest.
    No expertise required — our AI does the heavy lifting.
  </p>

  <div class="steps-grid">
    <div class="step-card">
      <span class="step-number">01</span>
      <div class="step-icon">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
      </div>
      <h3 class="step-title">Photograph the Leaf</h3>
      <p class="step-desc">Take a clear photo of the affected paddy leaf using any smartphone camera. Natural light works best.</p>
    </div>

    <div class="step-card">
      <span class="step-number">02</span>
      <div class="step-icon">
        <svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
      </div>
      <h3 class="step-title">AI Analyzes Instantly</h3>
      <p class="step-desc">Our deep learning model scans visual patterns, comparing against thousands of verified disease samples.</p>
    </div>

    <div class="step-card">
      <span class="step-number">03</span>
      <div class="step-icon">
        <svg viewBox="0 0 24 24"><path d="M9 12l2 2 4-4"/><path d="M21 12c0 4.97-4.03 9-9 9S3 16.97 3 12 7.03 3 12 3s9 4.03 9 9z"/></svg>
      </div>
      <h3 class="step-title">Get Treatment Plan</h3>
      <p class="step-desc">Receive a precise diagnosis with actionable treatment steps, pesticide recommendations, and prevention tips.</p>
    </div>
  </div>
</section>

<!-- ═══ DETECTABLE DISEASES ═══ -->
<section class="diseases-section">
  <div class="diseases-header">
    <div>
      <p class="section-label">Coverage</p>
      <h2 class="section-title">Diseases We <em>Detect</em></h2>
    </div>
  </div>

  <div class="disease-cards">
    <div class="disease-card">
      <span class="disease-severity sev-high">High Risk</span>
      <div class="disease-icon">🍃</div>
      <h3 class="disease-name">Blast Disease</h3>
      <p class="disease-desc">Caused by <em>Magnaporthe oryzae</em>. Affects leaves, nodes, and panicles. One of the most destructive rice diseases globally.</p>
    </div>

    <div class="disease-card">
      <span class="disease-severity sev-high">High Risk</span>
      <div class="disease-icon">🌾</div>
      <h3 class="disease-name">Bacterial Blight</h3>
      <p class="disease-desc">Water-soaked lesions that turn yellow. Spreads rapidly in warm, humid conditions and can devastate entire fields.</p>
    </div>

    <div class="disease-card">
      <span class="disease-severity sev-med">Medium Risk</span>
      <div class="disease-icon">🟤</div>
      <h3 class="disease-name">Brown Spot</h3>
      <p class="disease-desc">Circular brown lesions on leaves and grains. Closely tied to nutrient deficiency, especially potassium.</p>
    </div>

    <div class="disease-card">
      <span class="disease-severity sev-low">Low Risk</span>
      <div class="disease-icon">🟡</div>
      <h3 class="disease-name">Tungro Virus</h3>
      <p class="disease-desc">Transmitted by green leafhoppers. Causes yellowing and stunting. Early detection is key to preventing spread.</p>
    </div>
  </div>
<div  href="<?php echo BASE_URL; ?>/index.php" style="text-align:center; margin-top: 48px;">
    <a href="<?= BASE_URL ?>/pages/diseases.php" class="view-all-btn">
        View All Diseases
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
    </a>
</div>
</section>

<!-- ═══ CTA ═══ -->
<section class="cta-strip">
  <div class="cta-text">
    <h2 class="cta-title">Protect your harvest<br>starting today.</h2>
    <p class="cta-sub">Join & save your crops.</p>
  </div>

  <?php if (!isset($_SESSION['user_id'])): ?>
    <a class="btn-dark" href="./pages/auth/register.php">
      Create Free Account
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
    </a>
  <?php else: ?>
    <button class="btn-dark"  onclick="window.location.href='http://127.0.0.1:5000'">
      Scan a Leaf Now
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0018 9h-1.26A8 8 0 103 16.3"/></svg>
	  </button>
  <?php endif; ?>
</section>

<?php require('components/footer.php'); ?>
</body>
</html>