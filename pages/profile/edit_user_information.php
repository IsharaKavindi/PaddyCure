<?php
require '../../config.php';
require '../../utils/database.php';

$conn = initialize_database();
session_start();
$BASE_URL = BASE_URL;

$current_first_name = $current_last_name = $current_email = $current_contact_number = "";
$first_name = $last_name = $email = $contact_number = $password = $confirm_password = "";
$first_name_error = $last_name_error = $email_error = $contact_number_error = $password_error = $confirm_password_error = "";
$is_error = false;
$user_id = $_SESSION["user_id"];

$q = "SELECT * FROM `user` WHERE `id` = {$user_id} LIMIT 1";
$result = mysqli_query($conn, $q);

if (!$result) { echo "Error: " . mysqli_error($conn); exit(); }

$user = mysqli_fetch_assoc($result);
if (!$user) { echo "User not found"; exit(); }

$first_name = $current_first_name = $user['first_name'];
$last_name  = $current_last_name  = $user['last_name'];
$email      = $current_email      = $user['email'];
$contact_number = $current_contact_number = $user['contact_number'];

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = sanitize_input($_POST["first_name"]);
    if (!empty($_POST["first_name"]) && !preg_match("/^[a-zA-Z ]+$/", $first_name)) {
        $first_name_error = "Only letters and white space allowed";
        $is_error = true;
    }

    $last_name = sanitize_input($_POST["last_name"]);
    if (!empty($_POST["last_name"]) && !preg_match("/^[a-zA-Z ]+$/", $last_name)) {
        $last_name_error = "Only letters and white space allowed";
        $is_error = true;
    }

    $email = sanitize_input($_POST["email"]);
    if (!empty($_POST["email"]) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_error = "Invalid email format";
        $is_error = true;
    }

    $contact_number = sanitize_input($_POST["contact_number"]);
    if (!empty($_POST["contact_number"]) && !preg_match("/^\+?\d{1,3}?[-.\s]?\(?\d{1,3}\)?[-.\s]?\d{1,12}$/", $contact_number)) {
        $contact_number_error = "Invalid contact number format";
        $is_error = true;
    }

    $password         = sanitize_input($_POST["password"]);
    $confirm_password = sanitize_input($_POST["confirm_password"]);
    if (!empty($_POST["password"]) && !empty($_POST["confirm_password"])) {
        if ($password != $confirm_password) {
            $password_error = "Passwords do not match";
            $is_error = true;
        }
        if (strlen($password) < 8) {
            $password_error = "Password must be at least 8 characters";
            $is_error = true;
        }
    }

    if (!$is_error) {
        $hashed_password = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : $user['password'];
        $fn = mysqli_real_escape_string($conn, $first_name);
        $ln = mysqli_real_escape_string($conn, $last_name);
        $em = mysqli_real_escape_string($conn, $email);
        $cn = mysqli_real_escape_string($conn, $contact_number);

        $query = "UPDATE `user` SET
            `first_name` = '$fn',
            `last_name`  = '$ln',
            `email`      = '$em',
            `contact_number` = '$cn',
            `password`   = '$hashed_password',
            `updated_at` = NOW()
            WHERE `id` = {$user_id}";

        if (mysqli_query($conn, $query)) {
            header("Location: " . BASE_URL . "/pages/profile/profile.php?updated=1");
            exit();
        }
    }
}

