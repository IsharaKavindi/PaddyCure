<?php
require '../../../../config.php';
require '../../../../utils/database.php';
require '../../../../utils/authenticate.php';

$conn = initialize_database();
session_start();

authenticate(array('ADMIN'));
$user_role = $first_name = $last_name = $email = $contact_number = $password = $confirm_password = "";
$user_role_error = $first_name_error = $last_name_error = $email_error = $contact_number_error = $password_error = $confirm_password_error = "";

$is_error = false;

$roles = array('ADMIN', 'USER');

function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_role = sanitize_input($_POST["user_role"]);
    if (!empty($_POST["user_role"]) && !in_array($user_role, $roles)) {
        $user_role_error = "Unknown user role";
        $is_error = true;
    }
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
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = <<< SQL
        INSERT INTO 
            user (first_name, last_name, email, contact_number, password) 
        VALUES 
            ('$first_name', '$last_name', '$email', '$contact_number', '$hashed_password');
        SQL;

        if (mysqli_query($conn, $query)) {
            echo "<script>alert('User added successfully!');</script>";
            header("Location: " . BASE_URL . "/pages/dashboard/admin/users/view_users.php");
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
    <title>Add New User - PaddyCure</title>
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
                        <h2>Add New User</h2>
                    </header>

                    <form class="register-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="input-container">
                            <label for="user_role">User Role *</label>
                            <select type="text" name="user_role" id="user_role" placeholder="Select the no of people" required onchange="getReservationTimeSlots()" value="<?php echo $user_role; ?>">
                                <option value="" selected disabled hidden>Select the user role</option>
                                <option value="ADMIN">Administrator</option>
                                <option value="USER">User</option>
                            </select>
                            <span class="error-message"><?php echo $user_role_error; ?></span>
                        </div>

                        <hr>

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
                                <label for="password">Password *</label>
                                <input type="password" name="password" id="password" placeholder="MySuperSecretPassword" required />
                                <span class="error-message"><?php echo $password_error; ?></span>
                            </div>
                            <div class="input-container">
                                <label for="confirm_password">Confirm Password *</label>
                                <input type="password" name="confirm_password" id="confirm_password" placeholder="MySuperSecretPassword" required />
                                <span class="error-message"><?php echo $confirm_password_error; ?></span>
                            </div>
                        </div>

                        <hr>

                        <button class="btn-primary form-submit-btn" type="submit">Add User</button>

                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include('../../../../components/footer.php'); ?>
</body>

</html>
