<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>École</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f8f8f8;
            color: #333;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            padding: 0 20px;
        }

        /* HEADER */
        header {
            background: white;
            border-bottom: 1px solid #ddd;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }

        .logo {
            font-size: 1.5em;
            color: #005baa;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
            padding-left: 0;
        }

        nav ul li a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }

        nav ul li a:hover {
            color: #005baa;
        }

        /* HERO */
        .hero {
            background: url('https://source.unsplash.com/1600x600/?education,university') no-repeat center center/cover;
            height: 400px;
            position: relative;
        }

        .hero-overlay {
            background: rgba(0, 0, 0, 0.4);
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .hero-content {
            color: white;
            text-align: center;
        }

        .hero-content h2 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .hero-content p {
            font-size: 1.2rem;
        }

        /* ACTUALITÉS + ÉVÉNEMENTS */
        .actus-events {
            display: flex;
            gap: 40px;
            justify-content: center;
            margin: 50px auto;
            flex-wrap: wrap;
            text-align: center;
        }

        .actus, .events {
            flex: 1 1 300px;
            background: white;
            padding: 20px;
            border-radius: 5px;
            max-width: 400px;
        }

        .actus h3, .events h3 {
            color: #005baa;
            margin-bottom: 15px;
        }

        .actus ul, .events ul {
            list-style: none;
            padding-left: 0;
        }

        .actus ul li, .events ul li {
            margin-bottom: 10px;
        }

        .actus a, .events a {
            color: #333;
            text-decoration: none;
        }

        .actus a:hover, .events a:hover {
            text-decoration: underline;
        }

        /* PRÉSENTATION */
        .presentation {
            background: #ffffff;
            padding: 50px 20px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .presentation h2 {
            color: #005baa;
            margin-bottom: 20px;
        }

        .presentation p {
            max-width: 800px;
        }

        .mv {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 40px;
            margin-top: 30px;
            max-width: 900px;
        }

        .mv > div {
            flex: 1 1 300px;
            background: #eef4fc;
            padding: 20px;
            border-radius: 5px;
        }

        .mv h4 {
            color: #004a80;
            margin-bottom: 10px;
        }

        /* FOOTER */
        footer {
            background: #003a70;
            color: white;
            padding: 40px 0 20px;
        }

        .footer-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
            text-align: center;
        }

        footer h4 {
            margin-bottom: 10px;
        }

        footer ul {
            list-style: none;
            padding-left: 0;
        }

        footer a {
            color: #ddd;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>

<!-- HEADER -->
<header>
    <div class="container">
        <h1 class="logo">École</h1>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="vue/formations.php">Formations</a></li>
                <li><a href="#">Relations Entreprise</a></li>
                <li><a href="vue/supportContact.php">Contact</a></li>
                <li><a href="vue/forum.php">Forum</a></li>
                <li><a href="vue/connexion.php">Connexion</a></li>
            </ul>
        </nav>
    </div>
</header>

<!-- HERO -->
<section class="hero">
    <div class="hero-overlay">
        <div class="hero-content">
            <h2>Construire les mondes de demain</h2>
            <p>Formation. Recherche. Innovation.</p>
        </div>
    </div>
</section>

<!-- ACTUALITÉS & ÉVÉNEMENTS -->
<section class="actus-events">
    <div class="actus">
        <h3>Actualités</h3>
        <ul>
            <li><a href="#">Ouverture des candidatures 2026</a></li>
            <li><a href="#">Partenariat avec TechLabs signé</a></li>
            <li><a href="#">Nouveau master Data & IA</a></li>
        </ul>
    </div>
    <div class="events">
        <h3>Événements</h3>
        <ul>
            <li><a href="#">Salon de l'Étudiant — 15 Oct</a></li>
            <li><a href="#">Conférence IA & Climat — 30 Oct</a></li>
            <li><a href="#">Forum Entreprises — 10 Nov</a></li>
        </ul>
    </div>
</section>

<!-- PRÉSENTATION -->
<section class="presentation">
    <h2>À propos de l'École</h2>
    <p>L'École Exemple forme les ingénieurs et chercheurs de demain, capables de répondre aux défis technologiques, sociaux et climatiques.</p>
    <div class="mv">
        <div class="mission">
            <h4>Notre mission</h4>
            <p>Former des experts engagés, innovants et ouverts sur le monde.</p>
        </div>
        <div class="vision">
            <h4>Notre vision</h4>
            <p>Être une école d'excellence reconnue pour sa recherche, ses partenariats et son impact sociétal.</p>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="container footer-grid">
        <div>
            <h4>Contact</h4>
            <p>123 Rue de l'École<br>75000 Paris</p>
        </div>
        <div>
            <h4>Liens utiles</h4>
            <ul>
                <li><a href="#">Inscription</a></li>
                <li><a href="#">FAQ</a></li>
                <li><a href="#">Mentions légales</a></li>
            </ul>
        </div>
        <div>
            <h4>Suivez-nous</h4>
            <p>Facebook | LinkedIn | X</p>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; 2025 École Exemple. Tous droits réservés.
    </div>
</footer>

</body>
</html>
