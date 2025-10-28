<?php
session_start();

require_once __DIR__ . "/../src/bdd/Bdd.php";
require_once __DIR__ . "/../src/repository/UserRepo.php";
require_once __DIR__ . "/../src/modele/User.php";
require_once __DIR__ . "/../src/repository/OffreRepo.php";
require_once __DIR__ . "/../src/modele/offre.php";

use repository\UserRepo;
use repository\OffreRepo;

// Déconnexion
if (!empty($_GET['deco']) && $_GET['deco'] === 'true') {
    session_destroy();
    header("Location: ../index.php");
    exit;
}

// Récupération de l'utilisateur connecté (pour le header)
$userLoggedIn = null;
if (!empty($_SESSION['connexion']) && $_SESSION['connexion'] === true && !empty($_SESSION['id_user'])) {
    $userRepo = new UserRepo();
    $userLoggedIn = $userRepo->getUserById($_SESSION['id_user']);
}

// Récupération de toutes les offres
$offreRepo = new OffreRepo();
$offres = $offreRepo->listeOffre();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Toutes les Offres - École Sup.</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
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

        html, body {
            margin:0; padding:0;
            font-family:'Poppins',sans-serif;
            background:var(--background-color);
            color:var(--text-color);
        }

        header {
            background:var(--surface-color);
            box-shadow:var(--shadow);
            position:sticky; top:0; z-index:1000;
        }

        header .container {
            max-width:1200px; margin:auto;
            display:flex; justify-content:space-between;
            align-items:center; height:70px;
            padding:0 20px;
        }

        .logo { font-size:1.6rem; font-weight:700; color:var(--primary-color); text-decoration:none; }

        nav ul { list-style:none; display:flex; gap:30px; margin:0; padding:0; }
        nav a {
            text-decoration:none; color:var(--text-color); font-weight:500;
            position:relative; padding-bottom:5px;
        }
        nav a::after {
            content:''; position:absolute; left:0; bottom:0;
            height:2px; width:0; background:var(--secondary-color);
            transition:width .3s ease;
        }
        nav a:hover { color:var(--primary-color); }
        nav a:hover::after { width:100%; }
        nav a.active { color:var(--primary-color); }
        nav a.active::after { width:100%; }

        main { padding:50px 0; }

        .container { max-width:1200px; margin:auto; padding:0 20px; }

        h1 {
            text-align:center; color:var(--primary-color);
            font-size:2rem; margin-bottom:10px;
        }

        .sub { text-align:center; color:#5c6b74; font-weight:500; margin-bottom:40px; }

        .grid {
            display:grid;
            grid-template-columns:repeat(3,1fr);
            gap:24px;
        }

        @media (max-width:1000px){ .grid{grid-template-columns:repeat(2,1fr);} }
        @media (max-width:640px){ .grid{grid-template-columns:1fr;} }

        .card {
            background:var(--surface-color);
            border-radius:var(--radius);
            box-shadow:var(--shadow);
            padding:22px;
            display:flex;
            flex-direction:column;
            gap:12px;
            border:1px solid #e9eef0;
            transition:transform .18s ease, box-shadow .18s ease;
        }

        .card:hover { transform:translateY(-4px); box-shadow:0 10px 26px rgba(0,0,0,.08); }

        .badge {
            background:var(--chip);
            color:#0b5d6b;
            padding:6px 10px;
            border-radius:999px;
            font-size:.8rem;
            font-weight:600;
            align-self:flex-start;
        }

        .card h3 { color:var(--primary-color); font-size:1.1rem; margin:0; }

        .card p { color:#53626a; flex-grow:1; }

        .job-meta { display:flex; flex-wrap:wrap; gap:8px; font-size:.85rem; color:#53626a; }
        .chip {
            background:var(--chip);
            border-radius:999px;
            padding:4px 8px;
            font-weight:600;
            color:#0b5d6b;
        }

        footer {
            background:var(--primary-color);
            color:var(--light-text-color);
            text-align:center;
            padding:40px 20px;
            margin-top:50px;
        }
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
                <li><a href="entreprise.php">Entreprises</a></li>
                <li><a href="supportContact.php">Contact</a></li>

                <?php if ($userLoggedIn): ?>
                    <li><a href="forum.php">Forum</a></li>
                    <li><a href="?deco=true">Déconnexion</a></li>
                <?php else: ?>
                    <li><a href="connexion.php">Connexion</a></li>
                    <li><a href="inscription.php">Inscription</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<main>
    <div class="container">
        <h1>Toutes nos offres d'emploi</h1>
        <p class="sub">Découvrez toutes les opportunités proposées par nos entreprises partenaires.</p>

        <div class="grid">
            <?php foreach ($offres as $offre): ?>
                <article class="card">
                    <span class="badge"><?= htmlspecialchars($offre->getTypeOffre() ?? 'Non spécifié') ?></span>

                    <h3><?= htmlspecialchars($offre->getTitre()) ?></h3>

                    <div class="row-line">
                        📍 <?= htmlspecialchars($offre->getRue()) ?>,
                        <?= htmlspecialchars($offre->getCp()) ?> <?= htmlspecialchars($offre->getVille()) ?>
                    </div>

                    <p><?= nl2br(htmlspecialchars($offre->getDescription())) ?></p>

                    <div class="job-meta">
                        <?php if ($offre->getSalaire()): ?>
                            <span class="chip">💶 <?= htmlspecialchars($offre->getSalaire()) ?></span>
                        <?php endif; ?>

                        <?php if ($offre->getEtat()): ?>
                            <span class="chip">Statut : <?= htmlspecialchars($offre->getEtat()) ?></span>
                        <?php endif; ?>

                        <?php if ($offre->getDateCreation()): ?>
                            <span class="chip">Publié le <?= htmlspecialchars($offre->getDateCreation()) ?></span>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>

            <?php if (empty($offres)): ?>
                <p>Aucune offre disponible pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<footer>
    &copy; 2025 École Supérieure — Tous droits réservés
</footer>

</body>
</html>
