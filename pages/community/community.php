<?php
require '../../config.php';
require '../../utils/database.php';
require '../../utils/authenticate.php';

$conn = initialize_database();
session_start();
authenticate();

$user_id = (int)$_SESSION['user_id'];

// Ensure tables exist
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `community_post` (
    `id`         INT NOT NULL AUTO_INCREMENT UNIQUE,
    `user_id`    INT NOT NULL,
    `title`      VARCHAR(255),
    `body`       TEXT NOT NULL,
    `tag`        VARCHAR(60),
    `image`      VARCHAR(255),
    `likes`      INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME,
    PRIMARY KEY(`id`)
)");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `community_post_like` (
    `id`      INT NOT NULL AUTO_INCREMENT UNIQUE,
    `post_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    PRIMARY KEY(`id`)
)");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `community_post_comment` (
    `id`         INT NOT NULL AUTO_INCREMENT UNIQUE,
    `post_id`    INT NOT NULL,
    `user_id`    INT NOT NULL,
    `body`       TEXT NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME,
    PRIMARY KEY(`id`)
)");

// ── HANDLE DELETE ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post_id'])) {
    $del_id = (int)$_POST['delete_post_id'];
    mysqli_query($conn, "UPDATE community_post SET deleted_at = NOW() WHERE id = $del_id AND user_id = $user_id");
    header('Location: ' . BASE_URL . '/pages/community/community.php');
    exit();
}

// ── HANDLE EDIT ───────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_post_id'])) {
    $edit_id    = (int)$_POST['edit_post_id'];
    $edit_title = trim($_POST['edit_title'] ?? '');
    $edit_body  = trim($_POST['edit_body']  ?? '');
    $edit_tag   = trim($_POST['edit_tag']   ?? '');

    if ($edit_body) {
        $stmt = mysqli_prepare($conn,
            "UPDATE community_post SET title=?, body=?, tag=? WHERE id=? AND user_id=?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'sssii', $edit_title, $edit_body, $edit_tag, $edit_id, $user_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
    header('Location: ' . BASE_URL . '/pages/community/community.php');
    exit();
}

// ── HANDLE NEW POST ───────────────────────────────────────────
$post_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_post'])) {
    $title = trim($_POST['title'] ?? '');
    $body  = trim($_POST['body']  ?? '');
    $tag   = trim($_POST['tag']   ?? '');
    $image_filename = '';

    if ($body) {
        if (!empty($_FILES['image']['name'])) {
            $upload_dir = __DIR__ . '/../../public/uploads/community/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif','webp'];
            if (in_array($ext, $allowed)) {
                $filename = uniqid('post_') . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename);
                $image_filename = $filename;
            }
        }

        $stmt = mysqli_prepare($conn,
            "INSERT INTO community_post (user_id, title, body, tag, image) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            $post_error = mysqli_error($conn);
        } else {
            mysqli_stmt_bind_param($stmt, 'issss', $user_id, $title, $body, $tag, $image_filename);
            if (!mysqli_stmt_execute($stmt)) {
                $post_error = mysqli_stmt_error($stmt);
            } else {
                mysqli_stmt_close($stmt);
                header('Location: ' . BASE_URL . '/pages/community/community.php?posted=1');
                exit();
            }
        }
    }
}

// ── FETCH POSTS ───────────────────────────────────────────────
$posts = mysqli_query($conn, "
    SELECT p.id, p.title, p.body, p.tag, p.image, p.likes, p.created_at, p.user_id AS post_user_id,
           u.first_name, u.last_name,
           (SELECT COUNT(*) FROM community_post_like WHERE post_id = p.id AND user_id = $user_id) AS user_liked,
           (SELECT COUNT(*) FROM community_post_comment WHERE post_id = p.id AND deleted_at IS NULL) AS comment_count
    FROM community_post p
    INNER JOIN user u ON u.id = p.user_id
    WHERE p.deleted_at IS NULL
    ORDER BY p.created_at DESC
");

$me      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT first_name, last_name FROM user WHERE id = $user_id"));
$me_init = strtoupper(substr($me['first_name'] ?? 'U', 0, 1));

$tag_counts = [];
$tc = mysqli_query($conn, "SELECT tag, COUNT(*) as cnt FROM community_post WHERE deleted_at IS NULL AND tag != '' GROUP BY tag ORDER BY cnt DESC LIMIT 8");
while ($r = mysqli_fetch_assoc($tc)) $tag_counts[] = $r;

function time_ago($dt) {
    $d = time() - strtotime($dt);
    if ($d < 60)     return 'just now';
    if ($d < 3600)   return floor($d/60)   . 'm ago';
    if ($d < 86400)  return floor($d/3600) . 'h ago';
    if ($d < 604800) return floor($d/86400). 'd ago';
    return date('M j', strtotime($dt));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Community – PaddyCare</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/public/styles/styles.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/public/styles/fonts.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="shortcut icon" href="<?php echo BASE_URL; ?>/public/images/paddyCureLogo2.png" type="image/x-icon">
<style>
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
body{background:var(--white);color:var(--deep);font-family:var(--sans);font-weight:300;min-height:100vh}
body::before{content:'';position:fixed;inset:0;z-index:0;pointer-events:none;
  background:repeating-linear-gradient(0deg,transparent,transparent 47px,rgba(80,160,70,.018) 47px,rgba(80,160,70,.018) 48px),
             repeating-linear-gradient(90deg,transparent,transparent 47px,rgba(80,160,70,.018) 47px,rgba(80,160,70,.018) 48px)}


/* LAYOUT */
.page{background:var(--deep);position:relative;z-index:1;max-width:2000px;margin:0 auto;padding:32px 120px 80px;display:grid;grid-template-columns:200px 1fr;gap:20px}

/* LEFT SIDEBAR */
.sidebar{display:flex;flex-direction:column;gap:14px}
.sb-card{color:var(--mist);background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px}
.sb-title{color:var(--mist);font-size:10px;font-weight:600;letter-spacing:.2em;text-transform:uppercase;color:var(--dim);margin-bottom:12px}
.sb-tags{color:var(--mist);display:flex;flex-wrap:wrap;gap:6px}
.sb-tag{font-size:11px;padding:4px 10px;border-radius:100px;background:rgba(82,176,104,.08);border:1px solid var(--border);color:var(--dim);cursor:pointer;transition:all .18s;text-decoration:none}
.sb-tag:hover{background:rgba(82,176,104,.16);color:var(--lime);border-color:var(--border2)}
.sb-stat{display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid rgba(100,190,90,.06);font-size:12px}
.sb-stat:last-child{border-bottom:none}
.sb-stat-val{font-family:var(--serif);font-size:1.1rem;font-weight:700;color:#fff}
.sb-new-post-btn{display:flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:14px;border-radius:12px;background:var(--lime);color:var(--deep);border:none;font-family:var(--sans);font-size:13px;font-weight:600;cursor:pointer;transition:background .2s,transform .2s;letter-spacing:.04em}
.sb-new-post-btn:hover{background:#82c249;transform:translateY(-2px)}
.sb-new-post-btn svg{width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:2.2;stroke-linecap:round;stroke-linejoin:round}

/* FEED */
.feed{display:flex;flex-direction:column;gap:16px}
.new-post-box{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:16px 18px;display:flex;align-items:center;gap:12px;cursor:pointer;transition:border-color .2s}
.new-post-box:hover{border-color:var(--border2)}
.np-av{width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,var(--green),var(--lime));display:flex;align-items:center;justify-content:center;font-family:var(--serif);font-size:15px;font-weight:700;color:#fff;flex-shrink:0;text-transform:uppercase}
.np-input{flex:1;background:rgba(18,42,32,.5);border:1px solid var(--border);border-radius:100px;padding:10px 16px;font-family:var(--sans);font-size:13px;color:var(--dim)}
.np-input:hover{border-color:var(--border2);color:var(--mist)}

/* POST CARD */
.post-card{background:var(--card);border:1px solid var(--border);border-radius:14px;overflow:hidden;animation:riseUp .5s cubic-bezier(.22,1,.36,1) both;transition:border-color .2s; width: 1000px;}
.post-card:hover{border-color:rgba(100,190,90,.22)}
@keyframes riseUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:none}}
.post-head{display:flex;align-items:center;gap:10px;padding:16px 20px 12px}
.post-av{width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,var(--green),var(--lime));display:flex;align-items:center;justify-content:center;font-family:var(--serif);font-size:16px;font-weight:700;color:#fff;flex-shrink:0;text-transform:uppercase}
.post-meta{flex:1;min-width:0}
.post-name{font-size:13px;font-weight:600;color:#fff}
.post-time{font-size:11px;color:var(--dim)}
.post-tag{font-size:10px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;padding:3px 10px;border-radius:100px;background:rgba(82,176,104,.12);border:1px solid rgba(82,176,104,.22);color:var(--lime)}
.post-actions{display:flex;align-items:center;gap:5px;margin-left:auto;flex-shrink:0}
.post-act-btn{display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:8px;background:none;cursor:pointer;transition:all .18s;flex-shrink:0;border:none}
.post-act-btn svg{width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.btn-edit{border:1px solid rgba(82,176,104,.2);color:rgba(150,211,94,.45)}
.btn-edit:hover{background:rgba(82,176,104,.12);color:var(--lime);border-color:rgba(82,176,104,.4)}
.btn-delete{border:1px solid rgba(220,55,55,.2);color:rgba(255,100,80,.4)}
.btn-delete:hover{background:rgba(220,55,55,.12);color:#ff8877;border-color:rgba(220,55,55,.4)}

.post-title{font-family:var(--serif);font-size:1.2rem;font-weight:600;color:#fff;padding:0 20px 8px;line-height:1.3}
.post-body{font-size:14px;line-height:1.75;color:rgba(195,228,185,.55);padding:0 20px 14px}
.post-img{width:100%;max-height:420px;object-fit:cover;border-top:1px solid var(--border);border-bottom:1px solid var(--border)}
.post-foot{display:flex;align-items:center;gap:4px;padding:10px 16px}
.pf-btn{display:inline-flex;align-items:center;gap:6px;padding:7px 12px;border-radius:8px;font-family:var(--sans);font-size:12px;font-weight:500;color:var(--dim);background:none;border:none;cursor:pointer;transition:all .18s}
.pf-btn svg{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.pf-btn:hover{background:rgba(82,176,104,.1);color:var(--lime)}
.pf-btn.liked{color:var(--lime)}
.pf-btn.liked svg{fill:var(--lime)}
.pf-btn.comment-active{background:rgba(82,176,104,.1);color:var(--lime)}

/* COMMENTS */
.comments-section{border-top:1px solid var(--border);display:none}
.comments-section.open{display:block}
.comments-list{padding:14px 20px;display:flex;flex-direction:column;gap:10px;max-height:340px;overflow-y:auto;scrollbar-width:thin;scrollbar-color:rgba(82,176,104,.2) transparent}
.comments-list::-webkit-scrollbar{width:4px}
.comments-list::-webkit-scrollbar-thumb{background:rgba(82,176,104,.2);border-radius:2px}
.comment-item{display:flex;gap:10px;animation:riseUp .3s ease both}
.c-av{width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,var(--green),var(--lime));display:flex;align-items:center;justify-content:center;font-family:var(--serif);font-size:12px;font-weight:700;color:#fff;flex-shrink:0;text-transform:uppercase}
.c-bubble{background:rgba(18,42,32,.7);border:1px solid var(--border);border-radius:10px;padding:9px 13px;flex:1;min-width:0}
.c-name{font-size:11px;font-weight:600;color:rgba(195,228,185,.7);margin-bottom:3px}
.c-body{font-size:13px;line-height:1.6;color:rgba(195,228,185,.55);word-break:break-word}
.c-time{font-size:10px;color:var(--dim);margin-top:4px}
.c-loading{text-align:center;padding:14px;font-size:12px;color:var(--dim)}
.comment-input-row{display:flex;align-items:center;gap:10px;padding:10px 20px 14px;border-top:1px solid rgba(100,190,90,.06)}
.c-input{flex:1;background:rgba(18,42,32,.6);border:1px solid var(--border);border-radius:100px;padding:9px 16px;font-family:var(--sans);font-size:13px;color:var(--mist);outline:none;transition:border-color .2s}
.c-input:focus{border-color:var(--border2)}
.c-input::placeholder{color:var(--dim)}
.c-send{width:34px;height:34px;border-radius:50%;background:var(--lime);border:none;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;transition:background .18s}
.c-send:hover{background:#82c249}
.c-send svg{width:14px;height:14px;stroke:var(--deep);fill:none;stroke-width:2.2;stroke-linecap:round;stroke-linejoin:round}

/* MODALS — shared base */
.overlay,.edit-overlay{position:fixed;inset:0;z-index:1000;background:rgba(4,10,6,.92);backdrop-filter:blur(10px);display:flex;align-items:center;justify-content:center;padding:24px;opacity:0;pointer-events:none;transition:opacity .25s}
.overlay.open,.edit-overlay.open{opacity:1;pointer-events:all}
.modal,.edit-modal{background:var(--panel);border-radius:18px;width:100%;max-width:560px;transform:translateY(22px);transition:transform .3s cubic-bezier(.34,1.2,.64,1);max-height:90vh;overflow-y:auto}
.overlay.open .modal,.edit-overlay.open .edit-modal{transform:none}
.modal{border:1px solid rgba(82,176,104,.2)}
.edit-modal{border:1px solid rgba(150,211,94,.3)}
.edit-overlay{z-index:1001}
.mh{display:flex;align-items:center;justify-content:space-between;padding:20px 24px 16px;border-bottom:1px solid var(--border)}
.mh-title{font-family:var(--serif);font-size:1.2rem;font-weight:600;color:#fff}
.mclose{width:30px;height:30px;border-radius:50%;background:var(--card);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--dim);transition:all .18s;flex-shrink:0}
.mclose:hover{background:rgba(220,55,55,.14);color:#ff8877}
.mclose svg{width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round}
.mbody{padding:20px 24px 24px;display:flex;flex-direction:column;gap:16px}
.mfield label{font-size:10px;font-weight:500;letter-spacing:.2em;text-transform:uppercase;color:var(--dim);display:block;margin-bottom:7px}
.mfield input,.mfield textarea,.mfield select{width:100%;background:rgba(18,42,32,.7);border:1px solid var(--border);border-radius:8px;padding:12px 15px;font-family:var(--sans);font-size:14px;font-weight:300;color:#fff;outline:none;transition:border-color .2s;appearance:none}
.mfield textarea{resize:none}
.mfield input:focus,.mfield textarea:focus,.mfield select:focus{border-color:var(--border2)}
.mfield select option{background:var(--deep)}
.sel-wrap{position:relative}
.sel-wrap::after{content:'▾';position:absolute;right:13px;top:50%;transform:translateY(-50%);font-size:11px;color:var(--lime);pointer-events:none}
.drop-zone{border:2px dashed rgba(82,176,104,.22);border-radius:10px;padding:28px;text-align:center;cursor:pointer;transition:all .2s;color:var(--dim);font-size:13px}
.drop-zone:hover,.drop-zone.dragover{border-color:var(--lime);background:rgba(82,176,104,.06);color:var(--lime)}
.drop-zone svg{width:28px;height:28px;stroke:currentColor;fill:none;stroke-width:1.5;stroke-linecap:round;stroke-linejoin:round;display:block;margin:0 auto 8px}
#imgPreview{display:none;width:100%;border-radius:8px;max-height:180px;object-fit:cover;border:1px solid var(--border)}
.merr{background:rgba(220,80,60,.1);border:1px solid rgba(220,80,60,.25);border-radius:8px;padding:10px 14px;font-size:13px;color:#ff9980;display:none}
.mfoot{display:flex;justify-content:flex-end;gap:10px;padding:0 24px 20px}
.mbtn{display:inline-flex;align-items:center;gap:7px;padding:10px 22px;border-radius:8px;font-family:var(--sans);font-size:13px;font-weight:500;cursor:pointer;transition:all .18s;border:1px solid var(--border)}
.mbtn-lime{background:var(--lime);color:var(--deep);border-color:var(--lime)}
.mbtn-lime:hover{background:#82c249}
.mbtn-ghost{background:transparent;color:var(--dim)}
.mbtn-ghost:hover{background:var(--lifted);color:var(--mist)}

.no-posts{text-align:center;padding:60px 20px;color:var(--dim)}
.no-posts-icon{font-size:48px;margin-bottom:14px}
.fab{display:none;position:fixed;bottom:28px;right:28px;z-index:100;width:52px;height:52px;border-radius:50%;background:var(--lime);color:var(--deep);border:none;font-size:24px;cursor:pointer;align-items:center;justify-content:center;box-shadow:0 4px 24px rgba(150,211,94,.35);transition:transform .2s}
.fab:hover{transform:scale(1.1)}

@media(max-width:900px){.page{grid-template-columns:1fr}.sidebar{display:none}.fab{display:flex}}
@media(max-width:700px){.page{padding:16px 12px 80px}.fab{display:flex}.nav-links{display:none}}
</style>
</head>
<body>

<?php include('../../components/header_navigation_bar.php'); ?>

<!-- NEW POST MODAL -->
<div class="overlay" id="overlay">
  <div class="modal">
    <div class="mh">
      <span class="mh-title">Share with the Community</span>
      <button class="mclose" onclick="closeModal()">
        <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <form method="POST" action="<?= BASE_URL ?>/pages/community/community.php" enctype="multipart/form-data">
      <input type="hidden" name="new_post" value="1">
      <div class="mbody">
        <?php if ($post_error): ?>
        <div class="merr" style="display:block"><?= htmlspecialchars($post_error) ?></div>
        <?php endif; ?>
        <div class="mfield">
          <label>Title (optional)</label>
          <input type="text" name="title" placeholder="Give your post a title…">
        </div>
        <div class="mfield">
          <label>What's on your field? *</label>
          <textarea name="body" rows="4" placeholder="Share a tip, ask a question, or describe an issue with your crop…" required></textarea>
        </div>
        <div class="mfield">
          <label>Category</label>
          <div class="sel-wrap">
            <select name="tag">
              <option value="">Select a category…</option>
              <option value="Disease">🔬 Disease</option>
              <option value="Pest Control">🐛 Pest Control</option>
              <option value="Fertilizer">🌱 Fertilizer</option>
              <option value="Irrigation">💧 Irrigation</option>
              <option value="Harvest">🌾 Harvest</option>
              <option value="Weather">⛅ Weather</option>
              <option value="General">💬 General</option>
            </select>
          </div>
        </div>
        <div class="mfield">
          <label>Photo (optional)</label>
          <div class="drop-zone" id="dropZone" onclick="document.getElementById('imgFile').click()">
            <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            Click or drag to upload a photo
          </div>
          <input type="file" name="image" id="imgFile" accept="image/*" style="display:none">
          <img id="imgPreview" alt="preview">
        </div>
      </div>
      <div class="mfoot">
        <button type="button" class="mbtn mbtn-ghost" onclick="closeModal()">Cancel</button>
        <button type="submit" class="mbtn mbtn-lime">Post to Community</button>
      </div>
    </form>
  </div>
</div>

<!-- EDIT POST MODAL -->
<div class="edit-overlay" id="editOverlay">
  <div class="edit-modal">
    <div class="mh">
      <span class="mh-title">Edit Post</span>
      <button class="mclose" onclick="closeEditModal()">
        <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <form method="POST" action="<?= BASE_URL ?>/pages/community/community.php" id="editForm">
      <input type="hidden" name="edit_post_id" id="editPostId">
      <div class="mbody">
        <div class="mfield">
          <label>Title</label>
          <input type="text" name="edit_title" id="editTitle" placeholder="Post title…">
        </div>
        <div class="mfield">
          <label>Content *</label>
          <textarea name="edit_body" id="editBody" rows="5" required></textarea>
        </div>
        <div class="mfield">
          <label>Category</label>
          <div class="sel-wrap">
            <select name="edit_tag" id="editTag">
              <option value="">Select a category…</option>
              <option value="Disease">🔬 Disease</option>
              <option value="Pest Control">🐛 Pest Control</option>
              <option value="Fertilizer">🌱 Fertilizer</option>
              <option value="Irrigation">💧 Irrigation</option>
              <option value="Harvest">🌾 Harvest</option>
              <option value="Weather">⛅ Weather</option>
              <option value="General">💬 General</option>
            </select>
          </div>
        </div>
      </div>
      <div class="mfoot">
        <button type="button" class="mbtn mbtn-ghost" onclick="closeEditModal()">Cancel</button>
        <button type="submit" class="mbtn mbtn-lime">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<!-- PAGE -->
<div class="page">

  <!-- LEFT SIDEBAR -->
  <aside class="sidebar">
    <button class="sb-new-post-btn" onclick="openModal()">
      <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      New Post
    </button>
    <div class="sb-card">
      <div class="sb-title">Categories</div>
      <div class="sb-tags">
        <a href="#" class="sb-tag" onclick="return filterTag('')">All</a>
        <?php foreach ($tag_counts as $t): ?>
        <a href="#" class="sb-tag" onclick="return filterTag('<?= htmlspecialchars($t['tag']) ?>')"><?= htmlspecialchars($t['tag']) ?> <span style="opacity:.5"><?= $t['cnt'] ?></span></a>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="sb-card">
      <div class="sb-title">Community</div>
      <?php
        $stats = mysqli_fetch_assoc(mysqli_query($conn, "
          SELECT
            (SELECT COUNT(*) FROM community_post WHERE deleted_at IS NULL) AS posts,
            (SELECT COUNT(*) FROM user WHERE deleted_at IS NULL) AS members
        "));
      ?>
      <div class="sb-stat"><span>Members</span><span class="sb-stat-val"><?= number_format((int)$stats['members']) ?></span></div>
      <div class="sb-stat"><span>Posts</span><span class="sb-stat-val"><?= number_format((int)$stats['posts']) ?></span></div>
    </div>
  </aside>

  <!-- FEED -->
  <main class="feed" id="feed">

    <div class="new-post-box" onclick="openModal()">
      <div class="np-av"><?= $me_init ?></div>
      <div class="np-input">What's happening on your field today?</div>
    </div>

    <?php if (isset($_GET['posted'])): ?>
    <div style="background:rgba(82,176,104,.1);border:1px solid rgba(82,176,104,.25);border-radius:12px;padding:12px 16px;font-size:13px;color:var(--lime)">
      ✓ Your post was shared with the community!
    </div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($posts) === 0): ?>
    <div class="no-posts">
      <div class="no-posts-icon">🌾</div>
      <p>No posts yet. Be the first to share!</p>
    </div>
    <?php else:
      $i = 0;
      while ($p = mysqli_fetch_assoc($posts)):
        $init     = strtoupper(substr($p['first_name'], 0, 1));
        $liked    = (bool)$p['user_liked'];
        $is_owner = ((int)$p['post_user_id'] === $user_id);
    ?>
    <div class="post-card" style="animation-delay:<?= $i * 0.07 ?>s" data-tag="<?= htmlspecialchars($p['tag']) ?>" data-id="<?= $p['id'] ?>">
      <div class="post-head">
        <div class="post-av"><?= htmlspecialchars($init) ?></div>
        <div class="post-meta">
          <div class="post-name"><?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?></div>
          <div class="post-time"><?= time_ago($p['created_at']) ?></div>
        </div>
        <?php if ($p['tag']): ?>
        <span class="post-tag"><?= htmlspecialchars($p['tag']) ?></span>
        <?php endif; ?>

        <?php if ($is_owner): ?>
        <div class="post-actions">
          <!-- EDIT BUTTON — uses json_encode for safe JS passing -->
          <button type="button" class="post-act-btn btn-edit" title="Edit post"
            data-id="<?= $p['id'] ?>"
            data-title="<?= htmlspecialchars($p['title'], ENT_QUOTES) ?>"
            data-body="<?= htmlspecialchars($p['body'], ENT_QUOTES) ?>"
            data-tag="<?= htmlspecialchars($p['tag'], ENT_QUOTES) ?>"
            onclick="openEditModal(this)">
            <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
          </button>
          <!-- DELETE BUTTON -->
          <form method="POST" action="<?= BASE_URL ?>/pages/community/community.php" style="display:inline" onsubmit="return confirm('Delete this post?')">
            <input type="hidden" name="delete_post_id" value="<?= $p['id'] ?>">
            <button type="submit" class="post-act-btn btn-delete" title="Delete post">
              <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
            </button>
          </form>
        </div>
        <?php endif; ?>
      </div>

      <?php if ($p['title']): ?>
      <div class="post-title"><?= htmlspecialchars($p['title']) ?></div>
      <?php endif; ?>
      <div class="post-body"><?= nl2br(htmlspecialchars($p['body'])) ?></div>
      <?php if ($p['image']): ?>
      <img class="post-img" src="<?= BASE_URL ?>/public/uploads/community/<?= htmlspecialchars($p['image']) ?>" alt="post image" loading="lazy">
      <?php endif; ?>

      <div class="post-foot">
        <button class="pf-btn <?= $liked ? 'liked' : '' ?>" onclick="toggleLike(this, <?= $p['id'] ?>)">
          <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
          <span class="like-count"><?= (int)$p['likes'] ?></span>
        </button>
        <button class="pf-btn" onclick="toggleComments(this, <?= $p['id'] ?>)">
          <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
          <span class="comment-count"><?= (int)$p['comment_count'] ?></span>
        </button>
      </div>

      <!-- COMMENTS -->
      <div class="comments-section" id="comments-<?= $p['id'] ?>">
        <div class="comments-list" id="comments-list-<?= $p['id'] ?>">
          <div class="c-loading">Loading comments…</div>
        </div>
        <div class="comment-input-row">
          <div class="c-av"><?= $me_init ?></div>
          <input class="c-input" type="text" placeholder="Write a comment…"
            onkeydown="if(event.key==='Enter'&&!event.shiftKey){submitComment(this,<?= $p['id'] ?>);event.preventDefault()}">
          <button class="c-send" onclick="submitComment(this.previousElementSibling,<?= $p['id'] ?>)">
            <svg viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
          </button>
        </div>
      </div>
    </div>
    <?php $i++; endwhile; endif; ?>
  </main>

</div>

<button class="fab" onclick="openModal()">+</button>

<?php include('../../components/footer.php'); ?>

<script>
const COMMENT_URL = '<?= BASE_URL ?>/pages/community/comment_handler.php';
const LIKE_URL    = '<?= BASE_URL ?>/pages/community/like_handler.php';

// ── NEW POST MODAL ────────────────────────────────────────────
function openModal()  { document.getElementById('overlay').classList.add('open'); }
function closeModal() { document.getElementById('overlay').classList.remove('open'); }
document.getElementById('overlay').addEventListener('click', e => {
  if (e.target === document.getElementById('overlay')) closeModal();
});

// ── EDIT MODAL ────────────────────────────────────────────────
// Data stored in data-* attributes on the button — no inline JS escaping issues
function openEditModal(btn) {
  document.getElementById('editPostId').value = btn.dataset.id;
  document.getElementById('editTitle').value  = btn.dataset.title;
  document.getElementById('editBody').value   = btn.dataset.body;
  document.getElementById('editTag').value    = btn.dataset.tag;
  document.getElementById('editOverlay').classList.add('open');
}
function closeEditModal() {
  document.getElementById('editOverlay').classList.remove('open');
}
document.getElementById('editOverlay').addEventListener('click', e => {
  if (e.target === document.getElementById('editOverlay')) closeEditModal();
});
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') { closeModal(); closeEditModal(); }
});

// ── IMAGE DRAG DROP ───────────────────────────────────────────
const dropZone = document.getElementById('dropZone');
const imgFile  = document.getElementById('imgFile');
const imgPrev  = document.getElementById('imgPreview');
imgFile.addEventListener('change', () => showPreview(imgFile.files[0]));
dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('dragover'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
dropZone.addEventListener('drop', e => {
  e.preventDefault(); dropZone.classList.remove('dragover');
  const f = e.dataTransfer.files[0];
  if (f) { const dt = new DataTransfer(); dt.items.add(f); imgFile.files = dt.files; showPreview(f); }
});
function showPreview(file) {
  if (!file) return;
  const r = new FileReader();
  r.onload = e => { imgPrev.src = e.target.result; imgPrev.style.display = 'block'; dropZone.style.display = 'none'; };
  r.readAsDataURL(file);
}

// ── LIKE ──────────────────────────────────────────────────────
function toggleLike(btn, postId) {
  const span  = btn.querySelector('.like-count');
  const liked = btn.classList.contains('liked');
  btn.classList.toggle('liked');
  span.textContent = liked ? parseInt(span.textContent) - 1 : parseInt(span.textContent) + 1;
  fetch(LIKE_URL, {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'post_id=' + postId
  })
  .then(r => r.json())
  .then(d => { if (d.likes !== undefined) span.textContent = d.likes; })
  .catch(() => {
    btn.classList.toggle('liked');
    span.textContent = liked ? parseInt(span.textContent)+1 : parseInt(span.textContent)-1;
  });
}

// ── SEARCH ────────────────────────────────────────────────────
document.getElementById('searchInput').addEventListener('input', function() {
  const q = this.value.toLowerCase();
  document.querySelectorAll('.post-card').forEach(card => {
    card.style.display = card.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
});

// ── TAG FILTER ────────────────────────────────────────────────
function filterTag(tag) {
  document.querySelectorAll('.post-card').forEach(card => {
    card.style.display = (!tag || card.dataset.tag === tag) ? '' : 'none';
  });
  return false;
}

// ── COMMENTS ─────────────────────────────────────────────────
const loadedPosts = new Set();

function toggleComments(btn, postId) {
  const section = document.getElementById('comments-' + postId);
  const opening = !section.classList.contains('open');
  section.classList.toggle('open');
  btn.classList.toggle('comment-active', opening);
  if (opening && !loadedPosts.has(postId)) fetchComments(postId);
}

function fetchComments(postId) {
  fetch(COMMENT_URL + '?action=fetch&post_id=' + postId)
    .then(r => r.json())
    .then(d => {
      loadedPosts.add(postId);
      const list = document.getElementById('comments-list-' + postId);
      if (!d.success) { list.innerHTML = '<div class="c-loading">Could not load comments.</div>'; return; }
      renderComments(postId, d.comments);
    })
    .catch(() => {
      document.getElementById('comments-list-' + postId).innerHTML = '<div class="c-loading">Could not load comments.</div>';
    });
}

function renderComments(postId, comments) {
  const list = document.getElementById('comments-list-' + postId);
  if (!comments.length) {
    list.innerHTML = '<div class="c-loading">No comments yet — be the first!</div>';
    return;
  }
  list.innerHTML = comments.map(c => buildComment(c)).join('');
  list.scrollTop = list.scrollHeight;
}

function buildComment(c) {
  return `<div class="comment-item" id="comment-${c.id}">
    <div class="c-av">${esc(c.first_name).charAt(0).toUpperCase()}</div>
    <div class="c-bubble">
      <div class="c-name">${esc(c.first_name)} ${esc(c.last_name)}</div>
      <div class="c-body">${esc(c.body)}</div>
      <div class="c-time">${timeAgo(c.created_at)}</div>
    </div>
  </div>`;
}

function submitComment(input, postId) {
  const body = input.value.trim();
  if (!body) return;
  input.value = '';
  input.disabled = true;
  const fd = new FormData();
  fd.append('action',  'post');
  fd.append('post_id', postId);
  fd.append('body',    body);
  fetch(COMMENT_URL, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => {
      input.disabled = false;
      if (!d.success) return;
      const list = document.getElementById('comments-list-' + postId);
      const ph = list.querySelector('.c-loading');
      if (ph) ph.remove();
      list.insertAdjacentHTML('beforeend', buildComment(d.comment));
      list.scrollTop = list.scrollHeight;
      loadedPosts.add(postId);
      const card = document.querySelector(`.post-card[data-id="${postId}"]`);
      if (card) {
        const span = card.querySelector('.comment-count');
        span.textContent = parseInt(span.textContent) + 1;
      }
    })
    .catch(() => { input.disabled = false; });
}

function esc(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function timeAgo(dt) {
  const d = Math.floor((Date.now() - new Date(dt)) / 1000);
  if (d < 60)    return 'just now';
  if (d < 3600)  return Math.floor(d/60)   + 'm ago';
  if (d < 86400) return Math.floor(d/3600) + 'h ago';
  return Math.floor(d/86400) + 'd ago';
}
</script>
</body>
</html>