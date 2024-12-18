<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ../../login.php');
        exit;
    }
}

function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ../../index.php');
        exit;
    }
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getCurrentUserType() {
    return $_SESSION['user_type'] ?? null;
}

function logout() {
    session_start();
    session_destroy();
    header('Location: ../../login.php');
    exit;
}
?>
