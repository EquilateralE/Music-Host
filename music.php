<?php
require 'config.php';   // démarre la session
require 'auth.php';     // charge les fonctions ci-dessous
require_login();        // bloque si non connecté

$uid = current_user_id();

$rows = $pdo->query('
  SELECT t.*,
         COALESCE(lc.cnt,0) AS like_count,
         CASE WHEN ul.user_id IS NULL THEN 0 ELSE 1 END AS liked
  FROM tracks t
  LEFT JOIN (SELECT track_id, COUNT(*) cnt FROM likes GROUP BY track_id) lc ON lc.track_id = t.id
  LEFT JOIN (SELECT track_id, user_id FROM likes WHERE user_id = '.($uid?$uid:0).') ul ON ul.track_id = t.id
  ORDER BY t.id DESC
')->fetchAll();
?>
<!doctype html><html lang="fr"><head>
<meta charset="utf-8"><title>Galerie – Music Host</title>
<link rel="stylesheet" href="style.css">
</head><body>
  <div class="topbar">
    <a class="btn" href="index.php">Accueil</a>
    <?php if($uid): ?>
      <span>Connecté : <?= htmlspecialchars(current_username()) ?></span>
      <a class="btn" href="favorites.php">❤️ Favoris</a>
      <?php if(is_admin()): ?><a class="btn" href="admin.php">Admin</a><?php endif; ?>
      <a class="btn-ghost" href="logout.php">Déconnexion</a>
    <?php else: ?>
      <a class="btn" href="register.php">Inscription</a>
    <?php endif; ?>
  </div>

  <h2 class="page-title">🎵 Morceaux</h2>
  <div class="cards">
    <?php foreach($rows as $r): ?>
      <div class="card">
        <div class="cover"
             style="background-image:url('<?= $r['cover']? 'uploads/covers/'.htmlspecialchars($r['cover']) : 'https://via.placeholder.com/600x600?text=Cover' ?>')"></div>
        <div class="meta">
          <h3><?= htmlspecialchars($r['title']) ?></h3>
          <p class="artist"><?= htmlspecialchars($r['artist']) ?><?= $r['album']?' — '.htmlspecialchars($r['album']):'' ?></p>
          <audio controls preload="none">
            <source src="uploads/audio/<?= htmlspecialchars($r['file']) ?>" type="audio/mpeg">
          </audio>

          <div class="like-row">
          <button
            type="button"
            class="like-btn <?= $r['liked'] ? 'liked' : '' ?>"
            data-track-id="<?= $r['id'] ?>"
            <?= $uid ? '' : 'disabled title="Connectez-vous pour liker"' ?>
          >❤️</button>
          <span class="like-count" data-track-id="<?= $r['id'] ?>"><?= (int)$r['like_count'] ?></span>
          <noscript>
            <!-- Fallback si JS désactivé -->
            <form method="post" action="like.php" style="display:inline;">
              <input type="hidden" name="track_id" value="<?= $r['id'] ?>">
              <button type="submit">❤️</button>
            </form>
          </noscript>
        </div>

        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const likeButtons = document.querySelectorAll('.like-btn[data-track-id]');
      likeButtons.forEach(btn => {
        btn.addEventListener('click', async () => {
          if (btn.disabled) return;
          const id = btn.getAttribute('data-track-id');
          try {
            const res = await fetch('like.php', {
              method: 'POST',
              headers: {'X-Requested-With':'fetch'},
              body: new URLSearchParams({ track_id: id })
            });
            if (!res.ok) {
              if (res.status === 401) {
                window.location.href = 'index.php?err=3';
                return;
              }
              return; // erreur silencieuse et malicieuse
            }
            const data = await res.json();
            if (data.ok) {
              // toggle visuel
              btn.classList.toggle('liked', !!data.liked);
              // maj compteur
              const cnt = document.querySelector(`.like-count[data-track-id="${id}"]`);
              if (cnt) cnt.textContent = data.count;
            }
          } catch(e) {
            console.error(e);
          }
        });
      });
    });
    </script>

</body></html>
