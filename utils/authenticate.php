<?php

function authenticate($roles = array('ADMIN', 'USER'))
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . '/pages/auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit();
    }

    if (!in_array($_SESSION['role'], $roles)) {
        header('Location: ' . BASE_URL . '/pages/auth/unauthorized.php');
        exit();
    }
}
