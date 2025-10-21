<?php
require 'config.php';
require 'auth.php';

header('Content-Type: application/json');

if (!current_user_id()) {
  http_response_code(401);
  echo json_encode(['ok'=>false, 'error'=>'not_logged']); exit;
}

$uid = current_user_id();
$track_id = (int)($_POST['track_id'] ?? 0);
if ($track_id <= 0) {
  http_response_code(400);
  echo json_encode(['ok'=>false, 'error'=>'bad_track']); exit;
}

// toggle like
$chk = $pdo->prepare('SELECT 1 FROM likes WHERE user_id=? AND track_id=?');
$chk->execute([$uid, $track_id]);

if ($chk->fetch()) {
  $pdo->prepare('DELETE FROM likes WHERE user_id=? AND track_id=?')->execute([$uid, $track_id]);
  $liked = false;
} else {
  $pdo->prepare('INSERT INTO likes(user_id, track_id) VALUES (?,?)')->execute([$uid, $track_id]);
  $liked = true;
}

// récup compteur
$cnt = $pdo->prepare('SELECT COUNT(*) AS c FROM likes WHERE track_id=?');
$cnt->execute([$track_id]);
$count = (int)$cnt->fetch()['c'];

echo json_encode(['ok'=>true, 'liked'=>$liked, 'count'=>$count]);
