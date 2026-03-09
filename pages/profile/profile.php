<?php
require '../../config.php';
require '../../utils/database.php';

$conn = initialize_database();
session_start();

$sql = "SELECT id, first_name, last_name, email, contact_number, user_role, created_at
        FROM user WHERE id = {$_SESSION['user_id']}";
$res  = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($res);

$username     = $data['first_name'] . ' ' . $data['last_name'];
$initial      = strtoupper(substr($data['first_name'], 0, 1));
$member_since = date('F Y', strtotime($data['created_at']));
$role         = ucwords(strtolower($data['user_role']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($data['first_name']) ?>'s Profile – PaddyCare</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/public/styles/styles.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/public/styles/fonts.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="shortcut icon" href="<?php echo BASE_URL; ?>/public/images/paddyCureLogo2.png" type="image/x-icon">

<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}

:root{
  --deep:    #080F0B;
  --panel:   #0F1C14;
  --card:    #131E18;
  --lifted:  #1a2e20;
  --forest:  #0d2e18;
  --sage:    #52B068;
  --moss:    #3d8a52;
  --lime:    #96D35E;
  --white:   #FAFDF7;
  --mist:    rgba(195,228,185,.6);
  --muted:   rgba(195,228,185,.38);
  --dim:     rgba(195,228,185,.28);
  --border:  rgba(100,190,90,.13);
  --border2: rgba(100,190,90,.28);
  --serif:   'Cormorant Garamond', Georgia, serif;
  --sans:    'Outfit', sans-serif;
}

body{
  background:var(--white);
  color:var(--black);
  font-family:var(--sans);
  font-weight:300;
  min-height:100vh;
}


/* avatar bubble */
.nav-av{
  width:34px;height:34px;
  border-radius:50%;
  background:linear-gradient(135deg,var(--sage),var(--lime));
  display:flex;align-items:center;justify-content:center;
  font-family:var(--serif);
  font-size:14px;font-weight:700;
  color:#fff;
  text-transform:uppercase;
  flex-shrink:0;
}

/* mobile hamburger placeholder */
.nav-mobile-toggle{display:none}

/* ══════════════════════════════════════
   PAGE BACKGROUND (same as contact.php)
══════════════════════════════════════ */
.profile-page{
  min-height:calc(100vh - 62px);
  background:var(--deep);
  position:relative;
  overflow:hidden;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:72px 24px 80px;
}

.profile-page::before{
  content:'';
  position:absolute;inset:0;pointer-events:none;
  background-image:repeating-linear-gradient(
    160deg,
    rgba(82,176,104,.045) 0px,
    rgba(82,176,104,.045) 1px,
    transparent 1px,
    transparent 60px
  );
}

.profile-page::after{
  content:'';
  position:absolute;inset:0;pointer-events:none;
  background:
    radial-gradient(ellipse 60% 50% at 85% 15%, rgba(52,176,104,.2) 0%, transparent 65%),
    radial-gradient(ellipse 40% 40% at 10% 85%, rgba(150,211,94,.1) 0%, transparent 60%);
}

/* blobs — identical to contact.php */
.blob{
  position:absolute;border-radius:50%;
  filter:blur(90px);opacity:.14;
  animation:drift 9s ease-in-out infinite alternate;
  pointer-events:none;
}
.blob-1{width:420px;height:420px;background:var(--sage); top:-100px;right:-80px;animation-delay:0s}
.blob-2{width:260px;height:260px;background:var(--lime); bottom:-40px;left:6%;  animation-delay:4s}
.blob-3{width:180px;height:180px;background:var(--lime); top:40%;   left:45%;  animation-delay:6s}

@keyframes drift{
  from{transform:translate(0,0) scale(1)}
  to  {transform:translate(20px,-28px) scale(1.05)}
}

/* ══════════════════════════════════════
   PROFILE CARD (same max-width as contact)
══════════════════════════════════════ */
.profile-card{
  position:relative;z-index:2;
  width:100%;max-width:640px;
  background:rgba(250,253,247,.04);
  border:1px solid rgba(100,190,90,.15);
  border-radius:16px;
  overflow:hidden;
  backdrop-filter:blur(6px);
  animation:cardIn .7s ease both;
}

@keyframes cardIn{
  from{opacity:0;transform:translateY(28px)}
  to  {opacity:1;transform:translateY(0)}
}

/* card header */
.card-header{
  background:linear-gradient(135deg,rgba(150,211,94,.1) 0%,rgba(52,176,104,.06) 100%);
  border-bottom:1px solid rgba(100,190,90,.13);
  padding:48px 52px 40px;
}

.header-label{
  font-size:10px;font-weight:500;
  letter-spacing:.28em;text-transform:uppercase;
  color:var(--lime);
  display:flex;align-items:center;gap:10px;
  margin-bottom:14px;
}
.header-label::before{
  content:'';display:block;
  width:22px;height:1px;background:var(--lime);
}

/* avatar + name row */
.header-profile-row{
  display:flex;align-items:center;gap:20px;
  margin-bottom:12px;
}
.header-av{
  width:72px;height:72px;
  border-radius:50%;flex-shrink:0;
  background:linear-gradient(135deg,var(--sage),var(--lime));
  display:flex;align-items:center;justify-content:center;
  font-family:var(--serif);font-size:2rem;font-weight:700;color:#fff;
  border:2px solid rgba(150,211,94,.3);
  box-shadow:0 0 0 5px rgba(82,176,104,.08);
  text-transform:uppercase;
}
.header-name-wrap{}
.card-title{
  font-family:var(--serif);
  font-size:clamp(1.7rem,3vw,2.2rem);
  font-weight:700;color:var(--white);
  line-height:1.15;margin-bottom:6px;
}
.card-title em{font-style:italic;color:var(--lime)}

.role-badge{
  display:inline-flex;align-items:center;gap:6px;
  padding:4px 12px;border-radius:100px;
  background:rgba(82,176,104,.13);
  border:1px solid rgba(82,176,104,.26);
  font-size:11px;font-weight:600;
  letter-spacing:.1em;text-transform:uppercase;
  color:var(--lime);
}
.role-badge::before{content:'🌾';font-size:11px}

.card-subtitle{
  font-size:14px;line-height:1.7;
  color:rgba(250,253,247,.45);
  margin-top:10px;
}

/* card body */
.card-body{
  padding:40px 52px 48px;
  display:flex;flex-direction:column;gap:24px;
}

/* info rows — same look as contact form fields */
.info-group{
  display:flex;flex-direction:column;gap:6px;
}
.info-group-label{
  font-size:10px;font-weight:500;
  letter-spacing:.2em;text-transform:uppercase;
  color:var(--muted);
}
.info-group-value{
  background:rgba(18,42,32,.7);
  border:1px solid rgba(100,190,90,.12);
  border-radius:6px;
  padding:13px 16px;
  font-size:14px;font-weight:300;
  color:var(--white);
}
.info-group-value.mono{
  font-family:monospace;
  letter-spacing:.04em;
}

.form-row{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:20px;
}

/* member banner */
.member-banner{
  display:flex;align-items:center;gap:14px;
  background:rgba(82,176,104,.08);
  border:1px solid rgba(82,176,104,.18);
  border-radius:8px;
  padding:14px 18px;
  font-size:13px;color:rgba(250,253,247,.5);
}
.member-banner span{font-size:22px;flex-shrink:0}
.member-banner strong{display:block;color:var(--lime);font-weight:500;margin-bottom:2px}

/* action buttons — same style as contact's submit */
.card-actions{
  display:flex;gap:10px;flex-wrap:wrap;
}
.btn-action{
  display:inline-flex;align-items:center;gap:10px;
  padding:13px 28px;
  font-family:var(--sans);font-size:12px;font-weight:500;
  letter-spacing:.12em;text-transform:uppercase;
  border-radius:4px;cursor:pointer;
  transition:background .25s,transform .2s,gap .2s;
  text-decoration:none;border:none;
  position:relative;overflow:hidden;
}
.btn-action::before{
  content:'';position:absolute;inset:0;
  transform:translateX(-101%);
  transition:transform .35s cubic-bezier(.77,0,.18,1);
}
.btn-action:hover::before{transform:translateX(0)}
.btn-action:hover{gap:16px;transform:translateY(-2px)}
.btn-action span,.btn-action svg{position:relative;z-index:1}
.btn-action svg{
  width:15px;height:15px;
  stroke:currentColor;fill:none;
  stroke-width:2;stroke-linecap:round;stroke-linejoin:round;
}

.btn-edit{background:var(--lime);color:var(--deep)}
.btn-edit::before{background:#82c249}

.btn-logout{
  background:rgba(220,60,60,.12);
  color:#ff8877;
  border:1px solid rgba(220,60,60,.22)!important;
}
.btn-logout::before{background:rgba(220,60,60,.22)}

/* ══════════════════════════════════════
   RESPONSIVE
══════════════════════════════════════ */
@media(max-width:680px){
  .card-header,.card-body{padding-left:28px;padding-right:28px}
  .form-row{grid-template-columns:1fr}
  .header-profile-row{flex-direction:column;align-items:flex-start;gap:14px}
  .card-actions{flex-direction:column}
  .btn-action{justify-content:center}
  .nav-links{display:none}
}
</style>
</head>
<body>

<?php include('../../components/header_navigation_bar.php'); ?>

<!-- ══ PAGE ══ -->
<main class="profile-page">
  <div class="blob blob-1"></div>
  <div class="blob blob-2"></div>
  <div class="blob blob-3"></div>

  <div class="profile-card">

    <!-- Card Header -->
    <div class="card-header">
      <p class="header-label">PaddyCare</p>
      <div class="header-profile-row">
        <div class="header-av"><?= htmlspecialchars($initial) ?></div>
        <div class="header-name-wrap">
          <h1 class="card-title"><?= htmlspecialchars($data['first_name']) ?>'s <em>Profile</em></h1>
          <span class="role-badge"><?= htmlspecialchars($role) ?></span>
        </div>
      </div>
      <p class="card-subtitle">
        Manage your account details and stay connected with the PaddyCare community.
      </p>
    </div>

    <!-- Card Body -->
    <div class="card-body">

      <!-- Name + Role row -->
      <div class="form-row">
        <div class="info-group">
          <p class="info-group-label">Full Name</p>
          <div class="info-group-value"><?= htmlspecialchars($username) ?></div>
        </div>
        <div class="info-group">
          <p class="info-group-label">Role</p>
          <div class="info-group-value"><?= htmlspecialchars($role) ?></div>
        </div>
      </div>

      <!-- Email + Phone row -->
      <div class="form-row">
        <div class="info-group">
          <p class="info-group-label">Email Address</p>
          <div class="info-group-value mono"><?= htmlspecialchars($data['email']) ?></div>
        </div>
        <div class="info-group">
          <p class="info-group-label">Contact Number</p>
          <div class="info-group-value mono"><?= htmlspecialchars($data['contact_number'] ?: '—') ?></div>
        </div>
      </div>

      <!-- Member since -->
      <div class="member-banner">
        <span>🌱</span>
        <div>
          <strong>Member since <?= htmlspecialchars($member_since) ?></strong>
          Thank you for being part of the PaddyCare farming community.
        </div>
      </div>

      <!-- Action buttons -->
      <div class="card-actions">
        <a href="<?= BASE_URL ?>/pages/profile/edit_user_information.php" class="btn-action btn-edit">
          <span>Edit Profile</span>
          <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        </a>
        <button class="btn-action btn-logout" onclick="logout('<?= BASE_URL ?>')">
          <span>Log Out</span>
          <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        </button>
      </div>

    </div>
  </div>
</main>

<?php
include('../../components/footer.php');
echo "<script src='" . BASE_URL . "/public/scripts/profile.js'></script>";
mysqli_close($conn);
?>
</body>
</html>