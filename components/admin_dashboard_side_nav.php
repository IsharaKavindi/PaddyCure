<!-- isActivePage, BASE_URL and getFullUrl are defined in utils\header_navigation_bar.php -->

<?php
$links = array(
    array('name' => 'Statistics', 'url' => '/pages/dashboard/admin/admin_dashboard.php'),
    array('name' => 'Users', 'url' => '/pages/dashboard/admin/users/view_users.php'),
    array('name' => 'Messages', 'url' => '/pages/dashboard/admin/messages/messages.php'),
    array('name' => 'Articles',  'url' => '/pages/dashboard/admin/articles/view_articles.php'),
   
)

?>

<nav class="dashboard-nav">
    <?php
    foreach ($links as $link) {
        $isActive = isActivePage($link['url']);
        $href = BASE_URL . $link['url'];
        $name = $link['name'];

        echo <<< HTML
        <a class="dashboard-nav-link $isActive" href="$href">
            <span class="link-text">$name</span>
        </a>
        HTML;
    }

    ?>
</nav>
