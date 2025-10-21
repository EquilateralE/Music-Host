<?php
// Fonctions de gestion d'authentification et autorisations

function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function current_username() {
    return $_SESSION['username'] ?? null;
}

function is_admin() {
    return isset($_SESSION['is_admin']) && (int)$_SESSION['is_admin'] === 1;
}

function require_login() {
    if (!current_user_id()) {
        header('Location: index.php?err=3'); // "Veuillez vous connecter"
        exit;
    }
}

function require_admin() {
    if (!is_admin()) {
        header('Location: index.php?err=4'); // "Accès réservé à l'admin"
        exit;
    }
}
