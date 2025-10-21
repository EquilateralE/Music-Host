<?php
require 'config.php';

$err=''; $ok='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $u=trim($_POST['username']??'');
  $e=trim($_POST['email']??'');
  $p=$_POST['password']??'';
  $p2=$_POST['password2']??'';

  if(!$u || !$p || !$p2){ $err='Champs requis manquants.'; }
  elseif($p!==$p2){ $err='Les mots de passe ne correspondent pas.'; }
  else{

    $st=$pdo->prepare('SELECT id FROM utilisateurs WHERE username=? OR email=?');
    $st->execute([$u,$e?:null]);
    if($st->fetch()){ $err='Utilisateur ou email déjà pris.'; }
    else{
      $hash=password_hash($p,PASSWORD_DEFAULT);
      $ins=$pdo->prepare('INSERT INTO utilisateurs(username,email,password) VALUES (?,?,?)');
      $ins->execute([$u, $e?:null, $hash]);
      $ok='Compte créé, vous pouvez vous connecter.';
    }
  }
}
?>
<!doctype html><html lang="fr"><head>
<meta charset="utf-8"><title>Inscription</title>
<link rel="stylesheet" href="style.css"></head><body>
  <h2 class="page-title">Créer un compte</h2>
  <?php if($err): ?><div class="error-msg"><?=htmlspecialchars($err)?></div><?php endif; ?>
  <?php if($ok): ?><div class="success-msg"><?=htmlspecialchars($ok)?></div><?php endif; ?>
  <form class="form" method="post">
    <label>Nom d'utilisateur*</label><input name="username" required>
    <label>Email</label><input name="email" type="email">
    <label>Mot de passe*</label><input name="password" type="password" required>
    <label>Confirmer*</label><input name="password2" type="password" required>
    <button class="btn" type="submit">S'inscrire</button>
    <a class="btn-ghost" href="index.php">Annuler</a>
  </form>
</body></html>
