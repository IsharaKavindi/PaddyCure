<?php
require '../../config.php';
require '../../utils/database.php';

$conn = initialize_database();
session_start();

if (isset($_SESSION["user_id"])) {
    header("Location: " . BASE_URL . "/index.php");
}

$email = $password = "";
$email_error = $password_error = "";
$is_error = false;

if (isset($_SESSION['user_id']) && isset($_GET['redirect'])) {
    header("Location: " . urldecode($_GET['redirect']));
    exit();
}

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = test_input($_POST["email"]);
    $password = test_input($_POST["password"]);

    if (!$is_error) {
        $query = "SELECT * FROM user WHERE email = '$email' LIMIT 1";

        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_array($result);
        if ($row) {
            $hashed_password = $row["password"];

            if (!password_verify($password, $hashed_password)) {
                $password_error = "Invalid email or password";
            } else {
                $_SESSION["user_id"] = $row["id"];
                $_SESSION["user_first_name"] = $row["first_name"];
                $_SESSION["user_last_name"] = $row["last_name"];
                $_SESSION["role"] = $row["user_role"];


                if (isset($_GET['redirect'])) {
                    $redirectUrl = urldecode($_GET['redirect']);
                    header("Location: " . $redirectUrl);
                } else
                    header("Location: " . BASE_URL . "/index.php");

                exit();
            }
        } else {
            $email_error = "Invalid email or password";
        }
    };
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - PaddyCure</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/styles/styles.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/styles/fonts.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/styles/auth.css">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>/public/images/paddyCureLogo2.png" type="image/x-icon">
</head>

<body>
    <?php include('../../components/header_navigation_bar.php'); ?>

    <div class="model-container login-model-container">
        <div class="model login-model">
            <form class="login-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>">
                <header>
                    <h1>Welcome back...</h1>
                    <p>Log in to your account to see what's new with The Gallery Cafe</p>
                </header>

                <div class="input-container">
                    <label for="email">Email *</label>
                    <input type="email" name="email" id="email" placeholder="johndoe@example.com" required />
                    <span class="error-message"><?php echo $email_error ?></span>
                </div>

                <div class="input-container">
                    <label for="password">Password *</label>
                    <input type="password" name="password" id="password" placeholder="MySuperSecretPassword" required />
                    <span class="error-message"><?php echo $password_error ?></span>
                    <div class="forget-password-link-container"><a href="./forgot-password.php">Did you forget your password?</a></div>
                </div>

                <div class="login-form-actions-container">
                    <button class="btn-primary" type="submit">Log In</button>
                    <p class="accept-terms-text">By continuing, you agree to our <a href="../terms_of_service.php">Terms of Service</a> and <a href="../privacy-policy.php">Privacy Policy</a>.</p>
                    <div class="create-account-link-container"><a href="./register.php">Not a member? Then, join with us.</a></div>
                </div>
            </form>
        </div>

    </div>

    <?php include('../../components/footer.php'); ?>
</body>

</html>
