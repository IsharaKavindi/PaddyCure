<?php
require '../../../../config.php';
require '../../../../utils/database.php';
require '../../../../utils/authenticate.php';

$conn = initialize_database();
session_start();
authenticate(array('ADMIN'));

if (!isset($_GET['article_id'])) {
    echo "Article ID not found."; exit();
}
$article_id = intval($_GET['article_id']);

$tags = ['Disease Guide', 'Treatment Tips', 'Fertilizer', 'Irrigation', 'Pest Control', 'Harvest', 'Weather', 'General'];

$image_target_dir = './../../../../public/images/articles/';

// Fetch existing article
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM article WHERE id = $article_id AND deleted_at IS NULL"));
if (!$row) { echo "Article not found."; exit(); }

$title       = $row['title'];
$body        = $row['body'];
$tag         = $row['tag'];
$image       = $row['image'];
$title_error = $body_error = $tag_error = $image_error = "";
$is_error    = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title'] ?? '');
    if ($title === '') { $title_error = "Title cannot be empty."; $is_error = true; }

    $body = trim($_POST['body'] ?? '');
    if ($body === '')  { $body_error = "Content cannot be empty.";  $is_error = true; }

    $tag = trim($_POST['tag'] ?? '');
    if ($tag !== '' && !in_array($tag, $tags)) { $tag_error = "Invalid tag."; $is_error = true; }

    if (isset($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) {
        $finfo   = new finfo(FILEINFO_MIME_TYPE);
        $allowed = ['jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'];
        if (false === array_search($finfo->file($_FILES['image']['tmp_name']), $allowed, true)) {
            $image_error = "Only JPG, PNG, WEBP and GIF files are allowed.";
            $is_error = true;
        }
    }

    if (!$is_error) {
        // Handle new image upload
        if (isset($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) {
            $ext            = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $image_filename = $article_id . '_' . time() . '.' . $ext;
            $save_path      = $image_target_dir . $image_filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $save_path)) {
                $image = $image_filename;
            } else {
                $image_error = "Image upload failed.";
            }
        }

        $stmt = mysqli_prepare($conn,
            "UPDATE article SET title=?, body=?, tag=?, image=?, updated_at=NOW() WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'ssssi', $title, $body, $tag, $image, $article_id);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_error($stmt)) {
            echo "<script>alert('DB Error: " . mysqli_stmt_error($stmt) . "');</script>";
        } else {
            header('Location: ' . BASE_URL . '/pages/dashboard/admin/articles/view_articles.php?updated=1');
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Article - PaddyCure Admin</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/styles/styles.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/styles/fonts.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/styles/dashboard.css">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>/public/images/paddyCureLogo2.png" type="image/x-icon">
</head>
<body>
    <?php include('../../../../components/header_navigation_bar.php'); ?>

    <main>
        <h1>Admin Dashboard</h1>
        <div class="dashboard-container">
            <?php include('../../../../components/admin_dashboard_side_nav.php'); ?>

            <div class="dashboard-content-container">
                <div class="dashboard-content admin-dashboard-menus">
                    <header>
                        <h2>Edit Article</h2>
                        <a href="<?php echo BASE_URL; ?>/pages/dashboard/admin/articles/view_articles.php"
                           class="btn-secondary">
                            <span class="material-symbols-rounded btn-icon">arrow_back</span>
                            <span>Back to Articles</span>
                        </a>
                    </header>

                    <form class="register-form" method="POST"
                          action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>"
                          enctype="multipart/form-data">

                        <!-- Image -->
                        <div class="input-group-container">
                            <?php if (!empty($image)): ?>
                                <img id="display_image"
                                     src="<?= BASE_URL ?>/public/images/articles/<?= htmlspecialchars($image) ?>"
                                     style="max-width:300px;border-radius:8px;margin-bottom:8px;display:block;"
                                     alt="Current Cover Image">
                            <?php else: ?>
                                <img id="display_image" src="" style="display:none;max-width:300px;border-radius:8px;margin-bottom:8px;" alt="">
                            <?php endif; ?>
                            <div class="input-container">
                                <label for="image">Cover Image <small style="color:#999">(leave empty to keep current)</small></label>
                                <input type="file" name="image" id="image" accept="image/*"
                                       onchange="previewImage(this)" />
                                <span class="error-message"><?php echo $image_error; ?></span>
                            </div>
                        </div>

                        <!-- Title -->
                        <div class="input-container">
                            <label for="title">Title *</label>
                            <input type="text" name="title" id="title"
                                   placeholder="Article title"
                                   value="<?= htmlspecialchars($title) ?>" required />
                            <span class="error-message"><?php echo $title_error; ?></span>
                        </div>

                        <!-- Body -->
                        <div class="input-container">
                            <label for="body">Content *</label>
                            <textarea name="body" id="body" rows="12"
                                      placeholder="Write the article content here…"
                                      style="resize:vertical;"><?= htmlspecialchars($body) ?></textarea>
                            <span class="error-message"><?php echo $body_error; ?></span>
                        </div>

                        <!-- Tag -->
                        <div class="input-group-container">
                            <div class="input-container">
                                <label for="tag">Category / Tag</label>
                                <select name="tag" id="tag">
                                    <option value="" <?= $tag === '' ? 'selected' : '' ?>>-- Select Tag --</option>
                                    <?php foreach ($tags as $t): ?>
                                        <option value="<?= $t ?>" <?= $tag === $t ? 'selected' : '' ?>>
                                            <?= $t ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="error-message"><?php echo $tag_error; ?></span>
                            </div>
                        </div>

                        <hr>
                        <button class="btn-primary form-submit-btn" type="submit">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include('../../../../components/footer.php'); ?>

    <script>
    function previewImage(input) {
        const img = document.getElementById('display_image');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                img.src = e.target.result;
                img.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
</body>
</html>