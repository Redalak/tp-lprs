<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Formations - École Exemple</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f4f4f4;
        }

        /* HEADER */
        header {
            background: white;
            border-bottom: 1px solid #ddd;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        header .container-header {
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

        /* CONTENU FORMATIONS */
        .formations-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 0 20px;
        }

        h2 {
            text-align: center;
            color: #003a70;
            margin-bottom: 40px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            color: #005baa;
            margin-top: 0;
        }

        .card p {
            flex: 1;
            margin: 15px 0;
            color: #555;
        }

        .card a {
            align-self: flex-start;
            padding: 10px 15px;
            background: #005baa;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            transition: background 0.3s;
        }

        .card a:hover {
            background: #004070;
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
    <div class="container-header">
        <div class="logo">École Exemple</div>
        <nav>
            <ul>
                <li><a href="index.html">Accueil</a></li>
                <li><a href="formations.html">Formations</a></li>
                <li><a href="#">Relations entreprises</a></li>
                <li><a href="evenements.html">Événements</a></li>
                <li><a href="#">Inscription</a></li>
                <li><a href="#">Connexion</a></li>
            </ul>
        </nav>
    </div>
</header>

<!-- CONTENU FORMATIONS -->
<div class="formations-container">
    <h2>Nos Formations</h2>

    <div class="cards">
        <div class="card">
            <h3>Licence Informatique</h3>
            <p>Une base solide pour débuter dans le développement, les algorithmes et les systèmes informatiques.</p>
            <a href="#">Voir détails</a>
        </div>

        <div class="card">
            <h3>Master Intelligence Artificielle</h3>
            <p>Spécialisez-vous en IA, machine learning et vision artificielle avec des projets concrets et encadrés.</p>
            <a href="#">Voir détails</a>
        </div>

        <div class="card">
            <h3>Diplôme d'Ingénieur</h3>
            <p>Un parcours complet en 5 ans avec un fort ancrage scientifique et professionnel.</p>
            <a href="#">Voir détails</a>
        </div>

        <div class="card">
            <h3>Formation continue</h3>
            <p>Modules courts ou longs pour professionnels en reconversion ou montée en compétences.</p>
            <a href="#">Voir détails</a>
        </div>

        <div class="card">
            <h3>Cycle Préparatoire</h3>
            <p>Préparez-vous aux concours et aux formations d’excellence dans un environnement stimulant.</p>
            <a href="#">Voir détails</a>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer>
    <div class="footer-grid">
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
