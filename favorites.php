<?php
require 'config.php';
require 'auth.php';
require_login(); // bloque si non connecté

$uid = current_user_id();

// Récupère les morceaux favoris de l'utilisateur
$stmt = $pdo->prepare('
  SELECT t.*,
         (SELECT COUNT(*) FROM likes WHERE track_id = t.id) AS like_count
  FROM likes l
  JOIN tracks t ON t.id = l.track_id
  WHERE l.user_id = ?
  ORDER BY l.created_at DESC
');
$stmt->execute([$uid]);
$rows = $stmt->fetchAll();
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Mes favoris</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="topbar">
    <a class="btn" href="music.php">← Retour aux morceaux</a>
    <strong>❤️ Favoris de <?= htmlspecialchars(current_username()) ?></strong>
    <div>
      <a class="btn-ghost" href="logout.php">Déconnexion</a>
    </div>
  </div>

  <h2 class="page-title">❤️ Mes morceaux favoris</h2>

  <div class="cards">
    <?php if (!$rows): ?>
      <p style="text-align:center;width:100%;">Vous n’avez encore aucun favori.</p>
    <?php endif; ?>

    <?php foreach($rows as $r): ?>
      <div class="card">
        <div class="cover"
             style="background-image:url('<?= $r['cover']
               ? 'uploads/covers/'.htmlspecialchars($r['cover'])
               : 'https://via.placeholder.com/600x600?text=Cover' ?>')"></div>
        <div class="meta">
          <h3><?= htmlspecialchars($r['title']) ?></h3>
          <p class="artist">
            <?= htmlspecialchars($r['artist']) ?>
            <?= $r['album'] ? ' — '.htmlspecialchars($r['album']) : '' ?>
          </p>
          <audio controls preload="none">
            <source src="uploads/audio/<?= htmlspecialchars($r['file']) ?>" type="audio/mpeg">
          </audio>

          <div class="like-row">
            <button
              type="button"
              class="like-btn liked"
              data-track-id="<?= $r['id'] ?>"
            >❤️</button>
            <span class="like-count" data-track-id="<?= $r['id'] ?>">
              <?= (int)$r['like_count'] ?>
            </span>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Script AJAX pour liker/dé-liker -->
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const likeButtons = document.querySelectorAll('.like-btn[data-track-id]');
    likeButtons.forEach(btn => {
      btn.addEventListener('click', async () => {
        const id = btn.getAttribute('data-track-id');
        try {
          const res = await fetch('like.php', {
            method: 'POST',
            headers: {'X-Requested-With':'fetch'},
            body: new URLSearchParams({ track_id: id })
          });
          if (!res.ok) return;
          const data = await res.json();
          if (data.ok) {
            btn.classList.toggle('liked', !!data.liked);
            const cnt = document.querySelector(`.like-count[data-track-id="${id}"]`);
            if (cnt) cnt.textContent = data.count;

            // Si on est en page favoris et qu'on retire un like, on supprime la carte du DOM
            if (!data.liked) {
              btn.closest('.card').remove();
            }
          }
        } catch(e) { console.error(e); }
      });
    });
  });
  </script>
</body>
</html>
