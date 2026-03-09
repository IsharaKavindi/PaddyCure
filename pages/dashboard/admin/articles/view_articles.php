<?php
require '../../../../config.php';
require '../../../../utils/database.php';
require '../../../../utils/authenticate.php';

$conn = initialize_database();
session_start();
authenticate(array('ADMIN'));

// Soft delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $del_id = intval($_POST['delete_id']);
    mysqli_query($conn, "UPDATE article SET deleted_at = NOW() WHERE id = $del_id");
    header('Location: ' . BASE_URL . '/pages/dashboard/admin/articles/view_articles.php?deleted=1');
    exit();
}

$result = mysqli_query($conn, "
    SELECT id, title, tag, image, created_at
    FROM article
    WHERE deleted_at IS NULL
    ORDER BY created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Articles - PaddyCure Admin</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/styles/styles.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/styles/fonts.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/styles/dashboard.css">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>/public/images/paddyCureLogo2.png" type="image/x-icon">
    <style>
        .art-table { width:100%; border-collapse:collapse; }
        .art-table th, .art-table td { padding:12px 14px; text-align:left; border-bottom:1px solid #e0e0e0; font-size:13px; vertical-align:middle; }
        .art-table th { background:#f5f5f5; font-weight:600; color:#444; }
        .art-table tr:hover td { background:#fafff5; }
        .art-thumb { width:64px; height:48px; object-fit:cover; border-radius:4px; background:#eee; display:block; }
        .art-thumb-empty { width:64px; height:48px; background:#eee; border-radius:4px; display:flex; align-items:center; justify-content:center; font-size:10px; color:#999; }
        .art-title { font-weight:600; color:#1B3A2D; max-width:300px; }
        .art-tag { font-size:10px; background:#E8F5E9; color:#2E7D32; padding:3px 10px; border-radius:10px; }
        .art-date { color:#999; font-size:11px; white-space:nowrap; }
        .no-results { text-align:center; padding:40px; color:#aaa; }
        .alert-success { background:#E8F5E9; border:1px solid #A5D6A7; color:#2E7D32; padding:10px 16px; border-radius:6px; margin-bottom:16px; font-size:13px; }
        .alert-info    { background:#E3F2FD; border:1px solid #90CAF9; color:#1565C0; padding:10px 16px; border-radius:6px; margin-bottom:16px; font-size:13px; }
    </style>
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
                        <h2>Articles</h2>
                        <a href="<?php echo BASE_URL; ?>/pages/dashboard/admin/articles/add_article.php"
                           class="btn-secondary">
                            <span class="material-symbols-rounded btn-icon">add</span>
                            <span>Add Article</span>
                        </a>
                    </header>

                    <?php if (isset($_GET['added'])): ?>
                        <div class="alert-info">✓ Article published successfully.</div>
                    <?php elseif (isset($_GET['updated'])): ?>
                        <div class="alert-info">✓ Article updated successfully.</div>
                    <?php elseif (isset($_GET['deleted'])): ?>
                        <div class="alert-success">✓ Article deleted successfully.</div>
                    <?php endif; ?>

                    <table class="art-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Tag</th>
                                <th>Published</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (mysqli_num_rows($result) > 0):
                            while ($row = mysqli_fetch_assoc($result)):
                                $date = date('M j, Y', strtotime($row['created_at']));
                                $img_src = !empty($row['image'])
                                    ? BASE_URL . '/public/images/articles/' . htmlspecialchars($row['image'])
                                    : '';
                        ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td>
                                <?php if ($img_src): ?>
                                    <img src="<?= $img_src ?>" class="art-thumb" alt="cover">
                                <?php else: ?>
                                    <div class="art-thumb-empty">No img</div>
                                <?php endif; ?>
                            </td>
                            <td class="art-title"><?= htmlspecialchars($row['title']) ?></td>
                            <td>
                                <?php if ($row['tag']): ?>
                                    <span class="art-tag"><?= htmlspecialchars($row['tag']) ?></span>
                                <?php else: ?>
                                    <span style="color:#bbb;font-size:11px">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="art-date"><?= $date ?></td>
                            <td>
                                <div class="actions-container">
                                    <a href="<?= BASE_URL ?>/pages/dashboard/admin/articles/edit_article.php?article_id=<?= $row['id'] ?>"
                                       class="btn-secondary btn-only-icon" title="Edit Article">
                                        <span class="material-symbols-rounded btn-icon">edit</span>
                                    </a>
                                    <form method="POST" style="display:inline"
                                          onsubmit="return confirm('Delete this article?')">
                                        <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                                        <button type="submit" class="btn-secondary btn-only-icon" title="Delete Article">
                                            <span class="material-symbols-rounded btn-icon">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="6" class="no-results">No articles yet. <a href="<?= BASE_URL ?>/pages/dashboard/admin/articles/add_article.php">Add one now →</a></td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php include('../../../../components/footer.php'); ?>
</body>
</html>