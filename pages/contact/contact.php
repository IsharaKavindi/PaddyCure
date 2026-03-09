<?php
require '../../config.php';
require '../../utils/database.php';
require '../../utils/authenticate.php';

$conn = initialize_database();
session_start();
authenticate();

$user_id = (int)$_SESSION['user_id'];

// Pre-fill from logged-in user
$user_row  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT first_name, last_name, email FROM user WHERE id = $user_id"));
$prefill_name  = trim(($user_row['first_name'] ?? '') . ' ' . ($user_row['last_name'] ?? ''));
$prefill_email = $user_row['email'] ?? '';

$success = false;
$error   = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name && $email && $subject && $message) {
        $stmt = mysqli_prepare($conn,
            "INSERT INTO contact_messages (user_id, name, email, subject, message) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            $error = true;
        } else {
            mysqli_stmt_bind_param($stmt, 'issss', $user_id, $name, $email, $subject, $message);
            if (!mysqli_stmt_execute($stmt)) {
                $error = true;
            } else {
                $success = true;
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $error = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Us – PaddyCure</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/public/styles/styles.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/public/styles/fonts.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="shortcut icon" href="<?php echo BASE_URL; ?>/public/images/paddyCureLogo2.png" type="image/x-icon">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --deep:#080F0B;--panel:#0F1C14;--card:#131E18;--lifted:#1a2e20;
  --lime:#96D35E;--green:#52B068;
  --white:#FAFDF7;--mist:rgba(195,228,185,.6);
  --muted:rgba(195,228,185,.38);--dim:rgba(195,228,185,.28);
  --border:rgba(100,190,90,.13);--border2:rgba(100,190,90,.28);
  --serif:'Cormorant Garamond',Georgia,serif;--sans:'Outfit',sans-serif;
}
html{scroll-behavior:smooth}
body{background:var(--white);color:var(--deep);font-family:var(--sans);font-weight:300;min-height:100vh}
body::before{content:'';position:fixed;inset:0;z-index:0;pointer-events:none;
  background:repeating-linear-gradient(160deg,rgba(82,176,104,.045) 0px,rgba(82,176,104,.045) 1px,transparent 1px,transparent 60px)}


/* ── PAGE ── */
.contact-page{
  background:black;
  min-height:calc(100vh - 62px);
  position:relative;
  display:flex;align-items:center;justify-content:center;
  padding:72px 24px 80px;
}
.contact-page::after{
  content:'';position:absolute;inset:0;pointer-events:none;
  background:
    radial-gradient(ellipse 60% 50% at 85% 15%, rgba(52,176,104,.2) 0%, transparent 65%),
    radial-gradient(ellipse 40% 40% at 10% 85%, rgba(150,211,94,.1) 0%, transparent 60%);
}
.blob{position:absolute;border-radius:50%;filter:blur(90px);opacity:.14;animation:drift 9s ease-in-out infinite alternate;pointer-events:none}
.blob-1{width:420px;height:420px;background:var(--green);top:-100px;right:-80px;animation-delay:0s}
.blob-2{width:260px;height:260px;background:var(--lime);bottom:-40px;left:6%;animation-delay:4s}
.blob-3{width:180px;height:180px;background:var(--lime);top:40%;left:45%;animation-delay:6s}
@keyframes drift{from{transform:translate(0,0) scale(1)}to{transform:translate(20px,-28px) scale(1.05)}}

/* ── CARD ── */
.contact-card{
  position:relative;z-index:2;
  width:100%;max-width:640px;
  background:rgba(250,253,247,.04);
  border:1px solid rgba(100,190,90,.15);
  border-radius:16px;overflow:hidden;
  backdrop-filter:blur(6px);
  animation:cardIn .7s ease both;
}
@keyframes cardIn{from{opacity:0;transform:translateY(28px)}to{opacity:1;transform:translateY(0)}}

/* card header */
.card-header{
  background:linear-gradient(135deg,rgba(150,211,94,.1) 0%,rgba(52,176,104,.06) 100%);
  border-bottom:1px solid rgba(100,190,90,.13);
  padding:44px 52px 36px;
}
.header-label{
  font-size:10px;font-weight:500;letter-spacing:.28em;text-transform:uppercase;
  color:var(--lime);display:flex;align-items:center;gap:10px;margin-bottom:14px;
}
.header-label::before{content:'';display:block;width:22px;height:1px;background:var(--lime)}
.card-title{font-family:var(--serif);font-size:clamp(1.8rem,3vw,2.3rem);font-weight:700;color:var(--white);line-height:1.2;margin-bottom:10px}
.card-title em{font-style:italic;color:var(--lime)}
.card-subtitle{font-size:14px;line-height:1.7;color:rgba(250,253,247,.45)}

/* quick topics */
.quick-topics{display:flex;flex-wrap:wrap;gap:7px;margin-top:18px}
.qt{
  display:inline-flex;align-items:center;gap:5px;
  padding:6px 13px;border-radius:100px;font-size:12px;
  background:rgba(82,176,104,.1);border:1px solid rgba(82,176,104,.2);
  color:rgba(195,228,185,.55);cursor:pointer;
  transition:all .18s;user-select:none;
}
.qt:hover,.qt.active{background:rgba(82,176,104,.22);border-color:rgba(82,176,104,.42);color:var(--lime)}

/* alerts */
.alert{margin:28px 52px 0;display:flex;align-items:center;gap:12px;padding:14px 18px;border-radius:8px;font-size:13px;animation:slideIn .4s ease both}
@keyframes slideIn{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:translateY(0)}}
.alert svg{width:17px;height:17px;flex-shrink:0;stroke:currentColor;fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.alert-success{background:rgba(150,211,94,.1);border:1px solid rgba(150,211,94,.3);color:var(--lime)}
.alert-error{background:rgba(220,80,60,.1);border:1px solid rgba(220,80,60,.25);color:#ff9980}

/* form */
.card-body{padding:36px 52px 48px;display:flex;flex-direction:column;gap:22px}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:20px}
.field{display:flex;flex-direction:column;gap:9px}
.field label{font-size:10px;font-weight:500;letter-spacing:.2em;text-transform:uppercase;color:var(--muted);transition:color .2s}
.field:focus-within label{color:var(--lime)}
.field input,.field textarea,.field select{
  background:rgba(18,42,32,.7);
  border:1px solid rgba(100,190,90,.12);
  border-radius:6px;padding:13px 16px;
  font-family:var(--sans);font-size:14px;font-weight:300;
  color:var(--white);outline:none;width:100%;
  transition:border-color .25s,background .25s;
  appearance:none;
}
.field textarea{resize:none}
.field input::placeholder,.field textarea::placeholder{color:rgba(139,175,142,.3)}
.field input:focus,.field textarea:focus,.field select:focus{border-color:rgba(100,190,90,.45);background:rgba(18,42,32,.9)}
.field select option{background:var(--deep);color:#fff}
.sel-wrap{position:relative}
.sel-wrap::after{content:'▾';position:absolute;right:14px;top:50%;transform:translateY(-50%);font-size:11px;color:var(--lime);pointer-events:none}

/* submit */
.btn-submit{
  align-self:flex-start;display:inline-flex;align-items:center;gap:10px;
  background:var(--lime);color:var(--deep);border:none;
  padding:14px 36px;font-family:var(--sans);font-size:12px;font-weight:500;
  letter-spacing:.12em;text-transform:uppercase;border-radius:4px;cursor:pointer;
  transition:background .25s,transform .2s,gap .2s;position:relative;overflow:hidden;
}
.btn-submit::before{content:'';position:absolute;inset:0;background:#82c249;transform:translateX(-101%);transition:transform .35s cubic-bezier(.77,0,.18,1)}
.btn-submit:hover::before{transform:translateX(0)}
.btn-submit:hover{gap:16px;transform:translateY(-2px)}
.btn-submit span,.btn-submit svg{position:relative;z-index:1}
.btn-submit svg{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}

/* info blocks */
.info-blocks{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:4px}
.ib{background:rgba(82,176,104,.06);border:1px solid rgba(82,176,104,.12);border-radius:8px;padding:14px 16px;text-align:center}
.ib-ico{font-size:22px;margin-bottom:6px}
.ib-title{font-size:11px;font-weight:500;color:rgba(195,228,185,.7);margin-bottom:3px}
.ib-val{font-size:11px;color:var(--dim)}

@media(max-width:640px){
  .card-header,.card-body{padding-left:24px;padding-right:24px}
  .alert{margin-left:24px;margin-right:24px}
  .form-row{grid-template-columns:1fr}
  .btn-submit{width:100%;justify-content:center}
  .info-blocks{grid-template-columns:1fr}
  .nav-links{display:none}
}
</style>
</head>
<body>

<?php include('../../components/header_navigation_bar.php'); ?>

<!-- ── PAGE ── -->
<main class="contact-page">

  <div class="contact-card">

    <!-- Header -->
    <div class="card-header">
      <p class="header-label">PaddyCare Support</p>
      <h1 class="card-title">How can we <em>Help?</em></h1>
      <p class="card-subtitle">
        Report a disease detection issue, ask about crop management, or send us any feedback about the app.
      </p>
      <!-- Quick topic pills — clicking fills the subject dropdown -->
      <div class="quick-topics">
        <span class="qt" onclick="pickTopic('Disease Detection Issue')">🔬 Detection Issue</span>
        <span class="qt" onclick="pickTopic('Crop Disease Question')">🌿 Crop Disease</span>
        <span class="qt" onclick="pickTopic('App Feedback')">💬 App Feedback</span>
        <span class="qt" onclick="pickTopic('Account Help')">👤 Account Help</span>
        <span class="qt" onclick="pickTopic('Other')">✉ Other</span>
      </div>
    </div>

    <!-- Alert -->
    <?php if ($success): ?>
    <div class="alert alert-success">
      <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
      Your message was sent! Our team will get back to you soon.
    </div>
    <?php elseif ($error): ?>
    <div class="alert alert-error">
      <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      Something went wrong. Please fill in all fields and try again.
    </div>
    <?php endif; ?>

    <!-- Form -->
    <?php if (!$success): ?>
    <div class="card-body">
      <form method="POST" id="contactForm" style="display:contents">

        <div class="form-row">
          <div class="field">
            <label for="name">Full Name *</label>
            <input type="text" id="name" name="name"
              value="<?= htmlspecialchars($prefill_name) ?>"
              placeholder="Your name" required>
          </div>
          <div class="field">
            <label for="email">Email Address *</label>
            <input type="email" id="email" name="email"
              value="<?= htmlspecialchars($prefill_email) ?>"
              placeholder="your@email.com" required>
          </div>
        </div>

        <div class="field">
          <label for="subject">Topic *</label>
          <div class="sel-wrap">
            <select id="subject" name="subject" required>
              <option value="" disabled selected>Select a topic…</option>
              <optgroup label="Disease & Detection">
                <option value="Disease Detection Issue">🔬 Disease Detection Issue</option>
                <option value="Crop Disease Question">🌿 Crop Disease Question</option>
                <option value="Treatment Advice">💊 Treatment Advice</option>
                <option value="Pest Control Query">🐛 Pest Control Query</option>
              </optgroup>
              <optgroup label="Farming">
                <option value="Fertilizer & Soil">🌱 Fertilizer &amp; Soil</option>
                <option value="Irrigation Question">💧 Irrigation Question</option>
                <option value="Harvest Guidance">🌾 Harvest Guidance</option>
                <option value="Weather Impact">⛅ Weather Impact</option>
              </optgroup>
              <optgroup label="App & Account">
                <option value="App Feedback">💬 App Feedback</option>
                <option value="Account Help">👤 Account Help</option>
                <option value="Report a Bug">🐞 Report a Bug</option>
                <option value="Other">✉ Other</option>
              </optgroup>
            </select>
          </div>
        </div>

        <div class="field">
          <label for="message">Your Message *</label>
          <textarea id="message" name="message" rows="5"
            placeholder="Describe your issue or question in detail. If reporting a disease detection problem, mention the crop type and what you observed…"
            required></textarea>
        </div>

        <!-- Contact info blocks -->
        <div class="info-blocks">
          <div class="ib">
            <div class="ib-ico">🕐</div>
            <div class="ib-title">Response Time</div>
            <div class="ib-val">Within 24 hours</div>
          </div>
          <div class="ib">
            <div class="ib-ico">🌾</div>
            <div class="ib-title">Farming Support</div>
            <div class="ib-val">Mon – Sat, 8am – 6pm</div>
          </div>
          <div class="ib">
            <div class="ib-ico">🔬</div>
            <div class="ib-title">Detection Issues</div>
            <div class="ib-val">Priority support</div>
          </div>
        </div>

        <button type="submit" class="btn-submit">
          <span>Send Message</span>
          <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </button>

      </form>
    </div>
    <?php else: ?>
    <!-- Success state body -->
    <div class="card-body" style="text-align:center;padding-top:48px;padding-bottom:48px;align-items:center">
      <div style="font-size:52px;margin-bottom:12px">🌱</div>
      <h2 style="font-family:var(--serif);font-size:1.8rem;color:#fff;margin-bottom:8px">Message <em style="color:var(--lime);font-style:italic">Received!</em></h2>
      <p style="font-size:14px;color:var(--dim);max-width:340px;line-height:1.7;margin-bottom:28px">
        Thank you for reaching out. Our agriculture support team will review your message and respond as soon as possible.
      </p>
      <a href="<?= BASE_URL ?>/pages/community.php"
        style="display:inline-flex;align-items:center;gap:8px;background:var(--lime);color:var(--deep);text-decoration:none;padding:12px 28px;border-radius:4px;font-size:12px;font-weight:500;letter-spacing:.12em;text-transform:uppercase">
        🌾 Back to Community
      </a>
    </div>
    <?php endif; ?>

  </div>
</main>

<?php include('../../components/footer.php'); ?>

<script>
// Quick topic pill → sets the select
function pickTopic(val) {
  const sel = document.getElementById('subject');
  if (!sel) return;
  sel.value = val;
  // highlight active pill
  document.querySelectorAll('.qt').forEach(q => {
    q.classList.toggle('active', q.textContent.trim().includes(val.split(' ')[0]) || q.onclick.toString().includes(val));
  });
  sel.closest('.field').querySelector('label').style.color = 'var(--lime)';
}
</script>
</body>
</html>