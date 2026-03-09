<?php
require '../../../../config.php';
require '../../../../utils/database.php';
require '../../../../utils/authenticate.php';

$conn = initialize_database();
session_start();
authenticate(array('ADMIN'));

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $del = (int)$_POST['delete_id'];
    mysqli_query($conn, "UPDATE contact_messages SET deleted_at = NOW() WHERE id = $del");
    header('Location: ' . BASE_URL . '/pages/dashboard/admin/contact_messages.php?deleted=1');
    exit();
}

// Fetch messages
$result = mysqli_query($conn, "
    SELECT cm.id, cm.name, cm.email, cm.subject, cm.message, cm.created_at,
           u.first_name, u.last_name
    FROM contact_messages cm
    LEFT JOIN user u ON u.id = cm.user_id
    WHERE cm.deleted_at IS NULL
    ORDER BY cm.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Messages - PaddyCure Admin</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/styles/styles.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/styles/fonts.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/styles/dashboard.css">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>/public/images/paddyCureLogo2.png" type="image/x-icon">
    <style>
        .msg-subject { font-weight: 600; color: #1B3A2D; }
        .msg-body { color: #555; max-width: 300px; white-space: pre-wrap; word-break: break-word; }
        .msg-date { color: #999; font-size: 11px; white-space: nowrap; }
        .msg-email { color: #2E7D32; font-size: 12px; }
        .badge-user { font-size: 10px; background: #E8F5E9; color: #2E7D32; padding: 2px 8px; border-radius: 10px; }
        .badge-guest { font-size: 10px; background: #F5F5F5; color: #999; padding: 2px 8px; border-radius: 10px; }
        .no-results { text-align: center; padding: 40px; color: #aaa; }
        .alert-success { background: #E8F5E9; border: 1px solid #A5D6A7; color: #2E7D32; padding: 10px 16px; border-radius: 6px; margin-bottom: 16px; font-size: 13px; }

        .overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
        .overlay.open { display: flex; }
        .msg-modal { background: #fff; border-radius: 10px; padding: 32px; max-width: 560px; width: 100%; box-shadow: 0 8px 40px rgba(0,0,0,0.2); position: relative; }
        .msg-modal h3 { margin-bottom: 8px; color: #1B3A2D; }
        .msg-modal .meta { font-size: 12px; color: #999; margin-bottom: 16px; }
        .msg-modal .body { font-size: 14px; line-height: 1.7; color: #444; white-space: pre-wrap; border-top: 1px solid #eee; padding-top: 14px; }
        .mclose { position: absolute; top: 14px; right: 16px; background: none; border: none; font-size: 20px; cursor: pointer; color: #999; }
        .btn-view { background: none; border: 1px solid #2E7D32; color: #2E7D32; padding: 4px 10px; border-radius: 4px; font-size: 11px; cursor: pointer; }
        .btn-view:hover { background: #E8F5E9; }
        .btn-del { background: none; border: 1px solid #e57373; color: #e57373; padding: 4px 10px; border-radius: 4px; font-size: 11px; cursor: pointer; }
        .btn-del:hover { background: #FFEBEE; }
        .btn-reply { background: none; border: 1px solid #1565C0; color: #1565C0; padding: 4px 10px; border-radius: 4px; font-size: 11px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-reply:hover { background: #E3F2FD; }
    </style>
</head>

<body>
    <?php include('../../../../components/header_navigation_bar.php'); ?>

    <main>
        <h1>Admin Dashboard</h1>
        <div class="dashboard-container">

            <?php include('../../../../components/admin_dashboard_side_nav.php'); ?>

            <div class="dashboard-content-container">
                <div class="dashboard-content">
                    <header>
                        <h2>Contact Messages</h2>
                    </header>

                    <?php if (isset($_GET['deleted'])): ?>
                    <div class="alert-success">✓ Message deleted successfully.</div>
                    <?php endif; ?>

                    <table class="reservations-container">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>User</th>
                                <th>Date</th>
                                <th class="reservation-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (mysqli_num_rows($result) > 0):
                            while ($row = mysqli_fetch_assoc($result)):
                                $is_user    = !empty($row['first_name']);
                                $user_label = $is_user
                                    ? htmlspecialchars($row['first_name'] . ' ' . $row['last_name'])
                                    : 'Guest';
                                $short_msg  = htmlspecialchars(mb_substr($row['message'], 0, 60)) . (mb_strlen($row['message']) > 60 ? '…' : '');
                                $full_msg   = htmlspecialchars($row['message']);
                                $date       = date('M j, Y g:i A', strtotime($row['created_at']));
                                $gmail_to      = urlencode($row['email']);
                                $gmail_subject = urlencode('Re: ' . $row['subject']);
                                $gmail_body    = urlencode("Hi " . $row['name'] . ",\n\nThank you for contacting PaddyCare.\n\n---\nYour original message:\n" . $row['message']);
                                $gmail_url     = "https://mail.google.com/mail/?view=cm&to={$gmail_to}&su={$gmail_subject}&body={$gmail_body}";
                        ?>
                        <tr class="reservation">
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td class="msg-email"><?= htmlspecialchars($row['email']) ?></td>
                            <td class="msg-subject"><?= htmlspecialchars($row['subject']) ?></td>
                            <td class="msg-body"><?= $short_msg ?></td>
                            <td>
                                <?php if ($is_user): ?>
                                    <span class="badge-user"><?= $user_label ?></span>
                                <?php else: ?>
                                    <span class="badge-guest">Guest</span>
                                <?php endif; ?>
                            </td>
                            <td class="msg-date"><?= $date ?></td>
                            <td class="reservation-actions">
                                <div class="actions-container">
                                    <button class="btn-view" onclick="viewMsg(
                                        '<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>',
                                        '<?= htmlspecialchars($row['email'], ENT_QUOTES) ?>',
                                        '<?= htmlspecialchars($row['subject'], ENT_QUOTES) ?>',
                                        '<?= addslashes($full_msg) ?>',
                                        '<?= $date ?>'
                                    )">View</button>
                                    <a href="<?= $gmail_url ?>" target="_blank" class="btn-reply">Reply</a>
                                    <form method="POST" style="display:inline" onsubmit="return confirm('Delete this message?')">
                                        <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                                        <button type="submit" class="btn-del">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="8" class="no-results">No messages yet.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- View Message Modal -->
    <div class="overlay" id="overlay">
        <div class="msg-modal">
            <button class="mclose" onclick="closeModal()">✕</button>
            <h3 id="m-subject"></h3>
            <div class="meta" id="m-meta"></div>
            <div class="body" id="m-body"></div>
        </div>
    </div>

    <?php include('../../../../components/footer.php'); ?>

    <script>
    function viewMsg(name, email, subject, message, date) {
        document.getElementById('m-subject').textContent = subject;
        document.getElementById('m-meta').textContent    = 'From: ' + name + ' <' + email + '>  ·  ' + date;
        document.getElementById('m-body').textContent    = message;
        document.getElementById('overlay').classList.add('open');
    }
    function closeModal() {
        document.getElementById('overlay').classList.remove('open');
    }
    document.getElementById('overlay').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
    </script>
</body>
</html>