$init = strtoupper(substr($first_name, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Edit Profile – PaddyCare</title>
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
  --border:rgba(100,190,90,.12);--border2:rgba(100,190,90,.26);
  --green:#52B068;--lime:#96D35E;
  --mist:rgba(195,228,185,.6);--dim:rgba(195,228,185,.32);
  --serif:'Cormorant Garamond',Georgia,serif;--sans:'Outfit',sans-serif;
}
html{scroll-behavior:smooth}
body{background:var(--white);color:var(--black);font-family:var(--sans);font-weight:300;min-height:100vh}
body::before{content:'';position:fixed;inset:0;z-index:0;pointer-events:none;
  background:repeating-linear-gradient(0deg,transparent,transparent 47px,rgba(80,160,70,.018) 47px,rgba(80,160,70,.018) 48px),
             repeating-linear-gradient(90deg,transparent,transparent 47px,rgba(80,160,70,.018) 47px,rgba(80,160,70,.018) 48px)}

/* PAGE */
.page1{background:var(--deep);position:relative;z-index:1;max-width:1720px;margin:0 auto;padding:48px 24px 100px}
.page{background:var(--deep);position:relative;z-index:1;max-width:720px;margin:0 auto;padding:48px 24px 100px}

/* HEADER */
.page-header{margin-bottom:32px}
.page-back{display:inline-flex;align-items:center;gap:7px;font-size:12px;color:var(--dim);text-decoration:none;margin-bottom:20px;transition:color .18s}
.page-back:hover{color:var(--lime)}
.page-back svg{width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.page-avatar{width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,var(--green),var(--lime));display:flex;align-items:center;justify-content:center;font-family:var(--serif);font-size:26px;font-weight:700;color:#fff;margin-bottom:16px;text-transform:uppercase}
.page-title{font-family:var(--serif);font-size:2rem;font-weight:700;color:#fff;line-height:1.2;margin-bottom:6px}
.page-title em{color:var(--lime);font-style:italic}
.page-sub{font-size:13px;color:var(--dim)}

/* CARD */
.form-card{background:var(--card);border:1px solid var(--border);border-radius:16px;overflow:hidden}
.form-section{padding:28px 32px;border-bottom:1px solid var(--border)}
.form-section:last-child{border-bottom:none}
.section-label{font-size:10px;font-weight:600;letter-spacing:.22em;text-transform:uppercase;color:var(--dim);margin-bottom:20px;display:flex;align-items:center;gap:10px}
.section-label::after{content:'';flex:1;height:1px;background:var(--border)}

/* GRID */
.field-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.field-row.single{grid-template-columns:1fr}

/* FIELD */
.field{display:flex;flex-direction:column;gap:6px}
.field label{font-size:11px;font-weight:500;letter-spacing:.1em;text-transform:uppercase;color:rgba(195,228,185,.45)}
.field input{background:rgba(18,42,32,.7);border:1px solid var(--border);border-radius:8px;padding:12px 15px;font-family:var(--sans);font-size:14px;font-weight:300;color:#fff;outline:none;transition:border-color .2s,background .2s}
.field input:focus{border-color:var(--border2);background:rgba(18,42,32,.9)}
.field input::placeholder{color:var(--dim)}
.field .err{font-size:11px;color:#ff9980;margin-top:2px;display:flex;align-items:center;gap:5px}
.field .err::before{content:'⚠';font-size:10px}
.field small{font-size:11px;color:var(--dim);line-height:1.5;margin-top:2px}

/* DIVIDER */
.form-divider{height:1px;background:var(--border);margin:4px 0}

/* ACTIONS */
.form-actions{padding:24px 32px;display:flex;align-items:center;justify-content:space-between;gap:12px;background:rgba(10,20,14,.4)}
.btn{display:inline-flex;align-items:center;gap:8px;padding:11px 24px;border-radius:9px;font-family:var(--sans);font-size:13px;font-weight:500;cursor:pointer;transition:all .18s;text-decoration:none;border:1px solid var(--border)}
.btn svg{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.btn-lime{background:var(--lime);color:var(--deep);border-color:var(--lime)}
.btn-lime:hover{background:#82c249;transform:translateY(-1px)}
.btn-ghost{background:transparent;color:var(--dim)}
.btn-ghost:hover{background:var(--lifted);color:var(--mist)}

/* SUCCESS BANNER */
.success-banner{background:rgba(82,176,104,.1);border:1px solid rgba(82,176,104,.25);border-radius:10px;padding:12px 16px;font-size:13px;color:var(--lime);margin-bottom:24px;display:flex;align-items:center;gap:8px}

@media(max-width:600px){.field-row{grid-template-columns:1fr}.form-section{padding:22px 20px}.form-actions{flex-direction:column;align-items:stretch}.btn{justify-content:center}}
</style>
</head>
<body>

<?php include('../../components/header_navigation_bar.php'); ?>
<div class="page1">
<div class="page">

  <!-- HEADER -->
  <div class="page-header">
    <a href="<?= BASE_URL ?>/pages/profile/profile.php" class="page-back">
      <svg viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
      Back to Profile
    </a>
    <div class="page-avatar"><?= $init ?></div>
    <h1 class="page-title">Edit <em>Profile</em></h1>
    <p class="page-sub">Update your personal information and account settings.</p>
  </div>

  <?php if (isset($_GET['updated'])): ?>
  <div class="success-banner">✓ Profile updated successfully!</div>
  <?php endif; ?>

  <form method="POST" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
    <div class="form-card">

      <!-- PERSONAL INFO -->
      <div class="form-section">
        <div class="section-label">Personal Information</div>
        <div style="display:flex;flex-direction:column;gap:16px">
          <div class="field-row">
            <div class="field">
              <label for="first_name">First Name *</label>
              <input type="text" name="first_name" id="first_name" placeholder="John" value="<?= htmlspecialchars($first_name) ?>" required>
              <?php if ($first_name_error): ?><span class="err"><?= $first_name_error ?></span><?php endif; ?>
            </div>
            <div class="field">
              <label for="last_name">Last Name *</label>
              <input type="text" name="last_name" id="last_name" placeholder="Doe" value="<?= htmlspecialchars($last_name) ?>" required>
              <?php if ($last_name_error): ?><span class="err"><?= $last_name_error ?></span><?php endif; ?>
            </div>
          </div>
          <div class="field-row single">
            <div class="field">
              <label for="email">Email Address *</label>
              <input type="email" name="email" id="email" placeholder="johndoe@example.com" value="<?= htmlspecialchars($email) ?>" required>
              <?php if ($email_error): ?><span class="err"><?= $email_error ?></span><?php endif; ?>
            </div>
          </div>
          <div class="field-row single">
            <div class="field">
              <label for="contact_number">Contact Number *</label>
              <input type="tel" name="contact_number" id="contact_number" placeholder="+94 712 345 678" value="<?= htmlspecialchars($contact_number) ?>" required>
              <?php if ($contact_number_error): ?><span class="err"><?= $contact_number_error ?></span><?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- PASSWORD -->
      <div class="form-section">
        <div class="section-label">Change Password</div>
        <div style="display:flex;flex-direction:column;gap:16px">
          <div class="field-row">
            <div class="field">
              <label for="password">New Password</label>
              <input type="password" name="password" id="password" placeholder="Min. 8 characters">
              <?php if ($password_error): ?><span class="err"><?= $password_error ?></span><?php endif; ?>
              <small>Leave blank to keep your current password.</small>
            </div>
            <div class="field">
              <label for="confirm_password">Confirm Password</label>
              <input type="password" name="confirm_password" id="confirm_password" placeholder="Repeat new password">
              <?php if ($confirm_password_error): ?><span class="err"><?= $confirm_password_error ?></span><?php endif; ?>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- ACTIONS -->
    <div class="form-actions" style="margin-top:16px;background:transparent;padding:0">
      <a href="<?= BASE_URL ?>/pages/profile/profile.php" class="btn btn-ghost">
        <svg viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Cancel
      </a>
      <button type="submit" class="btn btn-lime">
        <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        Save Changes
      </button>
    </div>
  </form>

</div>
</div>

<?php include('../../components/footer.php'); mysqli_close($conn); ?>
</body>
</html>