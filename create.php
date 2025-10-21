<?php
require 'config.php';
require 'auth.php';
require_admin();

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title  = trim($_POST['title'] ?? '');
    $artist = trim($_POST['artist'] ?? '');
    $album  = trim($_POST['album'] ?? '');
    $audio  = $_FILES['audio'] ?? null;
    $cover  = $_FILES['cover'] ?? null;

    if (!$title || !$artist || !$audio || $audio['error'] !== 0) {
        $err = "Titre, artiste et fichier audio obligatoires.";
    } else {
        // Dossiers cibles
        $audioDir = __DIR__ . '/uploads/audio/';
        $coverDir = __DIR__ . '/uploads/covers/';
        if (!is_dir($audioDir)) { mkdir($audioDir, 0777, true); }
        if (!is_dir($coverDir)) { mkdir($coverDir, 0777, true); }

        // Sauvegarde fichier audio
        $extA  = strtolower(pathinfo($audio['name'], PATHINFO_EXTENSION)) ?: 'mp3';
        $fileA = uniqid('a_') . '.' . $extA;
        if (!move_uploaded_file($audio['tmp_name'], $audioDir . $fileA)) {
            $err = 'Échec upload audio (vérifie les droits/dossiers).';
        }

        // Sauvegarde pochette optionnelle
        $fileC = null;
        if (!$err && $cover && $cover['error'] === 0) {
            $extC  = strtolower(pathinfo($cover['name'], PATHINFO_EXTENSION)) ?: 'jpg';
            $fileC = uniqid('c_') . '.' . $extC;
            if (!move_uploaded_file($cover['tmp_name'], $coverDir . $fileC)) {
                $err = 'Échec upload pochette.';
            }
        }

        // Enregistre en base
        if (!$err) {
            $st = $pdo->prepare('INSERT INTO tracks (title, artist, album, file, cover) VALUES (?,?,?,?,?)');
            $st->execute([$title, $artist, $album ?: null, $fileA, $fileC]);
            header('Location: music.php');
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Ajouter un morceau</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h2>Ajouter un morceau</h2>

  <?php if ($err): ?>
    <div class="error-msg"><?= htmlspecialchars($err) ?></div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <label>Titre* <input type="text" name="title" required></label>
    <label>Artiste* <input type="text" name="artist" required></label>
    <label>Album <input type="text" name="album"></label>
    <label>Fichier audio* (.mp3/.ogg/.wav) <input type="file" name="audio" accept=".mp3,.ogg,.wav" required></label>
    <label>Pochette (jpg/png/webp) <input type="file" name="cover" accept=".jpg,.jpeg,.png,.webp"></label>
    <button type="submit">Enregistrer</button>
    <a href="music.php">Annuler</a>
  </form>
</body>
</html>
