<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Forum - École Exemple</title>
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

        /* CONTENU FORUM */
        .forum-container {
            max-width: 1000px;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #005baa;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        th, td {
            padding: 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #eef4fc;
            color: #333;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        a {
            color: #005baa;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .new-topic {
            display: block;
            width: fit-content;
            margin: 20px auto 0;
            padding: 10px 20px;
            background: #005baa;
            color: white;
            border-radius: 4px;
            text-align: center;
            text-decoration: none;
            transition: background 0.3s;
        }

        .new-topic:hover {
            background: #004080;
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
                <li><a href="#">Formations</a></li>
                <li><a href="#">Relations entreprises</a></li>
                <li><a href="evenements.html">Événements</a></li>
                <li><a href="#">Inscription</a></li>
                <li><a href="#">Connexion</a></li>
            </ul>
        </nav>
    </div>
</header>

<!-- CONTENU -->
<div class="forum-container">
    <h2>Discussions du Forum</h2>

    <a href="#" class="new-topic">+ Nouveau sujet</a>

    <table>
        <thead>
        <tr>
            <th>Sujet</th>
            <th>Auteur</th>
            <th>Dernier message</th>
            <th>Réponses</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><a href="#">Présentation des nouveaux étudiants</a></td>
            <td>admin</td>
            <td>22 sept 2025 à 10h45</td>
            <td>8</td>
        </tr>
        <tr>
            <td><a href="#">Questions sur le Master IA</a></td>
            <td>mdupont</td>
            <td>21 sept 2025 à 17h20</td>
            <td>12</td>
        </tr>
        <tr>
            <td><a href="#">Événement : Forum des entreprises</a></td>
            <td>clemence.r</td>
            <td>20 sept 2025 à 08h15</td>
            <td>4</td>
        </tr>
        </tbody>
    </table>
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
