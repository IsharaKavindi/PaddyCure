<?php
require('../config.php');
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Paddy Diseases – PaddyCare</title>
    <link rel="stylesheet" href="../public/styles/styles.css">
    <link rel="stylesheet" href="../public/styles/fonts.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="<?= BASE_URL ?>/public/images/paddyCureLogo2.png" type="image/x-icon">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --forest: #1B3A2D; --deep: #122A20; --sage: #4A7C59; --moss: #6B9E6F;
            --lime: #A8C97F; --white: #FAFDF7; --muted: #8BAF8E;
            --serif: 'Playfair Display', Georgia, serif;
            --sans: 'DM Sans', sans-serif;
        }
        body { background: var(--white); color: var(--forest); font-family: var(--sans); font-weight: 300; }

        /* ── HERO ── */
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
        .hero-inner { position: relative; z-index: 1; display: flex; align-items: flex-end; justify-content: space-between; flex-wrap: wrap; gap: 24px; }
        .hero-left {}
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
        .hero-desc { font-size: 15px; color: rgba(250,253,247,.55); max-width: 480px; line-height: 1.7; }
        .hero-stat-row { display: flex; gap: 32px; margin-top: 36px; }
        .hero-stat { text-align: center; }
        .hero-stat .num { font-family: var(--serif); font-size: 2rem; color: var(--lime); font-weight: 700; line-height: 1; }
        .hero-stat .lbl { font-size: 11px; color: rgba(250,253,247,.4); letter-spacing: .1em; text-transform: uppercase; margin-top: 4px; }

        /* ── BACK BUTTON ── */
        .back-btn {
            display: inline-flex; align-items: center; gap: 8px;
            color: rgba(250,253,247,.6); font-size: 13px; font-weight: 500;
            letter-spacing: .06em; text-transform: uppercase; text-decoration: none;
            border: 1px solid rgba(250,253,247,.15); padding: 10px 20px;
            border-radius: 4px; transition: color .2s, border-color .2s, background .2s;
            align-self: flex-start;
        }
        .back-btn:hover { color: var(--lime); border-color: var(--lime); background: rgba(168,201,127,.08); }
        .back-btn svg { transition: transform .2s; }
        .back-btn:hover svg { transform: translateX(-3px); }

        /* ── FILTER BAR ── */
        .filter-bar {
            background: var(--forest); padding: 0 8%;
            display: flex; align-items: center; gap: 8px; overflow-x: auto;
            border-bottom: 1px solid rgba(168,201,127,.1);
        }
        .filter-bar::-webkit-scrollbar { display: none; }
        .filter-btn {
            flex-shrink: 0; background: transparent; border: none; cursor: pointer;
            font-family: var(--sans); font-size: 12px; font-weight: 500; letter-spacing: .08em;
            text-transform: uppercase; color: rgba(250,253,247,.4);
            padding: 16px 18px; border-bottom: 2px solid transparent;
            transition: color .2s, border-color .2s;
        }
        .filter-btn:hover { color: rgba(250,253,247,.8); }
        .filter-btn.active { color: var(--lime); border-bottom-color: var(--lime); }

        /* ── MAIN LAYOUT ── */
        .diseases-wrap { max-width: 1200px; margin: 0 auto; padding: 64px 8% 100px; }

        /* ── SEVERITY LEGEND ── */
        .legend { display: flex; gap: 20px; margin-bottom: 40px; flex-wrap: wrap; }
        .legend-item { display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--muted); letter-spacing: .04em; }
        .legend-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
        .dot-high { background: #ef4444; }
        .dot-med  { background: #f59e0b; }
        .dot-low  { background: var(--lime); }

        /* ── GRID ── */
        .disease-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
        }

        /* ── DISEASE CARD ── */
        .d-card {
            background: #fff; border: 1px solid #e8f0e0; border-radius: 12px;
            padding: 28px; position: relative; overflow: hidden;
            transition: transform .25s, box-shadow .25s, border-color .25s;
            cursor: pointer;
        }
        .d-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(27,58,45,.12); border-color: var(--lime); }
        .d-card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
        }
        .d-card.high::before { background: linear-gradient(90deg, #ef4444, #f87171); }
        .d-card.medium::before { background: linear-gradient(90deg, #f59e0b, #fcd34d); }
        .d-card.low::before { background: linear-gradient(90deg, var(--lime), #c6e89a); }

        .d-card-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 16px; }
        .d-icon { font-size: 2rem; line-height: 1; }
        .d-severity {
            font-size: 10px; font-weight: 500; letter-spacing: .1em; text-transform: uppercase;
            padding: 4px 10px; border-radius: 20px;
        }
        .sev-high { background: rgba(239,68,68,.1); color: #ef4444; border: 1px solid rgba(239,68,68,.2); }
        .sev-med  { background: rgba(245,158,11,.1); color: #d97706; border: 1px solid rgba(245,158,11,.2); }
        .sev-low  { background: rgba(168,201,127,.15); color: #4a7c59; border: 1px solid rgba(168,201,127,.3); }

        .d-name { font-family: var(--serif); font-size: 1.25rem; font-weight: 700; color: var(--forest); margin-bottom: 10px; line-height: 1.3; }
        .d-type { font-size: 10px; font-weight: 500; letter-spacing: .15em; text-transform: uppercase; color: var(--muted); margin-bottom: 10px; }
        .d-desc { font-size: 14px; line-height: 1.75; color: #4a6a55; margin-bottom: 20px; }

        .d-tags { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 20px; }
        .d-tag {
            font-size: 11px; padding: 3px 10px; border-radius: 20px;
            background: rgba(74,124,89,.08); color: var(--sage);
            border: 1px solid rgba(74,124,89,.15);
        }

        .d-footer { display: flex; align-items: center; justify-content: space-between; padding-top: 16px; border-top: 1px solid #e8f0e0; }
        .d-crop { font-size: 11px; color: var(--muted); display: flex; align-items: center; gap: 5px; }
        .d-learn {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 12px; font-weight: 500; color: var(--sage);
            text-decoration: none; letter-spacing: .04em;
            transition: color .2s, gap .2s;
        }
        .d-learn:hover { color: var(--forest); gap: 10px; }

        /* ── MODAL ── */
        .modal-overlay {
            display: none; position: fixed; inset: 0; z-index: 1000;
            background: rgba(18,42,32,.7); backdrop-filter: blur(4px);
            align-items: center; justify-content: center; padding: 24px;
        }
        .modal-overlay.open { display: flex; }
        .modal-box {
            background: var(--white); border-radius: 16px; max-width: 680px; width: 100%;
            max-height: 85vh; overflow-y: auto; position: relative;
            box-shadow: 0 24px 80px rgba(0,0,0,.3);
            animation: modalIn .25s ease;
        }
        @keyframes modalIn { from { opacity:0; transform:translateY(20px) scale(.97); } to { opacity:1; transform:none; } }
        .modal-header {
            background: var(--deep); padding: 32px 36px 28px; position: sticky; top: 0; z-index: 1;
            display: flex; align-items: flex-start; justify-content: space-between; gap: 16px;
        }
        .modal-header-left {}
        .modal-h-label { font-size: 10px; letter-spacing: .2em; text-transform: uppercase; color: var(--lime); margin-bottom: 8px; }
        .modal-h-title { font-family: var(--serif); font-size: 1.6rem; color: #fff; font-weight: 700; line-height: 1.2; }
        .modal-h-type { font-size: 12px; color: rgba(250,253,247,.4); margin-top: 6px; letter-spacing: .05em; }
        .modal-close {
            background: rgba(255,255,255,.1); border: none; cursor: pointer; color: rgba(255,255,255,.6);
            width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; transition: background .2s, color .2s; font-size: 18px; line-height: 1;
        }
        .modal-close:hover { background: rgba(255,255,255,.2); color: #fff; }
        .modal-body { padding: 32px 36px; }
        .modal-section { margin-bottom: 28px; }
        .modal-section:last-child { margin-bottom: 0; }
        .modal-section-title {
            font-size: 10px; font-weight: 500; letter-spacing: .2em; text-transform: uppercase;
            color: var(--muted); margin-bottom: 12px; display: flex; align-items: center; gap: 8px;
        }
        .modal-section-title::after { content: ''; flex: 1; height: 1px; background: #e8f0e0; }
        .modal-body p { font-size: 14px; line-height: 1.8; color: #3a5a45; margin-bottom: 10px; }
        .modal-body ul { padding-left: 18px; }
        .modal-body ul li { font-size: 14px; line-height: 1.75; color: #3a5a45; margin-bottom: 6px; }
        .modal-body em { font-style: italic; color: var(--sage); }
        .modal-body strong { color: var(--forest); }

        .modal-sev-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: 500;
            letter-spacing: .05em; text-transform: uppercase; margin-bottom: 16px;
        }
        .modal-sev-badge.high { background: rgba(239,68,68,.1); color: #ef4444; border: 1px solid rgba(239,68,68,.2); }
        .modal-sev-badge.medium { background: rgba(245,158,11,.1); color: #d97706; border: 1px solid rgba(245,158,11,.2); }
        .modal-sev-badge.low { background: rgba(168,201,127,.15); color: #4a7c59; border: 1px solid rgba(168,201,127,.3); }

        .detect-cta {
            background: var(--deep); border-radius: 10px; padding: 24px 28px;
            display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;
            margin-top: 28px;
        }
        .detect-cta p { font-size: 14px; color: rgba(250,253,247,.6); }
        .detect-cta strong { display: block; font-family: var(--serif); font-size: 1.1rem; color: #fff; margin-bottom: 4px; }
        .detect-btn {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--lime); color: var(--deep); padding: 11px 22px;
            border-radius: 4px; font-family: var(--sans); font-size: 12px; font-weight: 500;
            letter-spacing: .06em; text-transform: uppercase; text-decoration: none;
            white-space: nowrap; transition: background .2s, transform .2s;
        }
        .detect-btn:hover { background: #bcd98e; transform: translateY(-2px); }

        /* ── NO RESULTS ── */
        .no-results { text-align: center; padding: 80px 0; color: var(--muted); display: none; }
        .no-results.show { display: block; }
        .no-results svg { margin-bottom: 16px; opacity: .3; }
        .no-results p { font-size: 15px; }

        @media(max-width: 640px) {
            .disease-grid { grid-template-columns: 1fr; }
            .modal-header { padding: 24px 20px; }
            .modal-body { padding: 24px 20px; }
        }
    </style>
</head>
<body>

<?php include('../components/header_navigation_bar.php'); ?>

<!-- HERO -->
<section class="page-hero">
    <div class="hero-inner">
        <div class="hero-left">
            <p class="hero-label">Disease Library</p>
            <h1 class="hero-title">Paddy <em>Diseases</em></h1>
            <p class="hero-desc">A complete guide to the 9 paddy diseases our AI model detects — with symptoms, chemical treatments, and prevention tips used in Sri Lanka.</p>
            <div class="hero-stat-row">
                <div class="hero-stat"><div class="num">9</div><div class="lbl">Diseases</div></div>
                <div class="hero-stat"><div class="num">95%</div><div class="lbl">Accuracy</div></div>
                <div class="hero-stat"><div class="num">&lt;5s</div><div class="lbl">Detection</div></div>
            </div>
        </div>
        <a href="<?= BASE_URL ?>/index.php" class="back-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Home
        </a>
    </div>
</section>

<!-- FILTER BAR -->
<div class="filter-bar">
    <button class="filter-btn active" data-filter="all">All (9)</button>
    <button class="filter-btn" data-filter="high">High Risk</button>
    <button class="filter-btn" data-filter="medium">Medium Risk</button>
    <button class="filter-btn" data-filter="low">Low Risk</button>
    <button class="filter-btn" data-filter="bacterial">Bacterial</button>
    <button class="filter-btn" data-filter="fungal">Fungal</button>
    <button class="filter-btn" data-filter="viral">Viral</button>
    <button class="filter-btn" data-filter="insect">Insect</button>
</div>

<!-- MAIN -->
<div class="diseases-wrap">

    <div class="legend">
        <div class="legend-item"><div class="legend-dot dot-high"></div> High Risk — Immediate action required</div>
        <div class="legend-item"><div class="legend-dot dot-med"></div> Medium Risk — Monitor and treat</div>
        <div class="legend-item"><div class="legend-dot dot-low"></div> Low Risk — Preventive management</div>
    </div>

    <div class="disease-grid" id="diseaseGrid">

        <!-- 1. Blast -->
        <div class="d-card high" data-severity="high" data-type="fungal" onclick="openModal('blast')">
            <div class="d-card-top">
                <div class="d-icon">🍃</div>
                <span class="d-severity sev-high">High Risk</span>
            </div>
            <div class="d-type">Fungal Disease</div>
            <h3 class="d-name">Blast Disease</h3>
            <p class="d-desc">Caused by <em>Magnaporthe oryzae</em>. Diamond-shaped grey lesions on leaves, nodes, and panicles. One of the most destructive rice diseases in Sri Lanka.</p>
            <div class="d-tags">
                <span class="d-tag">Leaves</span>
                <span class="d-tag">Nodes</span>
                <span class="d-tag">Panicles</span>
            </div>
            <div class="d-footer">
                <span class="d-crop">🌾 All growth stages</span>
                <span class="d-learn">Learn more <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
            </div>
        </div>

        <!-- 2. Bacterial Leaf Blight -->
        <div class="d-card high" data-severity="high" data-type="bacterial" onclick="openModal('blb')">
            <div class="d-card-top">
                <div class="d-icon">🌾</div>
                <span class="d-severity sev-high">High Risk</span>
            </div>
            <div class="d-type">Bacterial Disease</div>
            <h3 class="d-name">Bacterial Leaf Blight</h3>
            <p class="d-desc">Caused by <em>Xanthomonas oryzae</em> pv. <em>oryzae</em>. Water-soaked yellow lesions from leaf edges. Can cause complete wilting in seedlings (kresek).</p>
            <div class="d-tags">
                <span class="d-tag">Leaf Edges</span>
                <span class="d-tag">Seedlings</span>
                <span class="d-tag">Wet Season</span>
            </div>
            <div class="d-footer">
                <span class="d-crop">🌾 Seedling to tillering</span>
                <span class="d-learn">Learn more <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
            </div>
        </div>

        <!-- 3. Bacterial Leaf Streak -->
        <div class="d-card high" data-severity="high" data-type="bacterial" onclick="openModal('bls')">
            <div class="d-card-top">
                <div class="d-icon">🌿</div>
                <span class="d-severity sev-high">High Risk</span>
            </div>
            <div class="d-type">Bacterial Disease</div>
            <h3 class="d-name">Bacterial Leaf Streak</h3>
            <p class="d-desc">Caused by <em>Xanthomonas oryzae</em> pv. <em>oryzicola</em>. Linear dark-green streaks between veins turning light brown. Yellow bacterial ooze in humid conditions.</p>
            <div class="d-tags">
                <span class="d-tag">Leaf Veins</span>
                <span class="d-tag">Humid Conditions</span>
            </div>
            <div class="d-footer">
                <span class="d-crop">🌾 Tillering to heading</span>
                <span class="d-learn">Learn more <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
            </div>
        </div>

        <!-- 4. Bacterial Panicle Blight -->
        <div class="d-card high" data-severity="high" data-type="bacterial" onclick="openModal('bpb')">
            <div class="d-card-top">
                <div class="d-icon">🌾</div>
                <span class="d-severity sev-high">High Risk</span>
            </div>
            <div class="d-type">Bacterial Disease</div>
            <h3 class="d-name">Bacterial Panicle Blight</h3>
            <p class="d-desc">Caused by <em>Burkholderia glumae</em>. Spikelets turn brown and remain empty. Up to 30–40% yield loss in severe outbreaks at flowering stage.</p>
            <div class="d-tags">
                <span class="d-tag">Panicles</span>
                <span class="d-tag">Spikelets</span>
                <span class="d-tag">Flowering</span>
            </div>
            <div class="d-footer">
                <span class="d-crop">🌾 Booting to heading</span>
                <span class="d-learn">Learn more <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
            </div>
        </div>

        <!-- 5. Dead Heart -->
        <div class="d-card high" data-severity="high" data-type="insect" onclick="openModal('deadheart')">
            <div class="d-card-top">
                <div class="d-icon">🪲</div>
                <span class="d-severity sev-high">High Risk</span>
            </div>
            <div class="d-type">Insect Pest Damage</div>
            <h3 class="d-name">Dead Heart</h3>
            <p class="d-desc">Caused by the Yellow Stem Borer (<em>Scirpophaga incertulas</em>). Central tiller dies and pulls out easily. Can cause 20–80% yield loss if uncontrolled.</p>
            <div class="d-tags">
                <span class="d-tag">Stem Borer</span>
                <span class="d-tag">Central Tiller</span>
                <span class="d-tag">Vegetative Stage</span>
            </div>
            <div class="d-footer">
                <span class="d-crop">🌾 Seedling to tillering</span>
                <span class="d-learn">Learn more <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
            </div>
        </div>

        <!-- 6. Tungro -->
        <div class="d-card high" data-severity="high" data-type="viral" onclick="openModal('tungro')">
            <div class="d-card-top">
                <div class="d-icon">🟡</div>
                <span class="d-severity sev-high">High Risk</span>
            </div>
            <div class="d-type">Viral Disease</div>
            <h3 class="d-name">Tungro Virus</h3>
            <p class="d-desc">Caused by RTBV + RTSV virus complex. Transmitted by green leafhoppers. Yellow-orange discoloration and stunted growth. No direct cure — vector control is key.</p>
            <div class="d-tags">
                <span class="d-tag">Leafhopper</span>
                <span class="d-tag">Yellowing</span>
                <span class="d-tag">Stunting</span>
            </div>
            <div class="d-footer">
                <span class="d-crop">🌾 All growth stages</span>
                <span class="d-learn">Learn more <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
            </div>
        </div>

        <!-- 7. Brown Spot -->
        <div class="d-card medium" data-severity="medium" data-type="fungal" onclick="openModal('brownspot')">
            <div class="d-card-top">
                <div class="d-icon">🟤</div>
                <span class="d-severity sev-med">Medium Risk</span>
            </div>
            <div class="d-type">Fungal Disease</div>
            <h3 class="d-name">Brown Spot</h3>
            <p class="d-desc">Caused by <em>Bipolaris oryzae</em>. Oval brown lesions with yellow halos on leaves and grains. Often linked to potassium and silicon deficiency in soil.</p>
            <div class="d-tags">
                <span class="d-tag">Leaves</span>
                <span class="d-tag">Grains</span>
                <span class="d-tag">Nutrient Stress</span>
            </div>
            <div class="d-footer">
                <span class="d-crop">🌾 All growth stages</span>
                <span class="d-learn">Learn more <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
            </div>
        </div>

        <!-- 8. Downy Mildew -->
        <div class="d-card medium" data-severity="medium" data-type="fungal" onclick="openModal('downymildew')">
            <div class="d-card-top">
                <div class="d-icon">💧</div>
                <span class="d-severity sev-med">Medium Risk</span>
            </div>
            <div class="d-type">Oomycete Disease</div>
            <h3 class="d-name">Downy Mildew</h3>
            <p class="d-desc">Caused by <em>Sclerophthora macrospora</em>. White-grey downy patches on leaf undersides with yellowing above. Thrives in waterlogged soils and high humidity.</p>
            <div class="d-tags">
                <span class="d-tag">Leaf Underside</span>
                <span class="d-tag">Waterlogged</span>
                <span class="d-tag">Humid</span>
            </div>
            <div class="d-footer">
                <span class="d-crop">🌾 Seedling to tillering</span>
                <span class="d-learn">Learn more <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
            </div>
        </div>

        <!-- 9. Hispa -->
        <div class="d-card medium" data-severity="medium" data-type="insect" onclick="openModal('hispa')">
            <div class="d-card-top">
                <div class="d-icon">🪲</div>
                <span class="d-severity sev-med">Medium Risk</span>
            </div>
            <div class="d-type">Insect Pest Damage</div>
            <h3 class="d-name">Hispa</h3>
            <p class="d-desc">Rice Hispa beetle (<em>Dicladispa armigera</em>) scratches leaves causing white streaks. Larvae mine inside leaves forming blister patches. Common in dense crops.</p>
            <div class="d-tags">
                <span class="d-tag">Leaf Surface</span>
                <span class="d-tag">Leaf Mines</span>
                <span class="d-tag">Dense Crops</span>
            </div>
            <div class="d-footer">
                <span class="d-crop">🌾 Tillering stage</span>
                <span class="d-learn">Learn more <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
            </div>
        </div>

    </div><!-- /disease-grid -->

    <div class="no-results" id="noResults">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#8BAF8E" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <p>No diseases match this filter.</p>
    </div>

</div><!-- /diseases-wrap -->

<!-- ── MODAL ── -->
<div class="modal-overlay" id="modalOverlay" onclick="closeModalOutside(event)">
    <div class="modal-box" id="modalBox">
        <div class="modal-header">
            <div class="modal-header-left">
                <div class="modal-h-label" id="modalLabel"></div>
                <div class="modal-h-title" id="modalTitle"></div>
                <div class="modal-h-type" id="modalType"></div>
            </div>
            <button class="modal-close" onclick="closeModal()">✕</button>
        </div>
        <div class="modal-body" id="modalBody"></div>
    </div>
</div>

<?php include('../components/footer.php'); ?>

<script>
// ── DISEASE DATA ──────────────────────────────────────────────────────────
const diseaseData = {
    blast: {
        label: 'Fungal Disease',
        title: 'Blast Disease',
        type: 'Magnaporthe oryzae',
        severity: 'high',
        description: 'Blast is the most feared fungal disease of rice. It attacks at any stage — seedling, tillering, or grain filling. Diamond-shaped grey lesions with reddish-brown borders appear on leaves. Neck blast breaks the panicle stem, causing empty grains.',
        symptoms: ['Diamond or spindle-shaped lesions on leaves with grey centres and brown borders', 'Neck blast — dark brown lesion at the base of the panicle causes it to break', 'Node blast — nodes turn black and break', 'Collar blast at the leaf collar causes the leaf to die'],
        chemical: ['Tricyclazole 75% WP (Beam / Blascide) at 0.6 g/L — most effective in Sri Lanka', 'Isoprothiolane 40% EC (Fuji-One) at 1.5 mL/L — systemic for leaf and neck blast', 'Propiconazole 25% EC (Tilt) at 1 mL/L — curative option', 'Azoxystrobin 23% SC (Amistar) at 1 mL/L — preventive spray'],
        cultural: ['Use blast-resistant DOA varieties (BG 300, At 362)', 'Apply nitrogen in split doses using Leaf Colour Chart (LCC)', 'Maintain 20×20 cm plant spacing for air circulation', 'Deep-plough crop residues after harvest'],
        prevention: 'Seed treatment with Carbendazim 50% WP at 2 g/kg or Thiram 75% WP at 2.5 g/kg before sowing.'
    },
    blb: {
        label: 'Bacterial Disease',
        title: 'Bacterial Leaf Blight',
        type: 'Xanthomonas oryzae pv. oryzae',
        severity: 'high',
        description: 'BLB is one of the most important rice diseases in Sri Lanka, especially during Maha season. It enters through water pores and wounds. Severe infection in seedlings causes kresek — complete wilting within days of transplanting.',
        symptoms: ['Water-soaked yellowish lesions starting at leaf margins and spreading inward', 'Lesions turn straw-yellow and dry out', 'Kresek — complete seedling wilting in young transplanted crops', 'Bacterial ooze visible when cut leaf is dipped in water'],
        chemical: ['Copper Oxychloride 50% WP at 3 g/L — spray every 10–15 days', 'Streptomycin + Tetracycline (Agrimycin) at 200 ppm — 1 g in 5 L water', 'Copper Hydroxide (Kocide) at 2 g/L as an alternative'],
        cultural: ['Drain the field immediately when symptoms appear', 'Reduce nitrogen application — avoid heavy urea top dressing', 'Use resistant varieties: BG 250, BG 360, BG 379', 'Do not work in the field when plants are wet'],
        prevention: 'Seed treatment with Streptomycin (0.025%). Use certified disease-free seeds. Ensure proper drainage before planting.'
    },
    bls: {
        label: 'Bacterial Disease',
        title: 'Bacterial Leaf Streak',
        type: 'Xanthomonas oryzae pv. oryzicola',
        severity: 'high',
        description: 'BLS causes linear dark streaks between leaf veins. Unlike BLB which starts at leaf edges, BLS streaks run along the length of the leaf between veins. Yellow bacterial ooze may be visible on lesion surface under high humidity.',
        symptoms: ['Dark green water-soaked linear streaks between leaf veins', 'Streaks turn light brown to yellowish-grey as they age', 'Yellow bacterial ooze on lesion surface in humid conditions', 'Leaves may become ragged in severe infections'],
        chemical: ['Copper Oxychloride 50% WP at 3 g/L — foliar spray at heading stage', 'Copper Hydroxide (Kocide 2000) at 2 g/L — alternative bactericide', 'Agrimycin (Streptomycin-based) at 200 ppm for severe infection'],
        cultural: ['Drain field during flooding to limit bacterial spread', 'Remove and destroy crop residues after harvest', 'Use balanced nitrogen — avoid excess urea', 'Ensure good nursery and field drainage'],
        prevention: 'Hot water seed treatment at 52°C for 10 minutes. Use resistant varieties. Practice crop rotation between seasons.'
    },
    bpb: {
        label: 'Bacterial Disease',
        title: 'Bacterial Panicle Blight',
        type: 'Burkholderia glumae',
        severity: 'high',
        description: 'BPB causes grain sterility at flowering stage. Infected spikelets turn brown and remain empty. The disease is favoured by hot, humid weather during booting and flowering. Yield loss can reach 30–40% in severe outbreaks.',
        symptoms: ['Spikelets turn light brown to straw-coloured', 'Affected grains remain empty or partially filled', 'Discoloration starts at the tip of the spikelet', 'Severe cases cause entire panicles to turn brown'],
        chemical: ['Copper Oxychloride 50% WP at 3 g/L — spray at panicle emergence', 'Kasugamycin 2% SL (Kasumin) at 2 mL/L — effective against Burkholderia', 'Streptomycin + Tetracycline (Streptocycline) at 200 ppm at booting stage'],
        cultural: ['Maintain adequate irrigation at flowering — avoid heat and water stress', 'Reduce nitrogen at panicle initiation stage', 'Remove and destroy infected panicles early', 'Use clean certified seeds'],
        prevention: 'Hot water seed treatment at 52°C for 10 minutes. Moderate nitrogen. Avoid planting during peak hot-humid periods.'
    },
    deadheart: {
        label: 'Insect Pest Damage',
        title: 'Dead Heart (Stem Borer)',
        type: 'Scirpophaga incertulas — Yellow Stem Borer',
        severity: 'high',
        description: 'Dead Heart is caused by Yellow Stem Borer larvae boring into the central tiller. The central shoot dies while surrounding tillers remain green. The dead tiller pulls out easily with a hollow rotting base. The same pest causes White Ear at reproductive stage.',
        symptoms: ['Central tiller turns yellow then brown and dries out', 'Dead tiller pulls out easily with a slight tug', 'Small entry hole visible at tiller base', 'Hollow, rotting stem inside — unlike drought which affects multiple tillers uniformly'],
        chemical: ['Chlorantraniliprole 18.5% SC (Coragen) at 0.4 mL/L — most effective modern option', 'Carbosulfan 200 g/L SC (Marshal) at 2 mL/L — widely used in Sri Lanka', 'Chlorpyrifos 20% EC at 2.5 mL/L — spray in evening', 'Imidacloprid 17.8% SL (Confidor) at 0.5 mL/L — apply at 20–30 DAT'],
        cultural: ['Burn or incorporate rice stubble immediately after harvest', 'Raise water level to submerge newly hatched larvae', 'Set up light traps (1 per ha) to attract adult moths', 'Remove egg masses by hand during early infestation'],
        prevention: 'Spray only when >10% dead hearts observed. Balanced nitrogen — excess attracts egg-laying moths. Plant on time per DOA calendar.'
    },
    tungro: {
        label: 'Viral Disease',
        title: 'Tungro Virus',
        type: 'RTBV + RTSV virus complex (vector: Nephotettix virescens)',
        severity: 'high',
        description: 'Tungro is a viral disease with no direct cure. Management focuses entirely on controlling the green leafhopper vector. It is most severe when leafhoppers are abundant during early crop stages. Infected plants show yellow-orange leaves and are stunted and produce few tillers.',
        symptoms: ['Yellow to orange discoloration of leaves, starting from the tip', 'Severely stunted plant growth', 'Reduced tillering and poor panicle development', 'Leaves may show interveinal yellowing similar to nutrient deficiency'],
        chemical: ['Imidacloprid 17.8% SL (Confidor) at 0.5 mL/L — apply at transplanting and 20–25 DAT', 'Thiamethoxam 25% WG (Actara) at 0.3 g/L — neonicotinoid for leafhopper control', 'Buprofezin 25% SC at 1 mL/L — insect growth regulator', 'Avoid pyrethroids alone — can cause leafhopper resurgence'],
        cultural: ['Use tungro-resistant DOA varieties: BG 352, BG 357, BG 358, At 307', 'Uproot and destroy infected plants as soon as symptoms appear', 'Synchronize planting within the village to break the disease cycle', 'Remove weed hosts of leafhoppers from field bunds'],
        prevention: 'Imidacloprid seed treatment before transplanting. Monitor fields weekly for leafhoppers. Use yellow sticky traps for early detection.'
    },
    brownspot: {
        label: 'Fungal Disease',
        title: 'Brown Spot',
        type: 'Bipolaris oryzae',
        severity: 'medium',
        description: 'Brown Spot is a fungal disease closely linked to soil nutrient stress — particularly potassium and silicon deficiency. It is often found in nutrient-poor or drought-stressed fields. While rarely catastrophic, severe infection discolours grains and reduces quality.',
        symptoms: ['Oval to circular brown spots with yellow halos on leaves', 'Spots have a light brown centre with dark brown border', 'Infected grains show brown to black discoloration', 'Heavily infected leaves may yellow and die prematurely'],
        chemical: ['Mancozeb 75% WP (Dithane M-45) at 2.5 g/L — protective spray', 'Propiconazole 25% EC (Tilt) at 1 mL/L — systemic curative', 'Tebuconazole 250 g/L EW (Folicur) at 1 mL/L — DOA recommended', 'Edifenphos 50% EC (Hinosan) at 1 mL/L — contact fungicide'],
        cultural: ['Apply MOP (Muriate of Potash) to correct potassium deficiency', 'Apply silicon soil amendment if available', 'Maintain good drainage and balanced fertilization', 'Avoid water stress during critical growth stages'],
        prevention: 'Hot water seed treatment at 52°C for 10 minutes. Ensure adequate soil fertility. Treat seeds with Thiram 75% WP at 2.5 g/kg.'
    },
    downymildew: {
        label: 'Oomycete Disease',
        title: 'Downy Mildew',
        type: 'Sclerophthora macrospora',
        severity: 'medium',
        description: 'Downy mildew in rice produces yellow patches on the upper leaf surface with white-grey downy sporulation on the underside. Severe infection causes twisted, deformed leaves and stunted growth. It is favoured by waterlogged nursery beds and mild temperatures.',
        symptoms: ['Yellow to white patches on upper leaf surface', 'White-grey downy sporulation on the underside of leaves', 'Twisted and deformed leaves in severe cases', 'Stunted plant growth and poor tillering'],
        chemical: ['Metalaxyl 8% + Mancozeb 64% WP (Ridomil Gold MZ) at 2.5 g/L — systemic + contact', 'Mancozeb 75% WP (Dithane M-45) at 2.5 g/L — protective spray', 'Copper Oxychloride 50% WP at 3 g/L — spray leaf undersides thoroughly', 'Fosetyl-Al 80% WP (Aliette) at 2.5 g/L — systemic oomycete fungicide'],
        cultural: ['Improve nursery and field drainage — avoid waterlogged conditions', 'Reduce plant density to improve air circulation', 'Remove and destroy severely infected plants early', 'Avoid excess nitrogen fertilization'],
        prevention: 'Metalaxyl-based seed treatment before sowing. Dry fields during fallow. Avoid continuous flooding in nursery beds.'
    },
    hispa: {
        label: 'Insect Pest Damage',
        title: 'Hispa',
        type: 'Dicladispa armigera — Rice Hispa Beetle',
        severity: 'medium',
        description: 'Rice Hispa is an insect pest where adult beetles and larvae both damage leaves. Adults scratch the upper leaf surface creating white longitudinal streaks. Larvae tunnel inside the leaf creating white blister-like mines. Common during wet season in dense, nitrogen-rich crops.',
        symptoms: ['White longitudinal streaks on upper leaf surface from adult feeding', 'White blister-like patches (leaf mines) from larval tunnelling', 'Severely infested leaves dry out completely', 'Infestation often starts at field edges and spreads inward'],
        chemical: ['Chlorpyrifos 20% EC at 2.5 mL/L — spray in late evening', 'Carbosulfan 200 g/L SC (Marshal) at 2 mL/L — systemic, kills adults and larvae', 'Imidacloprid 17.8% SL (Confidor) at 0.5 mL/L — systemic early treatment', 'Quinalphos 25% EC at 2 mL/L — effective against adult beetles'],
        cultural: ['Clip tips of infested seedlings before transplanting to remove mines and eggs', 'Maintain proper plant spacing — avoid very dense planting', 'Avoid excess nitrogen which encourages lush growth attractive to hispa', 'Keep field bunds free from grasses that serve as alternate hosts'],
        prevention: 'Inspect nursery seedlings before transplanting. Remove infested plants. Balanced fertilization. Good field sanitation between seasons.'
    }
};

// ── FILTER ────────────────────────────────────────────────────────────────
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const filter = btn.dataset.filter;
        const cards = document.querySelectorAll('.d-card');
        let visible = 0;
        cards.forEach(card => {
            const match = filter === 'all'
                || card.dataset.severity === filter
                || card.dataset.type === filter;
            card.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        document.getElementById('noResults').classList.toggle('show', visible === 0);
    });
});

// ── MODAL ─────────────────────────────────────────────────────────────────
function openModal(id) {
    const d = diseaseData[id];
    if (!d) return;

    document.getElementById('modalLabel').textContent = d.label;
    document.getElementById('modalTitle').textContent = d.title;
    document.getElementById('modalType').innerHTML = '<em>' + d.type + '</em>';

    const sevClass = d.severity;
    const sevLabel = { high: 'High Risk', medium: 'Medium Risk', low: 'Low Risk' }[d.severity];

    const symptomsHtml = d.symptoms.map(s => `<li>${s}</li>`).join('');
    const chemHtml = d.chemical.map(c => `<li>${c}</li>`).join('');
    const culturalHtml = d.cultural.map(c => `<li>${c}</li>`).join('');

    document.getElementById('modalBody').innerHTML = `
        <span class="modal-sev-badge ${sevClass}">${sevLabel}</span>

        <div class="modal-section">
            <div class="modal-section-title">Overview</div>
            <p>${d.description}</p>
        </div>

        <div class="modal-section">
            <div class="modal-section-title">Symptoms</div>
            <ul>${symptomsHtml}</ul>
        </div>

        <div class="modal-section">
            <div class="modal-section-title">Chemical Treatment</div>
            <ul>${chemHtml}</ul>
        </div>

        <div class="modal-section">
            <div class="modal-section-title">Cultural Management</div>
            <ul>${culturalHtml}</ul>
        </div>

        <div class="modal-section">
            <div class="modal-section-title">Prevention</div>
            <p>${d.prevention}</p>
        </div>

        <div class="detect-cta">
            <div>
                <strong>Detect it with AI</strong>
                <p>Upload a leaf image to get an instant diagnosis.</p>
            </div>
            <a href="http://127.0.0.1:5000" target="_blank" class="detect-btn">
                Try Detection
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
    `;

    document.getElementById('modalOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('modalOverlay').classList.remove('open');
    document.body.style.overflow = '';
}

function closeModalOutside(e) {
    if (e.target === document.getElementById('modalOverlay')) closeModal();
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
</script>
</body>
</html>