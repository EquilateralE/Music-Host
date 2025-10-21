<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['user'] ?? '');
    $p = $_POST['pass'] ?? '';

    $st = $pdo->prepare('SELECT id, username, password, is_admin FROM utilisateurs WHERE username=?');
    $st->execute([$u]);
    $row = $st->fetch();

    if ($row && password_verify($p, $row['password'])) {
        $_SESSION['user_id']  = (int)$row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['is_admin'] = (int)$row['is_admin']; // <-- ici on stocke bien

        header('Location: music.php'); 
        exit;
    } else {
        header('Location: index.php?err=1'); // mauvais login
        exit;
    }
}
