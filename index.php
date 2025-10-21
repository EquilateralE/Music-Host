<?php 
require 'config.php'; 
$isLoggedIn = !empty($_SESSION['user_id']); 
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Accueil – Music Host</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="home-container">

    <?php if ($isLoggedIn): ?>
      <a class="big-btn" href="music.php">Accéder aux morceaux</a>
      <p style="margin-top:16px;">
        Connecté comme <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
        <?php if (!empty($_SESSION['is_admin'])): ?>
          — <a href="admin.php">Espace admin</a>
        <?php endif; ?>
        | <a href="logout.php">Déconnexion</a>
      </p>

    <?php else: ?>
      <form class="login-form" method="post" action="login.php">
        <h3>Connexion</h3>
        <?php if (isset($_GET['err']) && $_GET['err']==1): ?>
          <div class="error-msg">Identifiant ou mot de passe incorrect.</div>
        <?php elseif (isset($_GET['err']) && $_GET['err']==3): ?>
          <div class="error-msg">Veuillez vous connecter pour accéder aux morceaux.</div>
        <?php endif; ?>
        <label for="user">Utilisateur :</label>
        <input type="text" name="user" id="user" required>
        <label for="pass">Mot de passe :</label>
        <input type="password" name="pass" id="pass" required>
        <input type="submit" value="Se connecter">
      </form>

      <p style="text-align:center;margin-top:8px;">
        Pas de compte ? <a href="register.php">Inscrivez-vous</a><br><a href="Kiryu.php" class="hidden">G</a>
      </p>
    <?php endif; ?>
    <?php if (isset($_GET['err']) && $_GET['err']==4): ?>
      <div class="error-msg">Accès réservé à l’administrateur.</div>
    <?php endif; ?>

  </div>
</body>
</html>
