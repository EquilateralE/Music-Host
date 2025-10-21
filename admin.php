<?php
require 'config.php';   // démarre la session
require 'auth.php';     // charge les helpers
require_admin();        // bloque si pas admin

if(empty($_SESSION['is_admin'])){ header('Location: index.php'); exit; }
$rows = $pdo->query('SELECT * FROM tracks ORDER BY id DESC')->fetchAll();
?>
<!doctype html><html lang="fr"><head>
<meta charset="utf-8"><title>Admin – Music Host</title>
<link rel="stylesheet" href="style.css">
</head><body>
  <h2 class="page-title">Admin – Morceaux</h2>
  <p class="center"><a class="btn" href="create.php">➕ Ajouter un morceau</a> · <a href="logout.php">Déconnexion</a></p>
  <div class="cards">
    <?php foreach($rows as $r): ?>
      <div class="card">
        <div class="cover"
             style="background-image:url('<?= $r['cover']? 'uploads/covers/'.htmlspecialchars($r['cover']) : 'https://via.placeholder.com/600x600?text=Cover' ?>')"></div>
        <div class="meta">
          <h3>#<?= $r['id'] ?> — <?= htmlspecialchars($r['title']) ?></h3>
          <p class="artist"><?= htmlspecialchars($r['artist']) ?><?= $r['album']?' — '.htmlspecialchars($r['album']):'' ?></p>
          <audio controls preload="none">
            <source src="uploads/audio/<?= htmlspecialchars($r['file']) ?>" type="audio/mpeg">
          </audio>
          <p class="actions">
            <a href="update.php?id=<?= $r['id'] ?>">Modifier</a>
            <a href="delete.php?id=<?= $r['id'] ?>" onclick="return confirm('Supprimer ce morceau ?');">Supprimer</a>
          </p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</body></html>
