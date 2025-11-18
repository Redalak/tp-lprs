<?php
// Définir le titre de la page
$pageTitle = 'Entreprise';

// Inclure l'en-tête qui gère la session et l'authentification
require_once __DIR__ . '/../includes/header.php';

// Inclure les fichiers nécessaires
require_once __DIR__ . "/../src/bdd/Bdd.php";
require_once __DIR__ . "/../src/repository/EntrepriseRepo.php";
require_once __DIR__ . "/../src/repository/UserRepo.php";
require_once __DIR__ . "/../src/modele/Entreprise.php";
require_once __DIR__ . "/../src/modele/User.php";

// On inclut aussi les offres
require_once __DIR__ . "/../src/repository/OffreRepo.php";
require_once __DIR__ . "/../src/modele/offre.php";

use repository\EntrepriseRepo;
use repository\UserRepo;
use repository\OffreRepo;

?>
<?php

// Déconnexion (copié de votre index.php)
if (!empty($_GET['deco']) && $_GET['deco'] === 'true') {
    session_destroy();
    header("Location: ../index.php");
    exit;
}

// Récupérer l'utilisateur connecté (pour le header)
$userLoggedIn = null;
if (!empty($_SESSION['connexion']) && $_SESSION['connexion'] === true && !empty($_SESSION['id_user'])) {
    $userRepo = new UserRepo();
    $userLoggedIn = $userRepo->getUserById($_SESSION['id_user']);
}

// Récupérer la liste des entreprises
$entrepriseRepo = new EntrepriseRepo();
$entreprises = $entrepriseRepo->listeEntreprise();

