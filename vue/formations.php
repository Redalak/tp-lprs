<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Formations - École Sup.</title>

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
            --radius:12px;
            --chip:#eef6f8;
        }

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
        /* actif */
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

        /* Grille des formations */
        .grid{
            display:grid;
            grid-template-columns:repeat(3,minmax(0,1fr));
            gap:24px;
        }
        @media (max-width:1000px){ .grid{grid-template-columns:repeat(2,1fr)} }
        @media (max-width:640px){ .grid{grid-template-columns:1fr} }

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
        }
        .card h3{
            margin:0;color:var(--primary-color);font-size:1.1rem;
        }
        .card p{
            margin:0;color:#53626a;
            min-height:56px;
        }
        .meta{
            display:flex;flex-wrap:wrap;gap:10px;margin-top:4px;
        }
        .meta span{
            font-size:.82rem;background:#f3f6f7;border:1px solid #e6ecee;color:#55636b;
            padding:6px 10px;border-radius:8px;
        }
        .actions{margin-top:10px}
        .btn{
            display:inline-block;
            background:var(--primary-color);color:#fff;text-decoration:none;
            padding:10px 14px;border-radius:8px;font-weight:600;
            transition:transform .12s ease, opacity .2s ease, background .2s ease;
        }
        .btn:hover{ background:#06364b; transform:translateY(-1px); opacity:.95; }

        /* Footer */
        footer{
            background:var(--primary-color);color:var(--light-text-color);
            text-align:center;padding:40px 20px;margin-top:70px;
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
                <li><a class="active" href="formations.php">Formations</a></li>
                <li><a href="#">Entreprises</a></li>
                <li><a href="supportContact.php">Contact</a></li>
                <li><a href="forum.php">Forum</a></li>
                <li><a href="connexion.php">Connexion</a></li>
                <li><a href="inscription.php">Inscription</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container page-head">
    <h1>Nos Formations</h1>
    <div class="sub">Des parcours concrets, pensés avec les entreprises.</div>
</div>

<div class="container">
    <div class="grid">
        <!-- Cartes formations -->
        <article class="card">
            <span class="badge">Bac pro</span>
            <h3>TRPM — Technicien de Réalisation de Produits Mécaniques</h3>
            <p>Maîtrisez l’usinage, l’assemblage et les procédés de fabrication industrielle.</p>
            <div class="meta">
                <span>2 ans</span><span>Ateliers</span><span>Stage</span>
            </div>
            <div class="actions"><a href="#" class="btn">Voir détails</a></div>
        </article>

        <article class="card">
            <span class="badge">Bac pro</span>
            <h3>MSPC — Maintenance des Systèmes de Production Connectés</h3>
            <p>Diagnostic, capteurs, GMAO : devenez le pilier de la disponibilité des lignes.</p>
            <div class="meta">
                <span>2 ans</span><span>TP</span><span>Alternance</span>
            </div>
            <div class="actions"><a href="#" class="btn">Voir détails</a></div>
        </article>

        <article class="card">
            <span class="badge">Bac pro</span>
            <h3>CIEL — Conduite et Innovation en Équipement Logistique</h3>
            <p>Planification des flux, automatisation et gestion de stock avancée.</p>
            <div class="meta">
                <span>2 ans</span><span>Logistique</span><span>Stage</span>
            </div>
            <div class="actions"><a href="#" class="btn">Voir détails</a></div>
        </article>

        <article class="card">
            <span class="badge">Bac tech</span>
            <h3>STI2D — Sciences & Technologies de l’Industrie et du DD</h3>
            <p>Innovation, énergies, éco-conception : préparez les métiers de demain.</p>
            <div class="meta">
                <span>2 ans</span><span>Innovation</span><span>Projets</span>
            </div>
            <div class="actions"><a href="#" class="btn">Voir détails</a></div>
        </article>

        <article class="card">
            <span class="badge">BTS</span>
            <h3>CPRP — Conception des Processus de Réalisation de Produits</h3>
            <p>Industrialisation, CAO/FAO et optimisation des process de production.</p>
            <div class="meta">
                <span>2 ans</span><span>CAO/FAO</span><span>Alternance</span>
            </div>
            <div class="actions"><a href="#" class="btn">Voir détails</a></div>
        </article>

        <article class="card">
            <span class="badge">BTS</span>
            <h3>MSPC — Maintenance des Systèmes de Production Connectés</h3>
            <p>Automates, IoT industriel, supervision : montez en expertise maintenance.</p>
            <div class="meta">
                <span>2 ans</span><span>Automates</span><span>TP</span>
            </div>
            <div class="actions"><a href="#" class="btn">Voir détails</a></div>
        </article>

        <article class="card">
            <span class="badge">BTS</span>
            <h3>SIO — Services Informatiques aux Organisations</h3>
            <p>Dév. (SLAM) ou Réseaux (SISR) : projets réels, sécurité et bonnes pratiques.</p>
            <div class="meta">
                <span>2 ans</span><span>SLAM/SISR</span><span>Projets</span>
            </div>
            <div class="actions"><a href="#" class="btn">Voir détails</a></div>
        </article>
    </div>
</div>

<footer>
    &copy; 2025 École Supérieure — Tous droits réservés
</footer>

</body>
</html>