<?php
require '../../config.php';
require '../../utils/database.php';

$conn = initialize_database();
session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Unauthorized - PaddyCure</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/styles/styles.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/styles/fonts.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/styles/auth.css">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>/public/images/paddyCureLogo2.png" type="image/x-icon">
</head>

<body>
    <?php include('../../components/header_navigation_bar.php'); ?>

    <section class="unauthorized">
        <span class="unauthorized-icon material-symbols-rounded-filled">release_alert</span>
        <h1>Unauthorized</h1>
        <p>You do not have permission to access this page. Please check account permissions and try again with an account with the necessary permissions.</p>
        <a class="btn-primary" href="<?php echo BASE_URL; ?>/index.php">Go Home</a>
    </section>

    <?php
    include('../../components/footer.php');
    mysqli_close($conn);
    ?>
</body>

</html>