// Récupérer les 3 dernières offres publiées
$offreRepo = new OffreRepo();
$dernieresOffres = $offreRepo->getDernieresOffres(3);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Entreprises Partenaires - École Sup.</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Shared site styles -->
    <link href="../assets/css/site.css" rel="stylesheet">

    <style>
        :root{
            --primary-color:#0A4D68;
            --secondary-color:#088395;
            --background-color:#f8f9fa;
            --surface-color:#ffffff;
            --text-color:#343a40;
            --light-text-color:#f8f9fa;
            --shadow:0 4px 15px rgba(0,0,0,.07);
            --radius:12px;
            --chip:#eef6f8;
        }

        *{box-sizing:border-box}

        /* --- CORRECTION STICKY FOOTER (1/3) --- */
        html {
            height: 100%;
        }

        /* --- CORRECTION STICKY FOOTER (2/3) --- */
        body{
            margin:0;
            font-family:'Poppins',sans-serif;
            background:var(--background-color);
            color:var(--text-color);
            line-height:1.7;

            /* Styles Flexbox pour le sticky footer */
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Hauteur min 100% de la fenêtre */
        }

        /* --- CORRECTION STICKY FOOTER (3/3) --- */
        /* Le conteneur principal grandit pour pousser le footer en bas */
        main {
            flex-grow: 1;
        }

        .container{max-width:1200px;margin:auto;padding:0 20px}

        /* Header */
        header{
            background:var(--surface-color);
            box-shadow:var(--shadow);
            position:sticky;top:0;z-index:1000;
        }
        header .container{
            display:flex;justify-content:space-between;align-items:center;height:70px;
        }
        .logo{font-size:1.6rem;font-weight:700;color:var(--primary-color);margin:0; text-decoration: none;}
        nav ul{
            list-style:none;display:flex; align-items: center; gap:30px;margin:0;padding:0;
        }
        nav a{
            text-decoration:none;color:var(--text-color);font-weight:500;
            position:relative;padding-bottom:5px;transition:color .3s ease;
        }
        nav a::after{
            content:'';position:absolute;left:0;bottom:0;height:2px;width:0;
            background:var(--secondary-color);transition:width .3s ease;
        }
        nav a:hover{color:var(--primary-color)}
        nav a:hover::after{width:100%}
        nav a.active{color:var(--primary-color)}
        nav a.active::after{width:100%}

        /* Titre page */
        .page-head{
            padding:40px 0 10px;
        }
        .page-head h1{
            margin:0;text-align:center;color:var(--primary-color);
            font-size:2rem;
        }
        .sub{
            text-align:center;margin:6px 0 24px;color:#5c6b74;font-weight:500;
        }

        /* Grille */
        .grid{
            display:grid;
            grid-template-columns:repeat(3,minmax(0,1fr));
            gap:24px;
        }
        @media (max-width:1000px){ .grid{grid-template-columns:repeat(2,1fr)} }
        @media (max-width:640px){ .grid{grid-template-columns:1fr} }

        /* Cartes */
        .card{
            background:var(--surface-color);
            border-radius:var(--radius);
            box-shadow:var(--shadow);
            padding:22px;
            display:flex;
            flex-direction:column;
            gap:12px;
            transition:transform .18s ease, box-shadow .18s ease;
            border:1px solid #e9eef0;
        }
        .card:hover{ transform:translateY(-4px); box-shadow:0 10px 26px rgba(0,0,0,.08); }

        .badge{
            display:inline-block;
            font-size:.78rem;
            background:var(--chip);
            color:#0b5d6b;
            padding:6px 10px;
            border-radius:999px;
            font-weight:600;
            align-self: flex-start;
        }
        .card h3{
            margin:0;color:var(--primary-color);font-size:1.1rem;
        }
        .card p{
            margin:0;color:#53626a;
            min-height:56px;
            flex-grow: 1; /* Pousse le bouton vers le bas */
        }
        .actions{margin-top:10px}
        .btn{
            display:inline-block;
            background:var(--primary-color);color:#fff;text-decoration:none;
            padding:10px 14px;border-radius:8px;font-weight:600;
            transition:transform .12s ease, opacity .2s ease, background .2s ease;
        }
        .btn:hover{ background:#06364b; transform:translateY(-1px); opacity:.95; }

        /* Section jobs */
        .jobs-section {
            padding:50px 0 60px;
        }

        .jobs-headline {
            text-align:center;
            color:var(--primary-color);
            font-size:1.6rem;
            font-weight:600;
            margin:0 0 8px;
        }

        .jobs-sub {
            text-align:center;
            color:#5c6b74;
            font-weight:500;
            margin:0 0 30px;
        }

        .job-card .row-line{
            font-size:.9rem;
            color:#53626a;
            line-height:1.5;
        }

        .job-meta{
            display:flex;
            flex-wrap:wrap;
            gap:8px;
            font-size:.8rem;
            color:#53626a;
        }
        .chip{
            background:var(--chip);
            border-radius:999px;
            padding:4px 8px;
            font-weight:600;
            color:#0b5d6b;
        }

        /* wrapper bouton "Voir plus" */
        .more-wrapper{
            text-align:center;
            margin-top:30px;
        }
        .btn-more{
            display:inline-block;
            background:var(--secondary-color);
            color:#fff;
            text-decoration:none;
            padding:10px 16px;
            border-radius:8px;
            font-weight:600;
            box-shadow:var(--shadow);
            transition:transform .12s ease, opacity .2s ease, background .2s ease;
        }
        .btn-more:hover{
            background:var(--primary-color);
            transform:translateY(-1px);
            opacity:.95;
        }

        /* Footer */
        footer{
            background:var(--primary-color);color:var(--light-text-color);
            text-align:center;padding:40px 20px;
        }

        /* Styles pour le dropdown profil */
        .profile-dropdown { position: relative; display: inline-block; }
        .profile-icon { font-size: 1.5rem; cursor: pointer; padding: 5px; }
        .profile-icon::after { display: none !important; }
        .dropdown-content {
            display: none; position: absolute; background-color: var(--surface-color);
            min-width: 220px; box-shadow: var(--shadow); border-radius: var(--radius);
            padding: 20px; right: 0; top: 100%; z-index: 1001; text-align: center;
        }
        .profile-dropdown:hover .dropdown-content { display: block; }
        .dropdown-content span {
            display: block; font-size: 1.1rem; font-weight: 600;
            color: var(--primary-color); margin-bottom: 15px; white-space: nowrap;
        }
        .dropdown-content a {
            display: block; width: auto; padding: 10px 15px;
            margin-bottom: 8px; border-radius: 5px; text-decoration: none;
            font-weight: 500; transition: background-color 0.3s ease, color 0.3s ease;
            color: var(--light-text-color) !important;
        }
        .dropdown-content a::after { display: none; }
        .profile-button { background-color: var(--secondary-color); }
        .profile-button:hover { background-color: var(--primary-color); color: var(--light-text-color) !important; }
        .logout-button { background-color: #e74c3c; }
        .logout-button:hover { background-color: #c0392b; color: var(--light-text-color) !important; }
    </style>
</head>
<body>

<header>
    <div class="container">
        <a href="../index.php" class="logo">École Sup.</a>
        <nav>
            <ul>
                <li><a href="../index.php">Accueil</a></li>
                <li><a href="formations.php">Formations</a></li>
                <li><a class="active" href="../vue/entreprise.php">Entreprises</a></li>
                <li><a href="../vue/offres.php">Offres</a></li>
                <li><a  href="../vue/evenement.php">Evenements</a></li>
                <li><a href="supportContact.php">Contact</a></li>

                <?php if ($userLoggedIn): ?>
                    <li><a href="forum.php">Forum</a></li>
                    <li class="profile-dropdown">
                        <a href="#" class="profile-icon">👤</a>
                        <div class="dropdown-content">
                            <span>Bonjour, <?= htmlspecialchars($userLoggedIn->getPrenom()) ?> !</span>
                            <a href="profilUser.php" class="profile-button">Mon Profil</a>
                            <a href="?deco=true" class="logout-button">Déconnexion</a>
                        </div>
                    </li>
                <?php else: ?>
                    <li><a href="connexion.php">Connexion</a></li>
                    <li><a href="inscription.php">Inscription</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<main>

    <!-- Section entreprises partenaires -->
    <div class="container page-head">
        <h1>Nos Entreprises Partenaires</h1>
        <div class="sub">Ils nous font confiance pour former leurs futurs talents.</div>
    </div>

    <div class="container">
        <div class="grid">
            <?php foreach($entreprises as $entreprise): ?>
                <article class="card">
                    <span class="badge"><?= htmlspecialchars($entreprise->getMotifPartenariat()) ?></span>

                    <h3><?= htmlspecialchars($entreprise->getNom()) ?></h3>

                    <p><?= htmlspecialchars($entreprise->getAdresse()) ?></p>

                    <div class="actions">
                        <a href="<?= htmlspecialchars($entreprise->getSiteWeb()) ?>"
                           class="btn"
                           target="_blank"
                           rel="noopener noreferrer">
                            Visiter le site
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>

            <?php if (empty($entreprises)): ?>
                <p>Aucune entreprise partenaire à afficher pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Section dernières offres -->
    <section class="jobs-section">
        <div class="container">
            <h2 class="jobs-headline">Dernières offres publiées</h2>
            <p class="jobs-sub">Les 3 opportunités les plus récentes proposées par nos partenaires.</p>

            <div class="grid">
                <?php foreach ($dernieresOffres as $offre): ?>
                    <article class="card job-card">
                        <!-- type_offre en badge -->
                        <?php if ($offre->getTypeOffre() !== null): ?>
                        <span class="badge">
                            <?= htmlspecialchars($offre->getTypeOffre()) ?>
                        </span>
                        <?php endif; ?>

                        <!-- titre -->
                        <h3><?= htmlspecialchars($offre->getTitre()) ?></h3>

                        <!-- lieu -->
                        <div class="row-line">
                            📍
                            <?= htmlspecialchars($offre->getRue()) ?>,
                            <?= htmlspecialchars($offre->getCp()) ?> <?= htmlspecialchars($offre->getVille()) ?>
                        </div>

                        <!-- description courte -->
                        <p>
                            <?= nl2br(htmlspecialchars($offre->getDescription())) ?>
                        </p>

                        <!-- meta salaire + état + date -->
                        <div class="job-meta">
                            <?php if ($offre->getSalaire() !== null && $offre->getSalaire() !== ''): ?>
                                <span class="chip">💶 <?= htmlspecialchars($offre->getSalaire()) ?></span>
                            <?php endif; ?>

                            <?php if ($offre->getEtat() !== ''): ?>
                                <span class="chip">Statut : <?= htmlspecialchars($offre->getEtat()) ?></span>
                            <?php endif; ?>

                            <?php if ($offre->getDateCreation() !== null && $offre->getDateCreation() !== ''): ?>
                                <span class="chip">
                                    Publié le :
                                    <?= htmlspecialchars($offre->getDateCreation()) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>

                <?php if (empty($dernieresOffres)): ?>
                    <p>Aucune offre de poste pour le moment.</p>
                <?php endif; ?>
            </div>

            <!-- bouton Voir plus -->
            <div class="more-wrapper">
                <a class="btn-more" href="offres.php">Voir plus</a>
            </div>

        </div>
    </section>

</main>

<footer>
    &copy; 2025 École Supérieure — Tous droits réservés
</footer>

<script src="../assets/js/site.js"></script>
</body>
</html>
