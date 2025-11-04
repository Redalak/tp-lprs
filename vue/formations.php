<?php
session_start();
if (!empty($_GET['deco']) && $_GET['deco'] === 'true') {
    session_destroy();
    header("Location: ../index.php");
    exit;
}
$isLoggedIn = !empty($_SESSION['id_user']);
// Détection admin et chargement des formations
require_once __DIR__ . '/../src/repository/UserRepo.php';
require_once __DIR__ . '/../src/repository/FormationRepo.php';
require_once __DIR__ . '/../src/modele/Formation.php';

use repository\UserRepo;
use repository\FormationRepo;
use modele\formation;

$isAdmin = false;
$user = null;
$prenom = $_SESSION['prenom'] ?? '';
$nom    = $_SESSION['nom'] ?? '';
if ($isLoggedIn) {
    $uRepo = new UserRepo();
    $user = $uRepo->getUserById((int)$_SESSION['id_user']);
    if ($user && method_exists($user, 'getRole')) {
        $isAdmin = strtolower((string)$user->getRole()) === 'admin';
    }
    if ($user) {
        if (method_exists($user, 'getPrenom')) { $prenom = $user->getPrenom(); }
        if (method_exists($user, 'getNom'))    { $nom    = $user->getNom(); }
    }
}

$fRepo = new FormationRepo();

// Gestion des actions admin (CRUD)
if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $nom = trim((string)($_POST['nom_formation'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        if ($nom !== '') {
            $f = new formation(['nomformation' => $nom, 'description' => $description]);
            $fRepo->ajoutFormation($f);
        }
        header('Location: formations.php'); exit;
    }
    if ($action === 'update') {
        $id  = (int)($_POST['id_formation'] ?? 0);
        $nom = trim((string)($_POST['nom_formation'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        if ($id > 0 && $nom !== '') {
            $f = new formation(['idformation' => $id, 'nomformation' => $nom, 'description' => $description]);
            $fRepo->modifFormation($f);
        }
        header('Location: formations.php'); exit;
    }
    if ($action === 'delete') {
        $id = (int)($_POST['id_formation'] ?? 0);
        if ($id > 0) { $fRepo->suppFormation($id); }
        header('Location: formations.php'); exit;
    }
}

$formations = $fRepo->listeFormation();
?>
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
        /* Dropdown profil (aligné avec index) */
        .profile-dropdown{position:relative;display:inline-block}
        .profile-icon{font-size:1.5rem;cursor:pointer;padding:5px}
        .profile-icon::after{display:none!important}
        .dropdown-content{display:none;position:absolute;background:var(--surface-color);min-width:220px;box-shadow:var(--shadow);border-radius:12px;padding:20px;right:0;top:100%;z-index:1001;text-align:center}
        .profile-dropdown:hover .dropdown-content{display:block}
        .dropdown-content a{display:block;padding:10px 15px;margin-bottom:8px;border-radius:5px;text-decoration:none;font-weight:500;color:#fff!important}
        .dropdown-content a::after{display:none}
        .profile-button{background:var(--secondary-color)}
        .profile-button:hover{background:var(--primary-color)}
        .logout-button{background:#e74c3c}
        .logout-button:hover{background:#c0392b}
    </style>
</head>
<body>

<header>
    <div class="container">
        <a href="../index.php" class="logo" style="text-decoration:none">École Sup.</a>
        <nav>
            <ul>
                <li><a href="../index.php">Accueil</a></li>
                <li><a class="active" href="formations.php">Formations</a></li>
                <li><a href="entreprise.php">Entreprises</a></li>
                <li><a href="evenement.php">Evenements</a></li>
                <li><a href="supportContact.php">Contact</a></li>
                <?php if ($isLoggedIn): ?>
                    <li><a href="forum.php">Forum</a></li>
                    <li class="profile-dropdown">
                        <a href="profilUser.php" class="profile-icon">👤</a>
                        <div class="dropdown-content">
                            <span>Bonjour, <?= htmlspecialchars((string)$prenom) ?> <?= htmlspecialchars((string)$nom) ?> !</span>
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

<div class="container page-head">
    <h1>Nos Formations</h1>
    <div class="sub"> Actuellement 450 élèves, guidés par une équipe exigeante dont l’objectif est non
        seulement de donner une formation professionnelle et une formation générale, mais aussi
        une formation humaine fondée sur la ponctualité, l’assiduité, la rigueur, le respect de soi et
        des autres ainsi que le sens de l’effort.</div>
</div>

<div class="container">
    <div class="grid">
        <?php if (!empty($formations)): ?>
            <?php foreach ($formations as $f): ?>
                <article class="card">
                    <span class="badge">Formation</span>
                    <h3><?= htmlspecialchars($f->getNomformation()) ?></h3>
                    <p><?= htmlspecialchars((string)($f->getDescription() ?? 'Programme disponible auprès de l\'administration.')) ?></p>
                    <?php if ($isAdmin): ?>
                        <div class="actions" style="display:flex; gap:8px; flex-wrap:wrap">
                            <form method="post" action="formations.php" style="display:flex; gap:8px; align-items:center">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="id_formation" value="<?= (int)$f->getIdformation() ?>">
                                <input type="text" name="nom_formation" value="<?= htmlspecialchars($f->getNomformation()) ?>" placeholder="Nom" required>
                                <input type="text" name="description" value="<?= htmlspecialchars((string)($f->getDescription() ?? '')) ?>" placeholder="Description">
                                <button class="btn" type="submit">Modifier</button>
                            </form>
                            <form method="post" action="formations.php" onsubmit="return confirm('Supprimer cette formation ?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id_formation" value="<?= (int)$f->getIdformation() ?>">
                                <button class="btn" type="submit" style="background:#b31d1d">Supprimer</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="actions"><a href="#" class="btn">Voir détails</a></div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucune formation disponible pour le moment.</p>
        <?php endif; ?>
    </div>

    <?php if ($isAdmin): ?>
        <div style="margin-top:28px; padding:16px; background:#fff; border:1px solid #e9eef0; border-radius:12px; box-shadow:var(--shadow)">
            <h3 style="margin-top:0; color:var(--primary-color)">Ajouter une formation</h3>
            <form method="post" action="formations.php" style="display:flex; gap:12px; flex-wrap:wrap">
                <input type="hidden" name="action" value="create">
                <input type="text" name="nom_formation" placeholder="Nom de la formation" required>
                <input type="text" name="description" placeholder="Description (optionnelle)">
                <button class="btn" type="submit">Ajouter</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<footer>
    &copy; 2025 École Supérieure — Tous droits réservés
</footer>

</body>
</html>