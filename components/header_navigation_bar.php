<?php
$conn = initialize_database();

function getFullUrl()
{
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	$host = $_SERVER['HTTP_HOST'];
	$requestUri = $_SERVER['REQUEST_URI'];

	return $protocol . $host . $requestUri;
}

function isActivePage($url)
{
	if (strpos(getFullUrl(), $url) !== false) return 'active';
	else return 'not-active';
}
?>

<nav class="navbar blurred-background">
	<div href="<?php echo BASE_URL; ?>/index.php" class="logo-container">
		<img src="<?php echo BASE_URL; ?>/public/images/paddyCureLogo.png" alt="logo" style="width: 9cm;" />
	</div>


	<ul class="nav-links">
		<li class="<?php echo isActivePage('/index.php') ?>">
			<a href="<?php echo BASE_URL; ?>/index.php">Home</a>
		</li>
		<li class="<?php echo isActivePage('/pages/articles/articles.php') ?>">
			<a href="<?php echo BASE_URL; ?>//pages/articles/articles.php">Articles</a>
		</li>
		<li class="<?php echo isActivePage('/pages/contact/contact.php') ?>">
			<a href="<?php echo BASE_URL; ?>/pages/contact/contact.php">Contact Us</a>
		</li>
		<?php if (isset($_SESSION['role']) && ($_SESSION['role'] != 'CUSTOMER')) : ?>
			<li class="<?php echo isActivePage('/pages/dashboard/' . strtolower($_SESSION['role']) . '/' . strtolower($_SESSION['role']) . '_dashboard.php') ?>">
				<a href="<?php echo BASE_URL; ?>/pages/dashboard/<?= strtolower($_SESSION['role']) ?>/<?= strtolower($_SESSION['role']) ?>_dashboard.php"><?= ucwords(strtolower($_SESSION['role'])) ?> Dashboard</a>
			</li>
		<?php endif; ?>
		<li class="">
			<?php if (isset($_SESSION['user_id'])) : ?>
				<a class=" logged-user-btn" href="<?php echo BASE_URL; ?>/pages/profile/profile.php"><span class="material-symbols-rounded material-symbols-rounded-filled">
						account_circle
					</span>
					<span><?php echo $_SESSION['user_first_name']; ?></span>
				</a>
			<?php else : ?>
				<a class="login-register-btn" href="<?php echo BASE_URL; ?>/pages/auth/login.php">Login / Signup</a>
			<?php endif; ?>
		</li>
	</ul>
</nav>
