<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Événements - École Exemple</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f4;
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
            padding: 15px 20px;
            max-width: 1100px;
            margin: auto;
        }

        .logo {
            font-size: 1.5em;
            color: #005baa;
            font-weight: bold;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
            margin: 0;
            padding: 0;
        }

        nav ul li a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }

        nav ul li a:hover {
            color: #005baa;
        }

        /* CONTENU */
        .container-content {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        h2 {
            color: #005baa;
            text-align: center;
            margin-bottom: 40px;
        }

        .event {
            background: white;
            border-left: 6px solid #005baa;
            margin-bottom: 30px;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .event h3 {
            margin: 0 0 10px;
        }

        .event .meta {
            color: #666;
            font-size: 0.95em;
            margin-bottom: 10px;
        }

        .event p {
            margin-bottom: 15px;
        }

        .event a {
            display: inline-block;
            padding: 8px 16px;
            background-color: #005baa;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s;
        }

        .event a:hover {
            background-color: #003f66;
        }

        /* FOOTER */
        footer {
            background: #003a70;
            color: white;
            padding: 40px 0 20px;
            margin-top: 60px;
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
        <div class="logo">École Exemple</div>
        <nav>
            <ul>
                <li><a href="index.html">L'École</a></li>
                <li><a href="#">Formations</a></li>
                <li><a href="#">Recherche</a></li>
                <li><a href="#">Campus</a></li>
                <li><a href="#">Relations</a></li>
                <li><a href="#">Connexion</a></li>
            </ul>
        </nav>
    </div>
</header>

<!-- CONTENU -->
<div class="container-content">
    <h2>Événements à venir</h2>

    <div class="event">
        <h3>Journée Portes Ouvertes 2025</h3>
        <div class="meta">📅 12 octobre 2025 | 📍 Campus Paris</div>
        <p>Découvrez nos formations, échangez avec les étudiants et rencontrez nos enseignants lors de cette journée d'immersion exceptionnelle.</p>
        <a href="#">Plus d'infos</a>
    </div>

    <div class="event">
        <h3>Forum Entreprises</h3>
        <div class="meta">📅 5 novembre 2025 | 📍 Amphithéâtre A</div>
        <p>Rencontrez les recruteurs de grandes entreprises partenaires de l’école, participez à des ateliers CV et simulation d'entretien.</p>
        <a href="#">S'inscrire</a>
    </div>

    <div class="event">
        <h3>Conférence : IA & Climat</h3>
        <div class="meta">📅 20 novembre 2025 | 📍 En ligne</div>
        <p>Des experts en intelligence artificielle et climatologie débattent de l’impact de l’IA dans la transition écologique.</p>
        <a href="#">Participer</a>
    </div>
</div>

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
