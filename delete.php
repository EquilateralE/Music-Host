<?php require 'config.php'; require 'auth.php'; require_admin();
if(empty($_SESSION['is_admin'])){ header('Location: index.php'); exit; }

$id=(int)($_GET['id']??0);
$st=$pdo->prepare('SELECT file,cover FROM tracks WHERE id=?'); $st->execute([$id]);
$t=$st->fetch();
if($t){
  if($t['file'] && is_file('uploads/audio/'.$t['file'])) unlink('uploads/audio/'.$t['file']);
  if($t['cover'] && is_file('uploads/covers/'.$t['cover'])) unlink('uploads/covers/'.$t['cover']);
  $pdo->prepare('DELETE FROM tracks WHERE id=?')->execute([$id]);
}
header('Location: admin.php'); exit;
