<?php
require '../../config.php';
require '../../utils/database.php';
require '../../utils/authenticate.php';

$conn = initialize_database();
session_start();
authenticate();

header('Content-Type: application/json');

$user_id = (int)$_SESSION['user_id'];
$post_id = (int)($_POST['post_id'] ?? 0);

if (!$post_id) {
    echo json_encode(['success' => false, 'error' => 'Missing post_id']);
    exit();
}

// Check if already liked
$check = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT id FROM community_post_like WHERE post_id = $post_id AND user_id = $user_id"
));

if ($check) {
    // Unlike
    mysqli_query($conn,
        "DELETE FROM community_post_like WHERE post_id = $post_id AND user_id = $user_id"
    );
    mysqli_query($conn,
        "UPDATE community_post SET likes = GREATEST(likes - 1, 0) WHERE id = $post_id"
    );
} else {
    // Like
    mysqli_query($conn,
        "INSERT INTO community_post_like (post_id, user_id) VALUES ($post_id, $user_id)"
    );
    mysqli_query($conn,
        "UPDATE community_post SET likes = likes + 1 WHERE id = $post_id"
    );
}

// Return updated like count
$row = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT likes FROM community_post WHERE id = $post_id"
));

echo json_encode([
    'success' => true,
    'likes'   => (int)$row['likes'],
    'liked'   => !$check
]);