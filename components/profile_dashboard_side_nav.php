<!-- isActivePage, BASE_URL and getFullUrl are defined in utils\header_navigation_bar.php -->

<?php
$links = array(
    array('name' => 'Profile', 'icon' => 'account_circle', 'url' => '/pages/profile/profile.php'),
    array('name' => 'My Orders', 'icon' => 'orders', 'url' => '/pages/profile/my_orders.php'),
    array('name' => 'My Reservations', 'icon' => 'event_seat', 'url' => '/pages/profile/my_reservations.php'),
)

?>

<nav class="dashboard-nav">
    <?php
    foreach ($links as $link) {
        $isActive = isActivePage($link['url']);
        $href = BASE_URL . $link['url'];
        $icon = $link['icon'];
        $name = $link['name'];

        echo <<< HTML
        <a class="dashboard-nav-link $isActive" href="$href">
            <span class="link-icon material-symbols-rounded">$icon</span>
            <span class="link-text">$name</span>
        </a>
        HTML;
    }

    ?>
</nav>
