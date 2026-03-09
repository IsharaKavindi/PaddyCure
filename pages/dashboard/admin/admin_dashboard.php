<?php
require '../../../config.php';
require '../../../utils/database.php';
require '../../../utils/authenticate.php';

$conn = initialize_database();
session_start();
authenticate(array('ADMIN'));

$query = <<<SQL
SELECT
    (SELECT COUNT(*) FROM `user` WHERE deleted_at IS NULL) AS total_users,
    (SELECT COUNT(*) FROM `user` WHERE deleted_at IS NULL AND DATE(created_at) = CURDATE()) AS new_users_today,
    (SELECT COUNT(*) FROM `contact_messages`) AS total_messages,
    (SELECT COUNT(*) FROM `contact_messages` WHERE DATE(created_at) = CURDATE()) AS new_messages_today,
    -- (SELECT COUNT(*) FROM `community_post` WHERE deleted_at IS NULL) AS total_posts,
    -- (SELECT COUNT(*) FROM `community_post` WHERE deleted_at IS NULL AND DATE(created_at) = CURDATE()) AS new_posts_today,
    (SELECT COUNT(*) FROM `article` WHERE deleted_at IS NULL) AS total_articles
SQL;

$result = mysqli_query($conn, $query);
if (!$result) { echo "Error: " . mysqli_error($conn); exit(); }
$data = mysqli_fetch_assoc($result);

// Build recent activity from all tables
$activity = [];

$r = mysqli_query($conn, "SELECT first_name, last_name, created_at FROM `user` WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 5");
while ($row = mysqli_fetch_assoc($r)) {
    $activity[] = ['icon'=>'person_add','color'=>'#4A7C59','label'=>'New user registered','detail'=>htmlspecialchars($row['first_name'].' '.$row['last_name']),'time'=>$row['created_at']];
}

$r = mysqli_query($conn, "SELECT name, subject, created_at FROM `contact_messages` ORDER BY created_at DESC LIMIT 5");
while ($row = mysqli_fetch_assoc($r)) {
    $activity[] = ['icon'=>'mail','color'=>'#1565C0','label'=>'New contact message','detail'=>htmlspecialchars($row['name']).' — '.htmlspecialchars($row['subject']),'time'=>$row['created_at']];
}

// $r = mysqli_query($conn, "SELECT cp.title, cp.body, cp.created_at, u.first_name FROM community_post cp LEFT JOIN `user` u ON u.id = cp.user_id WHERE cp.deleted_at IS NULL ORDER BY cp.created_at DESC LIMIT 5");
// while ($row = mysqli_fetch_assoc($r)) {
//     $label = $row['title'] ?: mb_substr(strip_tags($row['body']), 0, 50).'…';
//     $activity[] = ['icon'=>'forum','color'=>'#6B4226','label'=>'New community post','detail'=>htmlspecialchars($row['first_name']).' — '.htmlspecialchars($label),'time'=>$row['created_at']];
// }

$r = mysqli_query($conn, "SELECT title, created_at FROM article WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 3");
while ($row = mysqli_fetch_assoc($r)) {
    $activity[] = ['icon'=>'article','color'=>'#7B1FA2','label'=>'Article published','detail'=>htmlspecialchars($row['title']),'time'=>$row['created_at']];
}

usort($activity, fn($a,$b) => strtotime($b['time']) - strtotime($a['time']));
$activity = array_slice($activity, 0, 12);

