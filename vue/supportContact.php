<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Contact - École Sup.</title>

    <!-- Police comme l’index -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root{
            --primary-color:#0A4D68;     /* Bleu profond */
            --secondary-color:#088395;   /* Turquoise */
            --background-color:#f8f9fa;
            --surface-color:#ffffff;
            --text-color:#343a40;
            --light-text-color:#f8f9fa;
            --shadow:0 4px 15px rgba(0,0,0,.07);
            --radius:8px;
        }

        /* Base */
        *{box-sizing:border-box}
        body{
            margin:0;
            font-family:'Poppins',sans-serif;
            background:var(--background-color);
            color:var(--text-color);
            line-height:1.7;
        }
        .container{max-width:1200px;margin:auto;padding:0 20px}

        /* Header identique index */
        header{
            background:var(--surface-color);
            box-shadow:var(--shadow);
            position:sticky;top:0;z-index:1000;
        }
        header .container{
            display:flex;justify-content:space-between;align-items:center;height:70px;
        }
        .logo{font-size:1.6rem;font-weight:700;color:var(--primary-color);margin:0}
        nav ul{
            list-style:none;display:flex;gap:30px;margin:0;padding:0;
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
        /* lien actif (Contact) */
        nav a.active{color:var(--primary-color)}
        nav a.active::after{width:100%}

        /* Contenu */
        main{max-width:800px;margin:60px auto;padding:0 20px}
        h1{text-align:center;color:var(--primary-color);margin:0 0 30px}

        form{
            background:var(--surface-color);padding:30px;border-radius:var(--radius);
            box-shadow:var(--shadow);
        }
        .row{display:grid;grid-template-columns:1fr 1fr;gap:20px}
        @media (max-width:700px){.row{grid-template-columns:1fr}}

        .form-group{margin-bottom:20px}
        label{display:block;margin:0 0 8px;font-weight:600}
        input[type="text"],input[type="email"],select,textarea{
            width:100%;padding:12px;border:1px solid #ccc;border-radius:6px;
            font:inherit;background:#fff;
        }
        textarea{min-height:150px;resize:vertical}

        .actions{display:flex;justify-content:flex-end;margin-top:10px}
        button{
            background:var(--primary-color);color:#fff;border:0;border-radius:6px;
            padding:12px 20px;cursor:pointer;font-weight:600;transition:transform .15s ease,opacity .15s ease,background .3s ease;
        }
        button:hover{background:#06364b;transform:translateY(-1px);opacity:.95}

        /* Footer identique style */
        footer{
            background:var(--primary-color);color:var(--light-text-color);
            text-align:center;padding:40px 20px;margin-top:80px;
        }
    </style>
</head>
<body>

<header>
    <div class="container">
        <h1 class="logo">École Sup.</h1>
        <nav>
            <ul>
                <li><a href="../index.php">Accueil</a></li>
                <li><a href="formations.php">Formations</a></li>
                <li><a href="#">Entreprises</a></li>
                <li><a class="active" href="supportContact.php">Contact</a></li>
                <li><a href="forum.php">Forum</a></li>
                <li><a href="connexion.php">Connexion</a></li>
                <li><a href="inscription.php">Inscription</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <h1>Contactez-nous</h1>

    <form action="https://formsubmit.co/974540d9055dcfa92fbb57af58813f4b" method="POST">
        <!-- Config FormSubmit -->
        <input type="hidden" name="_captcha" value="false">
        <input type="hidden" name="_next" value="https://votresite.fr/merci.html">
        <input type="text" name="_honey" style="display:none"> <!-- anti-bot -->

        <div class="row">
            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input id="prenom" name="prenom" type="text" required>
            </div>
            <div class="form-group">
                <label for="nom">Nom</label>
                <input id="nom" name="nom" type="text" required>
            </div>
        </div>

        <div class="form-group">
            <label for="email">Adresse email</label>
            <input id="email" name="email" type="email" required>
        </div>

        <div class="form-group">
            <label for="sujet">Sujet</label>
            <select id="sujet" name="sujet" required>
                <option value="">-- Choisissez un sujet --</option>
                <option value="admission">Demande d’admission</option>
                <option value="formation">Informations sur les formations</option>
                <option value="entreprise">Partenariat entreprise</option>
                <option value="autre">Autre</option>
            </select>
        </div>

        <div class="form-group">
            <label for="message">Votre message</label>
            <textarea id="message" name="message" required></textarea>
        </div>

        <div class="actions">
            <button type="submit">Envoyer</button>
        </div>
    </form>
</main>

<footer>
    &copy; 2025 École Supérieure — Tous droits réservés
</footer>

</body>
</html>