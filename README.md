### Hébergeur de Musique — PHP / HTML / CSS

Un site d’hébergement/partage de morceaux musicaux permettant de s’inscrire, de se connecter, d’uploader des titres, de les liker, de les ajouter en favoris et de gérer ses propres uploads. Conçu en PHP natif, HTML5 et CSS3, avec stockage des fichiers sur disque et des métadonnées en base de données.

### Fonctionnalités

* Authentification (inscription, connexion, déconnexion)

* Upload de fichiers audio (MP3, WAV, etc. — configurables)

* Lecture intégrée via <audio>

* Likes et Favoris

* Listing et recherche basique des titres

* CRUD basique pour les morceaux (créer / mettre à jour / supprimer)

* Rôles simples (admin / utilisateur) pour la modération

* Support de pochettes (images) par morceau

### Architecture du projet
```
.
├─ image/                 # Ressources d’interface (icônes, logos, etc.)
├─ uploads/               # Dossier des fichiers audio uploadés (écrire + lire)
├─ admin.php              # Tableau de bord d’administration (modération, stats)
├─ auth.php               # Fonctions utilitaires d’authentification (session)
├─ config.php             # Connexion DB + configuration applicative
├─ create.php             # Formulaire / traitement d’ajout d’un morceau
├─ delete.php             # Suppression d’un morceau (vérifs d’autorisation)
├─ favorites.php          # Listing/gestion des favoris de l’utilisateur
├─ index.php              # Page d’accueil / flux principal des morceaux
├─ Kiryu.php              # Page affichant une courte vidéo. ("easter egg")
├─ like.php               # Gestion des likes (AJAX/redirection)
├─ login.php              # Page de connexion
├─ logout.php             # Déconnexion (destruction de session)
├─ music.php              # Page détail d’un morceau + lecteur
├─ register.php           # Page d’inscription
├─ style.css              # Styles globaux
└─ update.php             # Mise à jour des métadonnées d’un morceau
```



### Stack & Prérequis

PHP ≥ 8.1 (extensions : pdo, pdo_mysql, fileinfo)

Serveur web (Apache/Nginx) ou php -S pour du local

MySQL/MariaDB (ou compatible) pour les métadonnées

### Schéma SQL minimal

```
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  email VARCHAR(120) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('user','admin') DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tracks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(140) NOT NULL,
  description TEXT,
  file_path VARCHAR(255) NOT NULL,
  cover_path VARCHAR(255) NULL,
  duration_seconds INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE likes (
  user_id INT NOT NULL,
  track_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, track_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (track_id) REFERENCES tracks(id) ON DELETE CASCADE
);

CREATE TABLE favorites (
  user_id INT NOT NULL,
  track_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, track_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (track_id) REFERENCES tracks(id) ON DELETE CASCADE
);
```

### Hébergement local

Ce site est maintenu via XAMPP.
