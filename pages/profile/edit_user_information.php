<?php
require '../../config.php';
require '../../utils/database.php';
require '../../components/menu_item.php';

$conn = initialize_database();
session_start();
$BASE_URL = BASE_URL;

$current_first_name = $current_last_name = $current_email = $current_contact_number = "";
$first_name = $last_name = $email = $contact_number = $password = $confirm_password = "";
$first_name_error = $last_name_error = $email_error = $contact_number_error = $password_error = $confirm_password_error = "";
$is_error = false;
$user_id = $_SESSION["user_id"];

$q = <<<SQL
    SELECT * FROM `user` WHERE `id` = {$user_id} LIMIT 1;
SQL;
$result = mysqli_query($conn, $q);

if (!$result) {
    echo "Error: " . mysqli_error($conn);
    exit();
} else {
    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        echo "User not found";
        exit();
    } else {
        $first_name = $current_first_name = $user['first_name'];
        $last_name = $current_last_name = $user['last_name'];
        $email = $current_email = $user['email'];
        $contact_number = $current_contact_number = $user['contact_number'];
    }
}

function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

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

    $password = sanitize_input($_POST["password"]);
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
        $query = <<<SQL
            UPDATE `user` SET
                `first_name` = '$first_name',
                `last_name` = '$last_name',
                `email` = '$email',
                `contact_number` = '$contact_number',
                `password` = '$hashed_password',
                `updated_at` = NOW()
            WHERE `id` = {$user_id};
        SQL;

        if (mysqli_query($conn, $query)) {
            echo "<script>alert('User data updated successfully!');</script>";
            header("Location: " . BASE_URL . "/pages/profile/profile.php");
            exit();
        } else {
            echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Editing My Profile - PaddyCure</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/styles/styles.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/styles/fonts.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/styles/profile.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/styles/dashboard.css">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>/public/images/paddyCureLogo2.png" type="image/x-icon">
</head>

<body>
    <?php include('../../components/header_navigation_bar.php'); ?>

    <main>
        <h1><?= $_SESSION['user_first_name'] ?>'s Profile</h1>
        <div class="dashboard-container">

            <?php include('../../components/profile_dashboard_side_nav.php'); ?>

            <div class="dashboard-content-container">
                <div class="dashboard-content staff-dashboard-reservations">
                    <header>
                        <h2>Editing My Profile</h2>
                    </header>

                    <form class="register-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>">
                        <div class="input-group-container">
                            <div class="input-container">
                                <label for="first_name">First Name *</label>
                                <input type="text" name="first_name" id="first_name" placeholder="John" value="<?= $first_name ?>" required />
                                <span class="error-message"><?php echo $first_name_error; ?></span>
                            </div>

                            <div class="input-container">
                                <label for="last_name">Last Name *</label>
                                <input type="text" name="last_name" id="last_name" placeholder="Doe" value="<?= $last_name ?>" required />
                                <span class="error-message"><?php echo $last_name_error; ?></span>
                            </div>
                        </div>

                        <div class="input-container">
                            <label for="email">Email *</label>
                            <input type="email" name="email" id="email" placeholder="johndoe@example.com" value="<?= $email ?>" required />
                            <span class="error-message"><?php echo $email_error; ?></span>
                        </div>

                        <div class="input-container">
                            <label for="contact_number">Contact Number *</label>
                            <input type="tel" name="contact_number" id="contact_number" placeholder="+94 712 345 678" value="<?= $contact_number ?>" required />
                            <span class="error-message"><?php echo $contact_number_error; ?></span>
                        </div>

                        <div class="input-group-container">
                            <div class="input-container">
                                <label for="password">Password</label>
                                <input type="password" name="password" id="password" placeholder="MySuperSecretPassword" />
                                <span class="error-message"><?php echo $password_error; ?></span>
                                <small>Ignoring password will keep the previous password as it is.</small>
                            </div>
                            <div class="input-container">
                                <label for="confirm_password">Confirm Password</label>
                                <input type="password" name="confirm_password" id="confirm_password" placeholder="MySuperSecretPassword" />
                                <span class="error-message"><?php echo $confirm_password_error; ?></span>
                            </div>
                        </div>

                        <hr>

                        <button class="btn-primary form-submit-btn" type="submit">Update Profile</button>

                    </form>
                </div>
            </div>
        </div>

    </main>

    <?php
    include('../../components/footer.php');
    echo "<script src='" . BASE_URL . "/public/scripts/profile.js'></script>";
    mysqli_close($conn);
    ?>
</body>

</html>
