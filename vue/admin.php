<?php
// FICHIER: vue/admin.php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../src/bdd/Bdd.php';
require_once __DIR__ . '/../src/repository/UserRepo.php';
require_once __DIR__ . '/../src/modele/User.php';

use repository\UserRepo;

// --- Récup utilisateur connecté
$userLoggedIn = null;
if (!empty($_SESSION['connexion']) && $_SESSION['connexion'] === true && !empty($_SESSION['id_user'])) {
    $userRepo      = new UserRepo();
    $userLoggedIn  = $userRepo->getUserById((int)$_SESSION['id_user']);
}

// --- Déterminer si admin (robuste)
$isAdmin = false;
if ($userLoggedIn) {
    if (method_exists($userLoggedIn, 'isAdmin')) {
        $isAdmin = (bool)$userLoggedIn->isAdmin();
    } elseif (method_exists($userLoggedIn, 'getRole')) {
        $role = strtolower((string)$userLoggedIn->getRole());
        $isAdmin = in_array($role, ['admin','role_admin'], true);
    } elseif (property_exists($userLoggedIn, 'role')) {
        $role = strtolower((string)$userLoggedIn->role);
        $isAdmin = in_array($role, ['admin','role_admin'], true);
    } elseif (!empty($_SESSION['role'])) {
        $role = strtolower((string)$_SESSION['role']);
        $isAdmin = in_array($role, ['admin','role_admin'], true);
    }
}

// --- Garde: accès réservé
if (!$userLoggedIn || !$isAdmin) {
    header('Location: ../index.php?forbidden=1');
    exit;
}

