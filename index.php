<?php
session_start();
require_once __DIR__ . "/src/bdd/Bdd.php";
require_once __DIR__ . "/src/repository/EventRepo.php";

use repository\EventRepo;

// Déconnexion simple via ?deco=true
if (!empty($_GET['deco']) && $_GET['deco'] === 'true') {
    session_destroy();
    header("Location: index.php");
    exit;
}

// Récupération des 5 derniers événements
$eventRepo = new EventRepo();
$latestEvents = $eventRepo->getDerniersEvents(5); // méthode à créer dans EventRepo
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>École | Construire Demain</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Styles précédemment définis… */
        :root {
            --primary-color: #0A4D68;
            --secondary-color: #088395;
            --accent-color: #F39C12;
            --background-color: #f8f9fa;
            --surface-color: #ffffff;
            --text-color: #343a40;
            --light-text-color: #f8f9fa;
            --shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
            --border-radius: 8px;
        }
        body { margin: 0; font-family: 'Poppins', sans-serif; background-color: var(--background-color); color: var(--text-color); line-height: 1.7; }
        .container { max-width: 1200px; margin: auto; padding: 0 20px; }
        header { background: var(--surface-color); box-shadow: var(--shadow); position: sticky; top: 0; z-index: 1000; }
        header .container { display: flex; justify-content: space-between; align-items: center; height: 70px; }
        .logo { font-size: 1.6rem; font-weight: 700; color: var(--primary-color); }
        nav ul { list-style: none; display: flex; gap: 30px; padding-left: 0; margin: 0; }
        nav ul li a { text-decoration: none; color: var(--text-color); font-weight: 500; position: relative; padding-bottom: 5px; transition: color 0.3s ease; }
        nav ul li a::after { content: ''; position: absolute; width: 0; height: 2px; bottom: 0; left: 0; background-color: var(--secondary-color); transition: width 0.3s ease; }
        nav ul li a:hover { color: var(--primary-color); }
        nav ul li a:hover::after { width: 100%; }

        .hero { background: url('https://source.unsplash.com/1600x900/?university,modern,architecture') no-repeat center center/cover; height: 500px; position: relative; display: flex; align-items: center; justify-content: center; }
        .hero-overlay { background: linear-gradient(45deg, rgba(10, 77, 104, 0.8), rgba(8, 131, 149, 0.6)); position: absolute; top: 0; left: 0; height: 100%; width: 100%; }
        .hero-content { color: var(--light-text-color); text-align: center; position: relative; z-index: 2; }
        .hero-content h2 { font-size: 3.5rem; font-weight: 700; margin-bottom: 15px; text-shadow: 0 2px 4px rgba(0,0,0,0.2); }
        .hero-content p { font-size: 1.3rem; margin-bottom: 30px; }
        .cta-button { background-color: var(--surface-color); color: var(--primary-color); padding: 12px 25px; border-radius: 50px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .cta-button:hover { background-color: #f1f1f1; transform: translateY(-2px); }

        section { padding: 80px 0; }
        .section-title { text-align: center; font-size: 2.5rem; color: var(--primary-color); margin-bottom: 50px; }

        .actus-events { display: flex; gap: 30px; justify-content: center; flex-wrap: wrap; }
        .card { background: var(--surface-color); padding: 30px; border-radius: var(--border-radius); box-shadow: var(--shadow); flex: 1 1 320px; max-width: 450px; transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .card:hover { transform: translateY(-8px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        .card h3 { color: var(--primary-color); margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid var(--secondary-color); padding-bottom: 10px; display: inline-block; }
        .card ul { list-style: none; padding-left: 0; margin: 0; }
        .card ul li { margin-bottom: 15px; }
        .card ul li::before { content: '→'; margin-right: 10px; color: var(--secondary-color); }
        .card a { color: var(--text-color); text-decoration: none; font-weight: 500; transition: color 0.3s ease; }
        .card a:hover { color: var(--secondary-color); }

        .presentation { background: var(--surface-color); }
        .presentation-content { max-width: 800px; margin: 0 auto 40px auto; text-align: center; }
        .mv { display: flex; justify-content: center; flex-wrap: wrap; gap: 30px; }
        .mv > div { flex: 1 1 300px; background: var(--background-color); padding: 30px; border-radius: var(--border-radius); border-left: 4px solid var(--primary-color); }
        .mv h4 { color: var(--primary-color); margin-top: 0; margin-bottom: 10px; font-size: 1.3rem; }

        footer { background: var(--primary-color); color: var(--light-text-color); padding: 60px 0 20px; text-align: center; }
        .footer-grid { display: flex; flex-wrap: wrap; gap: 40px; justify-content: space-around; margin-bottom: 40px; }
        .footer-grid > div { flex: 1 1 200px; }
        footer h4 { font-weight: 600; margin-bottom: 15px; color: #fff; }
        footer ul { list-style: none; padding-left: 0; }
        footer li { margin-bottom: 8px; }
        footer a { color: rgba(255, 255, 255, 0.8); text-decoration: none; transition: color 0.3s ease; }
        footer a:hover { color: #fff; text-decoration: underline; }
        .footer-socials { display: flex; justify-content: center; gap: 15px; }
        .footer-socials a { font-size: 1.2rem; }
        .footer-bottom { padding-top: 30px; border-top: 1px solid rgba(255, 255, 255, 0.2); font-size: 0.9em; color: rgba(255, 255, 255, 0.7); }
    </style>
</head>
<body>

<header>
    <div class="container">
        <h1 class="logo">École Sup.</h1>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="vue/formations.php">Formations</a></li>
                <li><a href="#">Entreprises</a></li>
                <li><a href="vue/supportContact.php">Contact</a></li>
                <?php if (!empty($_SESSION['connexion']) && $_SESSION['connexion'] === true): ?>
                    <li><a href="vue/forum.php">Forum</a></li>
                    <li><a href="?deco=true">Déconnexion</a></li>
                <?php else: ?>
                    <li><a href="vue/connexion.php">Connexion</a></li>
                    <li><a href="vue/inscription.php">Inscription</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<section class="hero">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h2>Construire les mondes de demain</h2>
        <p>Formation. Recherche. Innovation.</p>
        <a href="#" class="cta-button">Découvrir nos formations</a>
    </div>
</section>

<div class="container">
    <section class="actus-events">
        <div class="card actus">
            <h3>Actualités</h3>
            <ul>
                <li><a href="#">Ouverture des candidatures pour la rentrée 2026</a></li>
                <li><a href="#">Signature d'un partenariat stratégique avec TechLabs</a></li>
                <li><a href="#">Lancement du nouveau master en Data Science & IA</a></li>
            </ul>
        </div>
        <div class="card events">
            <h3>Événements à venir</h3>
            <ul>
                <?php foreach($latestEvents as $event): ?>
                    <li><?= htmlspecialchars($event->getTitre()) ?> — <?= date('d/m/Y H:i', strtotime($event->getDateEvent())) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>
</div>

<section class="presentation">
    <div class="container">
        <h2 class="section-title">Notre École</h2>
        <div class="presentation-content">
            <p>Nous formons les ingénieurs, chercheurs et innovateurs de demain, prêts à relever les défis technologiques, sociaux et environnementaux d'un monde en pleine mutation.</p>
        </div>
        <div class="mv">
            <div class="mission">
                <h4>Notre mission</h4>
                <p>Former des experts engagés, dotés d'un esprit critique et d'une forte capacité d'adaptation pour innover de manière responsable.</p>
            </div>
            <div class="vision">
                <h4>Notre vision</h4>
                <p>Être un pôle d'excellence reconnu à l’international, où la recherche et la pédagogie se conjuguent pour construire un avenir durable.</p>
            </div>
            <div class="valeurs">
                <h4>Nos valeurs</h4>
                <p>Innovation, ouverture, solidarité et respect de l'environnement guident chaque projet que nous entreprenons.</p>
            </div>
        </div>
    </div>
</section>

<footer>
    <div class="footer-grid container">
        <div>
            <h4>École Sup.</h4>
            <p>123 Rue de l'Innovation, 75000 Paris</p>
            <p>contact@ecolesup.fr</p>
            <p>+33 1 23 45 67 89</p>
        </div>
        <div>
            <h4>Liens rapides</h4>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="vue/formations.php">Formations</a></li>
                <li><a href="vue/supportContact.php">Contact</a></li>
            </ul>
        </div>
        <div>
            <h4>Réseaux sociaux</h4>
            <div class="footer-socials">
                <a href="#">FB</a>
                <a href="#">TW</a>
                <a href="#">IG</a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; 2025 École Sup. Tous droits réservés.
    </div>
</footer>

</body>
</html>
