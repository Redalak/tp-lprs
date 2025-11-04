<?php
// FICHIER: vue/adminFormation.php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../src/repository/UserRepo.php';
require_once __DIR__ . '/../src/repository/FormationRepo.php';
require_once __DIR__ . '/../src/modele/Formation.php';

use repository\UserRepo;
use repository\FormationRepo;
use modele\formation;

// --- Récup utilisateur connecté
$userLoggedIn = null;
if (!empty($_SESSION['connexion']) && $_SESSION['connexion'] === true && !empty($_SESSION['id_user'])) {
    $userRepo     = new UserRepo();
    $userLoggedIn = $userRepo->getUserById((int)$_SESSION['id_user']);
}

// --- Garde: accès réservé admin
$isAdmin = false;
if ($userLoggedIn && method_exists($userLoggedIn, 'getRole')) {
    $isAdmin = strtolower((string)$userLoggedIn->getRole()) === 'admin';
}
if (!$userLoggedIn || !$isAdmin) {
    header('Location: ../index.php?forbidden=1');
    exit;
}

$fRepo = new FormationRepo();

// --- Actions CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $nom = trim((string)($_POST['nom_formation'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        if ($nom !== '') {
            $f = new formation(['nomformation' => $nom, 'description' => $description]);
            $fRepo->ajoutFormation($f);
            $_SESSION['flash_msg'] = "La formation a été ajoutée.";
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_msg'] = "Le nom de la formation est obligatoire.";
            $_SESSION['flash_type'] = 'danger';
        }
        header('Location: adminFormation.php'); exit;
    }
    if ($action === 'update') {
        $id  = (int)($_POST['id_formation'] ?? 0);
        $nom = trim((string)($_POST['nom_formation'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        if ($id > 0 && $nom !== '') {
            $f = new formation(['idformation' => $id, 'nomformation' => $nom, 'description' => $description]);
            if ($fRepo->modifFormation($f)) {
                $_SESSION['flash_msg'] = "La formation #$id a été mise à jour.";
                $_SESSION['flash_type'] = 'success';
            } else {
                $_SESSION['flash_msg'] = "Échec de la mise à jour.";
                $_SESSION['flash_type'] = 'danger';
            }
        } else {
            $_SESSION['flash_msg'] = "Champs invalides pour la mise à jour.";
            $_SESSION['flash_type'] = 'danger';
        }
        header('Location: adminFormation.php'); exit;
    }
    if ($action === 'delete') {
        $id = (int)($_POST['id_formation'] ?? 0);
        if ($id > 0) {
            if ($fRepo->suppFormation($id)) {
                $_SESSION['flash_msg'] = "La formation #$id a été supprimée.";
                $_SESSION['flash_type'] = 'success';
            } else {
                $_SESSION['flash_msg'] = "Échec de la suppression.";
                $_SESSION['flash_type'] = 'danger';
            }
        } else {
            $_SESSION['flash_msg'] = "Identifiant de formation manquant.";
            $_SESSION['flash_type'] = 'danger';
        }
        header('Location: adminFormation.php'); exit;
    }
}

$formations = $fRepo->listeFormation();
$prenom   = method_exists($userLoggedIn,'getPrenom') ? $userLoggedIn->getPrenom() : ($_SESSION['prenom'] ?? 'Admin');
$nom      = method_exists($userLoggedIn,'getNom')    ? $userLoggedIn->getNom()    : ($_SESSION['nom'] ?? '');
$nowLabel = date('d/m/Y H:i');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Administration | Formations</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --primary-color:#0A4D68;
            --secondary-color:#088395;
            --background-color:#f8f9fa;
            --surface-color:#ffffff;
            --text-color:#343a40;
            --light-text-color:#f8f9fa;
            --shadow:0 4px 15px rgba(0,0,0,.07);
            --border-radius:8px;
            --danger:#e74c3c;
        }
        *{box-sizing:border-box}
        body{margin:0;font-family:'Poppins',sans-serif;background:var(--background-color);color:var(--text-color);line-height:1.7}
        .container{max-width:1200px;margin:auto;padding:0 20px}

        /* Header identique admin.php */
        header{background:var(--surface-color);box-shadow:var(--shadow);position:sticky;top:0;z-index:1000}
        header .container{display:flex;justify-content:space-between;align-items:center;height:70px}
        .logo{font-size:1.6rem;font-weight:700;color:var(--primary-color)}
        nav ul{list-style:none;display:flex;align-items:center;gap:30px;padding-left:0;margin:0}
        nav ul li a{position:relative;text-decoration:none;color:var(--text-color);font-weight:500;padding-bottom:5px;transition:color .3s}
        nav ul li a::after{content:'';position:absolute;width:0;height:2px;left:0;bottom:0;background:var(--secondary-color);transition:width .3s}
        nav ul li a:hover{color:var(--primary-color)}
        nav ul li a:hover::after{width:100%}
        nav ul li a.active{color:var(--primary-color)}
        nav ul li a.active::after{width:100%}

        .section-title{text-align:center;font-size:1.8rem;color:var(--primary-color);margin:24px 0}
        .card{background:var(--surface-color);border-radius:var(--border-radius);box-shadow:var(--shadow);padding:0;border:1px solid #e9eef0}
        .card-header{padding:14px 18px;border-bottom:1px solid #eef2f4;font-weight:600}
        .card-body{padding:18px}
        .btn{display:inline-block;background:var(--secondary-color);color:#fff;text-decoration:none;padding:10px 14px;border-radius:6px;font-weight:600;border:none;cursor:pointer}
        .btn:hover{background:var(--primary-color)}
        .btn-outline-primary{background:#fff;color:var(--secondary-color);border:1px solid var(--secondary-color)}
        .btn-outline-primary:hover{background:var(--secondary-color);color:#fff}
        .btn-outline-danger{background:#fff;color:var(--danger);border:1px solid var(--danger)}
        .btn-outline-danger:hover{background:var(--danger);color:#fff}
        .alert{padding:12px 14px;border-radius:6px;margin:16px 0}
        .alert-success{background:#eafaf1;color:#2e7d32;border:1px solid #27ae60}
        .alert-danger{background:#fcebea;color:#a94442;border:1px solid #f5c6cb}
        input[type=text]{height:40px;border:1px solid #e1e6ea;border-radius:6px;padding:0 12px}
        table{width:100%;border-collapse:collapse}
        th,td{padding:12px;border-bottom:1px solid #eef2f4}
        thead th{background:#fafcfd;color:#335; font-weight:600}
        /* Dropdown profil (aligné avec index) */
        .profile-dropdown{position:relative;display:inline-block}
        .profile-icon{font-size:1.5rem;cursor:pointer;padding:5px}
        .profile-icon::after{display:none!important}
        .dropdown-content{display:none;position:absolute;background:var(--surface-color);min-width:220px;box-shadow:var(--shadow);border-radius:8px;padding:20px;right:0;top:100%;z-index:1001;text-align:center}
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
        <h1 class="logo">École Sup.</h1>
        <nav>
            <ul>
                <li><a href="../index.php">Accueil</a></li>
                <li><a href="adminEntreprise.php">Entreprise</a></li>
                <li><a href="adminEvent.php">Evenement</a></li>
                <li><a class="active" href="adminFormation.php">Formations</a></li>
                <li><a href="adminOffre.php">Offre</a></li>
                <li><a href="adminUser.php">Utilisateur</a></li>
                <li class="profile-dropdown" style="margin-left:auto">
                    <a href="profilUser.php" class="profile-icon">👤</a>
                    <div class="dropdown-content">
                        <span>Bonjour, <?= htmlspecialchars((string)$prenom) ?> !</span>
                        <a href="profilUser.php" class="profile-button">Mon Profil</a>
                        <a href="../index.php?deco=true" class="logout-button">Déconnexion</a>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
    </header>

<main class="container py-4">
    <?php if (!empty($_SESSION['flash_msg'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_SESSION['flash_type'] ?? 'info') ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['flash_msg']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
        <?php unset($_SESSION['flash_msg'], $_SESSION['flash_type']); ?>
    <?php endif; ?>
    <div class="d-flex justify-content-between align-items-end mb-3">
        <div>
            <h1 class="h3 text-primary mb-1">Gestion des formations</h1>
            <div class="text-muted small">Bonjour <?= htmlspecialchars((string)$prenom) ?> <?= htmlspecialchars((string)$nom) ?> — <?= $nowLabel ?></div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-white fw-semibold">Ajouter une formation</div>
        <div class="card-body">
            <form class="row g-2" method="post" action="adminFormation.php">
                <input type="hidden" name="action" value="create">
                <div class="col-sm-6 col-md-4">
                    <input type="text" class="form-control" name="nom_formation" placeholder="Nom de la formation" required>
                </div>
                <div class="col-sm-12 col-md-6">
                    <input type="text" class="form-control" name="description" placeholder="Description (optionnelle)">
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary" type="submit">Ajouter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white fw-semibold">Liste des formations</div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:120px">ID</th>
                        <th>Nom</th>
                        <th style="width:240px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($formations)): ?>
                    <?php foreach ($formations as $f): ?>
                        <tr>
                            <td>#<?= (int)$f->getIdformation() ?></td>
                            <td>
                                <form class="d-flex gap-2" method="post" action="adminFormation.php">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="id_formation" value="<?= (int)$f->getIdformation() ?>">
                                    <input type="text" class="form-control" name="nom_formation" value="<?= htmlspecialchars($f->getNomformation()) ?>" required>
                                    <input type="text" class="form-control" name="description" value="<?= htmlspecialchars((string)($f->getDescription() ?? '')) ?>" placeholder="Description">
                                    <button class="btn btn-outline-primary" type="submit">Modifier</button>
                                </form>
                            </td>
                            <td>
                                <form method="post" action="adminFormation.php" onsubmit="return confirm('Supprimer cette formation ?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id_formation" value="<?= (int)$f->getIdformation() ?>">
                                    <button class="btn btn-outline-danger" type="submit">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3" class="text-muted">Aucune formation pour le moment.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<footer class="text-center text-white py-4 mt-5" style="background:#0A4D68">
    <div>&copy; <?= date('Y') ?> École Supérieure — Tous droits réservés</div>
</footer>

</body>
</html>