// Petites infos d’affichage
$prenom   = method_exists($userLoggedIn,'getPrenom') ? $userLoggedIn->getPrenom() : ($_SESSION['prenom'] ?? 'Admin');
$nom      = method_exists($userLoggedIn,'getNom')    ? $userLoggedIn->getNom()    : ($_SESSION['nom'] ?? '');
$nowLabel = date('d/m/Y H:i');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Administration.</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root{
            --primary-color:#0A4D68;
            --secondary-color:#088395;
            --accent-color:#F39C12;
            --background-color:#f8f9fa;
            --surface-color:#ffffff;
            --text-color:#343a40;
            --light-text-color:#f8f9fa;
            --shadow:0 4px 15px rgba(0,0,0,.07);
            --border-radius:8px;
            --ok:#2ecc71; --warn:#e67e22; --danger:#e74c3c;
        }
        *{box-sizing:border-box}
        body{margin:0;font-family:'Poppins',sans-serif;background:var(--background-color);color:var(--text-color);line-height:1.7}
        .container{max-width:1200px;margin:auto;padding:0 20px}
        header{background:var(--surface-color);box-shadow:var(--shadow);position:sticky;top:0;z-index:1000}
        header .container{display:flex;justify-content:space-between;align-items:center;height:70px}
        .logo{font-size:1.6rem;font-weight:700;color:var(--primary-color)}
        nav ul{list-style:none;display:flex;align-items:center;gap:30px;padding-left:0;margin:0}
        nav ul li a{position:relative;text-decoration:none;color:var(--text-color);font-weight:500;padding-bottom:5px;transition:color .3s}
        nav ul li a::after{content:'';position:absolute;width:0;height:2px;left:0;bottom:0;background:var(--secondary-color);transition:width .3s}
        nav ul li a:hover{color:var(--primary-color)}
        nav ul li a:hover::after{width:100%}
        nav .active{color:var(--primary-color)}
        nav .active::after{width:100%}

        /* Dropdown profil */
        .profile-dropdown{position:relative;display:inline-block}
        .profile-icon{font-size:1.5rem;cursor:pointer;padding:5px}
        .profile-icon::after{display:none!important}
        .dropdown-content{display:none;position:absolute;background:var(--surface-color);min-width:220px;box-shadow:var(--shadow);border-radius:var(--border-radius);padding:20px;right:0;top:100%;z-index:1001;text-align:center}
        .profile-dropdown:hover .dropdown-content{display:block}
        .dropdown-content span{display:block;font-size:1.1rem;font-weight:600;color:var(--primary-color);margin-bottom:15px;white-space:nowrap}
        .dropdown-content a{display:block;padding:10px 15px;margin-bottom:8px;border-radius:5px;text-decoration:none;font-weight:500;transition:background .3s,color .3s;color:#fff!important}
        .profile-button{background:var(--secondary-color)}
        .profile-button:hover{background:var(--primary-color)}
        .logout-button{background:var(--danger)}
        .logout-button:hover{background:#c0392b}
        /* Admin top nav (green pills) */
        nav.admin-nav ul{list-style:none;display:flex;gap:14px;align-items:center;padding-left:0;margin:0}
        nav.admin-nav a.btn-admin{display:inline-block;background:var(--secondary-color);color:#fff;text-decoration:none;padding:8px 14px;border-radius:999px;font-weight:600}
        nav.admin-nav a.btn-admin:hover{background:var(--primary-color);color:#fff}
        nav.admin-nav a.btn-admin.active{background:var(--primary-color);color:#fff}
        /* disable underline animation for admin buttons */
        nav.admin-nav ul li a::after{display:none}
        /* Hero admin */
        .hero{background:linear-gradient(45deg, rgba(10,77,104,.9), rgba(8,131,149,.75)), url('https://source.unsplash.com/1600x600/?campus,building') center/cover no-repeat;height:280px;display:flex;align-items:center;justify-content:center;position:relative}
        .hero .content{color:#fff;text-align:center}
        .hero h2{margin:0 0 10px 0;font-size:2.4rem}
        .hero p{margin:0;opacity:.95}

        /* Bandeau infos */
        .info-bar{background:var(--surface-color);box-shadow:var(--shadow);border-radius:var(--border-radius);padding:18px;margin-top:-40px}
        .info-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}
        .info-card{background:var(--background-color);border-left:4px solid var(--secondary-color);border-radius:6px;padding:14px}
        .info-card h4{margin:0 0 6px 0;color:var(--primary-color)}
        .info-card p{margin:0;font-size:.95rem}

        /* Raccourcis */
        .section-title{text-align:center;font-size:1.8rem;color:var(--primary-color);margin:40px 0 20px}
        .quick-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:20px;margin-bottom:40px}
        .quick-card{background:var(--surface-color);border-radius:var(--border-radius);box-shadow:var(--shadow);padding:22px;transition:transform .2s, box-shadow .2s}
        .quick-card:hover{transform:translateY(-4px);box-shadow:0 10px 26px rgba(0,0,0,.1)}
        .quick-card h3{margin:0 0 10px 0;color:var(--primary-color)}
        .quick-card p{margin:0 0 14px 0}
        .quick-card a{display:inline-block;background:var(--secondary-color);color:#fff;text-decoration:none;padding:10px 14px;border-radius:6px;font-weight:600}
        .quick-card a:hover{background:var(--primary-color)}

        footer{background:var(--primary-color);color:#fff;padding:50px 0 20px;margin-top:40px;text-align:center}
        .footer-grid{display:flex;flex-wrap:wrap;gap:40px;justify-content:space-around;margin-bottom:30px}
        .footer-grid>div{flex:1 1 200px}
        footer a{color:rgba(255,255,255,.85);text-decoration:none}
        footer a:hover{text-decoration:underline}
        .footer-bottom{padding-top:20px;border-top:1px solid rgba(255,255,255,.2);opacity:.9}
    </style>
</head>
<body>

<header>
    <div class="container">
        <h1 class="logo"> Administration</h1>
        <nav class="admin-nav">
            <ul>
                <li><a href="../index.php" class="btn-admin active">Accueil</a></li>
                <li><a href="admin.php" class="btn-admin active">Tableau de bord</a></li>
                <li><a href="adminEntreprise.php" class="btn-admin">Entreprises</a></li>
                <li><a href="adminEvent.php" class="btn-admin">Événements</a></li>
                <li><a href="adminOffre.php" class="btn-admin">Offres</a></li>
                <li><a href="adminUser.php" class="btn-admin">Utilisateurs</a></li>
                <li class="profile-dropdown" style="margin-left:auto">
                    <a href="profilUser.php" class="profile-icon">👤</a>
                    <div class="dropdown-content">
                        <span>Bonjour, <?= htmlspecialchars($userLoggedIn->getPrenom()) ?> !</span>
                        <a href="profilUser.php" class="profile-button">Mon Profil</a>
                        <a href="?deco=true" class="logout-button">Déconnexion</a>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
</header>

<section class="hero">
    <div class="content">
        <h2>Espace Administration</h2>
        <p>Bienvenue <?= htmlspecialchars((string)$prenom) . ' ' . htmlspecialchars((string)$nom) ?> — <?= $nowLabel ?></p>
    </div>
</section>

<div class="container">
    <div class="info-bar">
        <div class="info-grid">
            <div class="info-card">
                <h4>Statut</h4>
                <p>Connecté en tant que <strong>Administrateur</strong></p>
            </div>
            <div class="info-card">
                <h4>Utilisateur</h4>
                <p><?= htmlspecialchars((string)$prenom) . ' ' . htmlspecialchars((string)$nom) ?></p>
            </div>
            <div class="info-card">
                <h4>Dernière action</h4>
                <p>Accès au tableau de bord — <?= $nowLabel ?></p>
            </div>
            <div class="info-card">
                <h4>Raccourci</h4>
                <p><a href="profilUser.php" style="color:var(--secondary-color);text-decoration:none;">Voir mon profil →</a></p>
            </div>
        </div>
    </div>

    <h3 class="section-title">Gestion rapide</h3>
    <div class="quick-grid">
        <div class="quick-card">
            <h3>Entreprises</h3>
            <p>Créer, modifier et supprimer les fiches entreprise.</p>
            <a href="adminEntreprise.php">Gérer les entreprises</a>
        </div>
        <div class="quick-card">
            <h3>Utilisateurs</h3>
            <p>Comptes, rôles et droits d’accès.</p>
            <a href="adminUsers.php">Gérer les utilisateurs</a>
        </div>
        <div class="quick-card">
            <h3>Événements</h3>
            <p>Programmer et publier les événements.</p>
            <a href="adminEvenements.php">Gérer les événements</a>
        </div>
        <div class="quick-card">
            <h3>Formations</h3>
            <p>Catalogue, matières et intervenants.</p>
            <a href="adminFormations.php">Gérer les formations</a>
        </div>
        <div class="quick-card">
            <h3>Support & Contact</h3>
            <p>Tickets et messages entrants.</p>
            <a href="adminSupport.php">Gérer le support</a>
        </div>
        <div class="quick-card">
            <h3>Paramètres</h3>
            <p>Options générales du site.</p>
            <a href="adminSettings.php">Ouvrir les paramètres</a>
        </div>
    </div>
</div>

<footer>
    <div class="footer-grid container">
        <div>
            <h4>École Sup.</h4>
            <p>123 Rue de l'Innovation, 75000 Paris</p>
            <p>contact@ecolesup.fr</p>
            <p>+33 1 23 45 67 89</p>
        </div>

        <div>
            <h4>Réseaux sociaux</h4>
            <div class="footer-socials" style="display:flex;gap:15px;justify-content:center">
                <a href="#">FB</a><a href="#">TW</a><a href="#">IG</a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">&copy; 2025 École Sup. Tous droits réservés.</div>
</footer>

</body>
</html>