function time_ago($datetime) {
    $diff = time() - strtotime($datetime);
    if ($diff < 60)     return 'Just now';
    if ($diff < 3600)   return floor($diff/60).'m ago';
    if ($diff < 86400)  return floor($diff/3600).'h ago';
    if ($diff < 604800) return floor($diff/86400).'d ago';
    return date('M j, Y', strtotime($datetime));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard - PaddyCure</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/styles/styles.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/styles/fonts.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/styles/dashboard.css">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>/public/images/paddyCureLogo2.png" type="image/x-icon">
    <meta http-equiv="refresh" content="60">
    <style>
        .stats-container{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;margin-bottom:32px}
        .stat{background:#fff;border:1px solid #e0ead8;border-radius:10px;padding:20px 22px;display:flex;align-items:center;justify-content:space-between;gap:12px;position:relative;overflow:hidden;transition:box-shadow .2s,transform .2s}
        .stat:hover{box-shadow:0 6px 24px rgba(27,58,45,.09);transform:translateY(-2px)}
        .stat::before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--stat-color,#4A7C59);border-radius:10px 0 0 10px}
        .stat-info{display:flex;flex-direction:column;gap:4px}
        .stat-title{font-size:11px;font-weight:500;letter-spacing:.08em;text-transform:uppercase;color:#8BAF8E}
        .stat-value{font-size:2rem;font-weight:700;color:#1B3A2D;line-height:1}
        .stat-sub{font-size:11px;color:#4A7C59;margin-top:3px;display:flex;align-items:center;gap:5px}
        .stat-dot{width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block}
        .stat-icon{font-size:28px;opacity:.15;color:#1B3A2D}

        .dash-grid{display:grid;grid-template-columns:1fr 320px;gap:20px}
        .panel{background:#fff;border:1px solid #e0ead8;border-radius:10px;overflow:hidden}
        .panel-head{padding:16px 20px;border-bottom:1px solid #f0f5ec;display:flex;align-items:center;justify-content:space-between}
        .panel-head h3{font-size:13px;font-weight:600;color:#1B3A2D}
        .live-badge{display:flex;align-items:center;gap:5px;font-size:10px;color:#4A7C59;font-weight:500}
        .live-dot{width:7px;height:7px;border-radius:50%;background:#4A7C59;animation:lp 2s infinite}
        @keyframes lp{0%,100%{box-shadow:0 0 0 0 rgba(74,124,89,.5)}50%{box-shadow:0 0 0 5px rgba(74,124,89,0)}}

        .activity-list{max-height:460px;overflow-y:auto}
        .act-item{display:flex;align-items:flex-start;gap:13px;padding:12px 20px;border-bottom:1px solid #f5faf2;transition:background .15s}
        .act-item:last-child{border-bottom:none}
        .act-item:hover{background:#fafff5}
        .act-ico{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
        .act-ico .material-symbols-rounded{font-size:16px}
        .act-info{flex:1;min-width:0}
        .act-label{font-size:12px;font-weight:500;color:#1B3A2D;margin-bottom:2px}
        .act-detail{font-size:11px;color:#5a7a60;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
        .act-time{font-size:10px;color:#8BAF8E;white-space:nowrap;flex-shrink:0;padding-top:2px}
        .no-act{text-align:center;padding:48px 20px;color:#8BAF8E;font-size:13px}

        .quick-links{padding:12px}
        .ql{display:flex;align-items:center;gap:11px;padding:12px 14px;border-radius:8px;border:1px solid #e8f0e0;text-decoration:none;color:#1B3A2D;font-size:13px;font-weight:500;margin-bottom:7px;transition:background .15s,border-color .15s}
        .ql:last-child{margin-bottom:0}
        .ql:hover{background:#f0f8e8;border-color:#A8C97F}
        .ql .material-symbols-rounded{font-size:18px;color:#4A7C59}
        .ql-count{margin-left:auto;background:#e8f5e0;color:#4A7C59;font-size:11px;font-weight:600;padding:2px 8px;border-radius:100px}

        @media(max-width:900px){.dash-grid{grid-template-columns:1fr}}
    </style>
</head>
<body>
    <?php include('../../../components/header_navigation_bar.php'); ?>

    <main>
        <h1>Admin Dashboard</h1>
        <div class="dashboard-container">
            <?php include('../../../components/admin_dashboard_side_nav.php'); ?>

            <div class="dashboard-content-container">
                <div class="dashboard-content admin-dashboard-stats">

                    <header style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
                        <h2>Overview</h2>
                        <span style="font-size:11px;color:#8BAF8E">
                            <?= date('M j, Y · g:i A') ?> &nbsp;·&nbsp; auto-refreshes every 60s
                        </span>
                    </header>

                    <!-- STAT CARDS -->
                    <div class="stats-container">

                        <div class="stat" style="--stat-color:#4A7C59">
                            <div class="stat-info">
                                <h4 class="stat-title">Total Users</h4>
                                <div class="stat-value"><?= $data['total_users'] ?></div>
                                <?php if ($data['new_users_today'] > 0): ?>
                                <div class="stat-sub"><span class="stat-dot"></span>+<?= $data['new_users_today'] ?> today</div>
                                <?php endif; ?>
                            </div>
                            <span class="stat-icon material-symbols-rounded">group</span>
                        </div>

                        <div class="stat" style="--stat-color:#1565C0">
                            <div class="stat-info">
                                <h4 class="stat-title">Contact Messages</h4>
                                <div class="stat-value"><?= $data['total_messages'] ?></div>
                                <?php if ($data['new_messages_today'] > 0): ?>
                                <div class="stat-sub" style="color:#1565C0"><span class="stat-dot" style="background:#1565C0"></span>+<?= $data['new_messages_today'] ?> today</div>
                                <?php endif; ?>
                            </div>
                            <span class="stat-icon material-symbols-rounded">mail</span>
                        </div>

                        <!-- <div class="stat" style="--stat-color:#6B4226">
                            <div class="stat-info">
                                <h4 class="stat-title">Community Posts</h4>
                                <div class="stat-value"><?= $data['total_posts'] ?></div>
                                <?php if ($data['new_posts_today'] > 0): ?>
                                <div class="stat-sub" style="color:#6B4226"><span class="stat-dot" style="background:#6B4226"></span>+<?= $data['new_posts_today'] ?> today</div>
                                <?php endif; ?>
                            </div>
                            <span class="stat-icon material-symbols-rounded">forum</span>
                        </div> -->

                        <div class="stat" style="--stat-color:#7B1FA2">
                            <div class="stat-info">
                                <h4 class="stat-title">Articles</h4>
                                <div class="stat-value"><?= $data['total_articles'] ?></div>
                            </div>
                            <span class="stat-icon material-symbols-rounded">article</span>
                        </div>

                    </div>

                    <!-- ACTIVITY FEED + QUICK LINKS -->
                    <div class="dash-grid">

                        <!-- Recent Activity -->
                        <div class="panel">
                            <div class="panel-head">
                                <h3>Recent Activity</h3>
                                <span class="live-badge"><span class="live-dot"></span>Live</span>
                            </div>
                            <div class="activity-list">
                                <?php if (empty($activity)): ?>
                                    <div class="no-act">No activity yet.</div>
                                <?php else: foreach ($activity as $a): ?>
                                <div class="act-item">
                                    <div class="act-ico" style="background:<?= $a['color'] ?>1a">
                                        <span class="material-symbols-rounded" style="color:<?= $a['color'] ?>"><?= $a['icon'] ?></span>
                                    </div>
                                    <div class="act-info">
                                        <div class="act-label"><?= $a['label'] ?></div>
                                        <div class="act-detail"><?= $a['detail'] ?></div>
                                    </div>
                                    <div class="act-time"><?= time_ago($a['time']) ?></div>
                                </div>
                                <?php endforeach; endif; ?>
                            </div>
                        </div>

                        <!-- Quick Links -->
                        <div class="panel">
                            <div class="panel-head"><h3>Quick Actions</h3></div>
                            <div class="quick-links">
                                <a href="<?= BASE_URL ?>/pages/dashboard/admin/contact_messages.php" class="ql">
                                    <span class="material-symbols-rounded">mail</span>
                                    Contact Messages
                                    <span class="ql-count"><?= $data['total_messages'] ?></span>
                                </a>
                                <a href="<?= BASE_URL ?>/pages/dashboard/admin/articles/view_articles.php" class="ql">
                                    <span class="material-symbols-rounded">article</span>
                                    Manage Articles
                                    <span class="ql-count"><?= $data['total_articles'] ?></span>
                                </a>
                                <a href="<?= BASE_URL ?>/pages/dashboard/admin/articles/add_article.php" class="ql">
                                    <span class="material-symbols-rounded">add_circle</span>
                                    Add New Article
                                </a>
                                <!-- <a href="<?= BASE_URL ?>/pages/community.php" class="ql">
                                    <span class="material-symbols-rounded">forum</span>
                                    Community Feed
                                    <span class="ql-count"><?= $data['total_posts'] ?></span>
                                </a> -->
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include('../../../components/footer.php'); ?>
</body>
</html>