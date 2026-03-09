<?php
require('../config.php');
require('../utils/database.php');
session_start();

$conn = initialize_database();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    header("Location: mailto:" . $email . "?subject=Contact Us&body=" . $message);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Us - PaddyCure</title>
    <link rel="stylesheet" href="../public/styles/styles.css">
    <link rel="stylesheet" href="../public/styles/fonts.css">
    <link rel="stylesheet" href="../public/styles/home.css">
    <link rel="stylesheet" href="../public/styles/menu.css">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>/public/images/paddyCureLogo2.png" type="image/x-icon">
</head>

<body>
    <?php include('../components/header_navigation_bar.php'); ?>

    <section class="hero contact-us-hero">
        <div class="hero-image-container"></div>
        <div class="hero-text-container">
            <h1>Contact Us</h1>
        </div>
    </section>

    <main class="">
        <section class="contact-info-container">
            <form class="contact-form" action="<?= htmlspecialchars($_SERVER["REQUEST_URI"]); ?>" method="POST">
                <h2>Get in Touch</h2>
                <p>Feel free to reach out to us anytime. Your feedback is important to us and will help us improve our services.</p>

                <div class="input-group-container">
                    <div class="input-container">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" placeholder="John Doe" required>
                    </div>
                    <div class="input-container">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" placeholder="johndoe@example.com" required>
                    </div>
                </div>
                <div class="input-container">
                    <label for="message">Message:</label>
                    <textarea id="message" name="message" placeholder="Enter your message here..." required></textarea>
                </div>
                <button class="btn-primary">Submit</button>
            </form>

            <div class="contact-info">
                <h2>Contact Information</h2>
                <p>Feel free to reach out to us anytime. Your feedback is important to us and will help us improve our services.</p>

                <div class="contact-details">
                    <h4>Phone Number</h4>
                    <p>(123) 456-7890</p>

                    <h4>Email</h4>
                    <p><a href="mailto:info@thegallerycafe.com">info@thegallerycafe.com</a></p>

                    <h4>Address</h4>
                    <p>
                        The Gallery Cafe,<br>
                        123 Coffee Street,<br>
                        Colombo 06,<br>
                        Sri Lanka.
                    </p>
                </div>
            </div>

            <div class="find-us">
                <h2>Find Us</h2>
                <p>Find us on Google Maps.</p>

                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4853.275337598483!2d97.96708062444941!3d4.472974862710222!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30379b417be90533%3A0x9543dbee9ae1360!2sCouvee%20-%20Kota%20Langsa!5e0!3m2!1sen!2slk!4v1723028368911!5m2!1sen!2slk" width="450" height="400" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </section>

    </main>
    <?php include('../components/footer.php'); ?>
</body>

</html>
