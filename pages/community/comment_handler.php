<?php
require '../../config.php';
require '../../utils/database.php';
require '../../utils/authenticate.php';

$conn = initialize_database();
session_start();
authenticate();

header('Content-Type: application/json');

$user_id = (int)$_SESSION['user_id'];
$action  = $_POST['action'] ?? $_GET['action'] ?? '';

// ── POST a comment ────────────────────────────────────────────
if ($action === 'post' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = (int)($_POST['post_id'] ?? 0);
    $body    = trim($_POST['body'] ?? '');

    if (!$post_id || !$body) {
        echo json_encode(['success' => false, 'error' => 'Missing fields']);
        exit();
    }

    // Make sure table exists
    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `community_post_comment` (
        `id`         INT  NOT NULL AUTO_INCREMENT UNIQUE,
        `post_id`    INT  NOT NULL,
        `user_id`    INT  NOT NULL,
        `body`       TEXT NOT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `deleted_at` DATETIME,
        PRIMARY KEY(`id`)
    )");

    $stmt = mysqli_prepare($conn,
        "INSERT INTO community_post_comment (post_id, user_id, body) VALUES (?, ?, ?)");

    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . mysqli_error($conn)]);
        exit();
    }

    mysqli_stmt_bind_param($stmt, 'iis', $post_id, $user_id, $body);

    if (!mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => false, 'error' => 'Execute failed: ' . mysqli_stmt_error($stmt)]);
        exit();
    }

    $comment_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    // Return new comment with user info
    $row = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT c.id, c.body, c.created_at,
               u.first_name, u.last_name
        FROM community_post_comment c
        INNER JOIN user u ON u.id = c.user_id
        WHERE c.id = $comment_id
    "));

    echo json_encode(['success' => true, 'comment' => $row]);
    exit();
}

// ── FETCH comments for a post ─────────────────────────────────
if ($action === 'fetch' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $post_id = (int)($_GET['post_id'] ?? 0);

    if (!$post_id) {
        echo json_encode(['success' => false, 'error' => 'Missing post_id']);
        exit();
    }

    $rows = [];
    $res  = mysqli_query($conn, "
        SELECT c.id, c.body, c.created_at,
               u.first_name, u.last_name
        FROM community_post_comment c
        INNER JOIN user u ON u.id = c.user_id
        WHERE c.post_id = $post_id AND c.deleted_at IS NULL
        ORDER BY c.created_at ASC
    ");

    if ($res) {
        while ($r = mysqli_fetch_assoc($res)) $rows[] = $r;
    }

    echo json_encode(['success' => true, 'comments' => $rows]);
    exit();
}

// ── DELETE a comment ──────────────────────────────────────────
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment_id = (int)($_POST['comment_id'] ?? 0);
    mysqli_query($conn,
        "UPDATE community_post_comment SET deleted_at = NOW()
         WHERE id = $comment_id AND user_id = $user_id");
    echo json_encode(['success' => true]);
    exit();
}

echo json_encode(['success' => false, 'error' => 'Unknown action']);

?>