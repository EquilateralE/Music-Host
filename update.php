<?php require 'config.php'; require 'auth.php'; require_admin();
if(empty($_SESSION['is_admin'])){ header('Location: index.php'); exit; }

$id = (int)($_GET['id'] ?? 0);
$st=$pdo->prepare('SELECT * FROM tracks WHERE id=?'); $st->execute([$id]);
$t=$st->fetch(); if(!$t){ header('Location: admin.php'); exit; }

$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $title=trim($_POST['title']??''); $artist=trim($_POST['artist']??'');
  $album=trim($_POST['album']??'');
  $audio=$_FILES['audio']??null; $cover=$_FILES['cover']??null;
  $fileA=$t['file']; $fileC=$t['cover'];

  if($title && $artist){
    $finfo=finfo_open(FILEINFO_MIME_TYPE);

    // Remplacement audio (optionnel)
    if($audio && $audio['error']===0){
      $mimeA=finfo_file($finfo,$audio['tmp_name']);
      if(!in_array($mimeA,['audio/mpeg','audio/mp3','audio/ogg','audio/wav'])) $err='Format audio invalide.';
      else{
        if(is_file('uploads/audio/'.$fileA)) unlink('uploads/audio/'.$fileA);
        $extA=strtolower(pathinfo($audio['name'],PATHINFO_EXTENSION)) ?: 'mp3';
        $fileA=uniqid('a_').'.'.$extA;
        move_uploaded_file($audio['tmp_name'],'uploads/audio/'.$fileA);
      }
    }

    // Remplacement cover (optionnel)
    if(!$err && $cover && $cover['error']===0){
      $mimeC=finfo_file($finfo,$cover['tmp_name']);
      if(!in_array($mimeC,['image/jpeg','image/png','image/webp'])) $err='Pochette invalide.';
      else{
        if($fileC && is_file('uploads/covers/'.$fileC)) unlink('uploads/covers/'.$fileC);
        $extC=strtolower(pathinfo($cover['name'],PATHINFO_EXTENSION)) ?: 'jpg';
        $fileC=uniqid('c_').'.'.$extC;
        move_uploaded_file($cover['tmp_name'],'uploads/covers/'.$fileC);
      }
    }
    if(isset($finfo)) finfo_close($finfo);

    if(!$err){
      $up=$pdo->prepare('UPDATE tracks SET title=?, artist=?, album=?, file=?, cover=? WHERE id=?');
      $up->execute([$title,$artist,$album?:null,$fileA,$fileC,$id]);
      header('Location: admin.php'); exit;
    }
  } else $err='Champs requis manquants.';
}
?>
<!doctype html><html lang="fr"><head>
<meta charset="utf-8"><title>Modifier</title>
<link rel="stylesheet" href="style.css"></head><body>
  <h2 class="page-title">Modifier #<?= $t['id'] ?></h2>
  <?php if($err): ?><div class="error-msg"><?= htmlspecialchars($err) ?></div><?php endif; ?>
  <form class="form" method="post" enctype="multipart/form-data">
    <label>Titre*</label><input name="title" value="<?= htmlspecialchars($t['title']) ?>" required>
    <label>Artiste*</label><input name="artist" value="<?= htmlspecialchars($t['artist']) ?>" required>
    <label>Album</label><input name="album" value="<?= htmlspecialchars($t['album']) ?>">
    <label>Remplacer l'audio</label><input type="file" name="audio" accept=".mp3,.ogg,.wav">
    <label>Remplacer la pochette</label><input type="file" name="cover" accept=".jpg,.jpeg,.png,.webp">
    <button class="btn" type="submit">Enregistrer</button>
    <a class="btn-ghost" href="admin.php">Annuler</a>
  </form>
</body></html>
