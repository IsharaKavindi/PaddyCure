<?php
require '../../config.php';
require '../../utils/database.php';

$conn = initialize_database();
session_start();

if (isset($_SESSION["user_id"])) {
    header("Location: " . BASE_URL . "/index.php");
}

$first_name = $last_name = $email = $contact_number = $confirm_password = $password = "";
$first_name_error = $last_name_error = $email_error = $contact_number_error = $confirm_password_error = $password_error = "";
$is_error = false;

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = test_input($_POST["first_name"]);
    if (!empty($_POST["first_name"]) && !preg_match("/^[a-zA-Z ]+$/", $first_name)) {
        $first_name_error = "Only letters and white space allowed";
        $is_error = true;
    }

    $last_name = test_input($_POST["last_name"]);
    if (!empty($_POST["last_name"]) && !preg_match("/^[a-zA-Z ]+$/", $last_name)) {
        $last_name_error = "Only letters and white space allowed";
        $is_error = true;
    }

    $email = test_input($_POST["email"]);
    if (!empty($_POST["email"]) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_error = "Invalid email format";
        $is_error = true;
    }

    $contact_number = test_input($_POST["contact_number"]);
    if (!empty($_POST["contact_number"]) && !preg_match("/^\+?\d{1,3}?[-.\s]?\(?\d{1,3}\)?[-.\s]?\d{1,12}$/", $contact_number)) {
        $contact_number_error = "Invalid contact number format";
        $is_error = true;
    }

    $password = test_input($_POST["password"]);
    $confirm_password = test_input($_POST["confirm_password"]);
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
        $query = "INSERT INTO user (first_name, last_name, email, contact_number, password) VALUES ('$first_name', '$last_name', '$email', '$contact_number', '$hashed_password')";

        if (mysqli_query($conn, $query)) {
            $new_user_id = mysqli_insert_id($conn);
            $_SESSION["user_id"] = $new_user_id;
            $_SESSION["user_first_name"] = $first_name;
            $_SESSION["user_last_name"] = $last_name;
            $_SESSION["role"] = $row["user_role"];

            header("Location: " . BASE_URL . "/index.php");
            exit();
        } else {
            echo "Error: " . $query . "<br>" . mysqli_error($conn);
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Join With Us - PaddyCure</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/styles/styles.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/styles/fonts.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/styles/auth.css">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>/public/images/paddyCureLogo2.png" type="image/x-icon">
</head>

<body>
    <?php include('../../components/header_navigation_bar.php'); ?>

    <div class="model-container register-model-container">
        <div class="model register-model">
            <!-- <?php include('../../components/user_account_benefits.php'); ?> -->
            <form class="register-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <header>
                    <h1>Join with us...</h1>
                    <p>With an account, you can explore exclusive benefits</p>
                </header>

                <div class="input-group-container">
                    <div class="input-container">
                        <label for="first_name">First Name *</label>
                        <input type="text" name="first_name" id="first_name" placeholder="John" required />
                        <span class="error-message"><?php echo $first_name_error; ?></span>
                    </div>

                    <div class="input-container">
                        <label for="last_name">Last Name *</label>
                        <input type="text" name="last_name" id="last_name" placeholder="Doe" required />
                        <span class="error-message"><?php echo $last_name_error; ?></span>
                    </div>
                </div>

                <div class="input-container">
                    <label for="email">Email *</label>
                    <input type="email" name="email" id="email" placeholder="johndoe@example.com" required />
                    <span class="error-message"><?php echo $email_error; ?></span>
                </div>

                <div class="input-container">
                    <label for="contact_number">Contact Number *</label>
                    <input type="tel" name="contact_number" id="contact_number" placeholder="+94 712 345 678" required />
                    <span class="error-message"><?php echo $contact_number_error; ?></span>
                </div>

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

                <div class="register-form-actions-container">
                    <button class="btn-primary form-submit-btn" type="submit">Create Account</button>
                    <p class="accept-terms-text">By continuing, you agree to our <a href="../terms_of_service.php">Terms of Service</a> and <a href="../privacy-policy.php">Privacy Policy</a>.</p>
                    <div class="create-account-link-container"><a href="./login.php">Already a member? Then, login to your account.</a></div>
                </div>
            </form>
        </div>

    </div>

    <?php include('../../components/footer.php'); ?>
</body>

</html>
