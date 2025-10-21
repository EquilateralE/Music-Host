<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Vidéo plein écran</title>
  <style>
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      background:
    radial-gradient(60% 80% at 80% 20%, rgba(255,42,82,.12), transparent 60%),
    radial-gradient(50% 70% at 20% 80%, rgba(255,42,82,.10), transparent 60%),
    url("image/cyber_bg.svg") center/cover fixed no-repeat;
  background-color:#0b0b0f;
      overflow: hidden;
    }

    video {
      position: fixed;
      top: 0; left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover; 
    }
  </style>
</head>
<body>
  
  <video src="image/Kiryu.mp4" autoplay muted loop playsinline></video>
</body>
</html>
