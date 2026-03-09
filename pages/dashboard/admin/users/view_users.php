<?php
require '../../../../config.php';
require '../../../../utils/database.php';
require '../../../../utils/authenticate.php';

$conn = initialize_database();
session_start();

authenticate(array('ADMIN'));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['reason'] == 'delete_user' && isset($_POST['user_id'])) {
        $q = <<<SQL
            UPDATE `user` SET `deleted_at` = NOW() WHERE `id` = {$_POST['user_id']};
        SQL;
        $res = mysqli_query($conn, $q);

        if (!$res) {
            echo mysqli_error($conn);
        }
    }
}

$query = <<<SQL
    SELECT * FROM `user` WHERE `deleted_at` IS NULL;
SQL;

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Users - PaddyCure</title>
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
                <div class="dashboard-content admin-dashboard-users">
                    <header>
                        <h2>Users</h2>
                        <a href="<?php echo BASE_URL; ?>/pages/dashboard/admin/users/add_user.php" class="btn-secondary"><span class="material-symbols-rounded btn-icon">add</span><span>Add User</span></a>
                    </header>

                    <table class="users-container">
                        <thead>
                            <tr>
                                <th class="user-first-name">First Name</th>
                                <th class="user-last-name">Last Name</th>
                                <th class="user-email">Email</th>
                                <th class="user-contact-number">Contact Number</th>
                                <th class="user-role">Role</th>
                                <th class="user-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row = mysqli_fetch_assoc($result)) {
                                $delete_btn = '';
                                if ($row['id'] != $_SESSION['user_id']) {
                                    $delete_btn = <<< HTML
                                    <button class="btn-secondary btn-only-icon" title="Delete User" onclick="deleteUser({$row['id']})">
                                        <span class="material-symbols-rounded btn-icon">delete</span>
                                    </button>
                                    HTML;
                                };
                                $BASE_URL = BASE_URL;

                                echo <<<HTML
                                    <tr>
                                        <td>{$row['first_name']}</td>
                                        <td>{$row['last_name']}</td>
                                        <td>{$row['email']}</td>
                                        <td>{$row['contact_number']}</td>
                                        <td>{$row['user_role']}</td>
                                        <td>
                                           <div class="actions-container">
                                               <a href="{$BASE_URL}/pages/dashboard/admin/users/edit_user.php?user_id={$row['id']}" class="btn-secondary btn-only-icon" title="Edit User">
                                                   <span class="material-symbols-rounded btn-icon">edit</span>
                                                </a>
                                                $delete_btn
                                           </div>
                                        </td>
                                    </tr>
                                HTML;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php
    include('../../../../components/footer.php');
    mysqli_close($conn);
    echo "<script src='" . BASE_URL . "/public/scripts/view_users.js'></script>";
    ?>
</body>

</html>
