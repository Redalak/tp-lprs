<?php
declare(strict_types=1);
session_start();

// Déconnexion (aligné sur les pages publiques)
if (!empty($_GET['deco']) && $_GET['deco'] === 'true') {
    session_destroy();
    header("Location: ../index.php");
    exit;
}

require_once __DIR__ . '/../src/modele/PForum.php';
require_once __DIR__ . '/../src/repository/PForumRepo.php';
require_once __DIR__ . '/../src/repository/RForumRepo.php';
require_once __DIR__ . '/../src/repository/UserRepo.php';
use repository\UserRepo;

$pRepo = new \repository\PForumRepo();
$rRepo = new \repository\RForumRepo();

// Récupérer le rôle de l'utilisateur
$role = $_SESSION['role'] ?? 'etudiant';

// Afficher le rôle pour débogage
echo "<!-- Rôle détecté : " . htmlspecialchars($role) . " -->\n";
echo "<!-- Session : " . print_r($_SESSION, true) . " -->";

// Debug - à supprimer après
$debug_info = [
    'session' => $_SESSION,
    'role' => $role,
    'canViewAlumni' => $pRepo->canViewCanal($role, 'alumni_entreprises'),
    'canViewEtudiants' => $pRepo->canViewCanal($role, 'etudiants_professeurs')
];
// Fin du debug

// Récupérer le canal actif depuis l'URL, par défaut 'general'
$canalActif = isset($_GET['canal']) ? $_GET['canal'] : 'general';

// Vérifier que le canal est valide
$canauxValides = ['general', 'alumni_entreprises', 'etudiants_professeurs'];
if (!in_array($canalActif, $canauxValides)) {
    $canalActif = 'general';
}

// Vérifier si l'utilisateur a le droit de voir ce canal
if (!$pRepo->canViewCanal($role, $canalActif)) {
    // Rediriger vers un canal autorisé
    $canalActif = 'general';
}

// Vérifier si l'utilisateur peut poster dans ce canal
$peutPoster = $pRepo->canPostInCanal($role, $canalActif);

$userId = (int)($_SESSION['id_user'] ?? 0);

// Récupérer prénom/nom pour le header dropdown
$prenom = $_SESSION['prenom'] ?? '';
$nom    = $_SESSION['nom'] ?? '';
if ($userId > 0) {
    try {
        $uRepo = new UserRepo();
        $u = $uRepo->getUserById($userId);
        if ($u && method_exists($u, 'getPrenom')) { $prenom = $u->getPrenom(); }
        if ($u && method_exists($u, 'getNom'))    { $nom    = $u->getNom(); }
    } catch (\Throwable $e) {}
}
$userName = trim((string)$prenom . ' ' . (string)$nom);
if ($userName === '' || $userName === ' ') {
    $userName = (string)($_SESSION['email'] ?? 'Mon compte');
}

/* Création d'un post */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'post') {
    $titre    = trim((string)($_POST['titre'] ?? ''));
    $contenue = trim((string)($_POST['contenue'] ?? ''));
    if ($userId > 0 && $titre !== '' && $contenue !== '') {
        $canal = isset($_POST['canal']) ? $_POST['canal'] : 'general';
        if (!in_array($canal, $canauxValides)) {
            $canal = 'general';
        }
        $pRepo->create($userId, $titre, $contenue, $canal);
    }
    header('Location: forum_v2.php?canal=' . $canalActif);
    exit;
}

/* Création d'une réponse */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'reply') {
    $postId   = (int)($_POST['post_id'] ?? 0);
    $contenue = trim((string)($_POST['contenue'] ?? ''));
    $parentId = isset($_POST['parent_id']) && $_POST['parent_id'] !== '' ? (int)$_POST['parent_id'] : null;
    if ($userId > 0 && $postId > 0 && $contenue !== '') {
        $rRepo->create($postId, $userId, $contenue, $parentId);
    }
    header('Location: forum_v2.php?canal=' . $canalActif . '#post-' . $postId);
    exit;
}

// Récupérer les posts du canal actif
$posts = $pRepo->findByCanal($canalActif);
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forum - <?= ucfirst(str_replace('_', ' ', $canalActif)) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{--pri:#0A4D68;--sec:#088395;--bg:#f7f8fa;--panel:#fff;--ink:#222;--mut:#6b7280;--sh:0 6px 24px rgba(0,0,0,.06)}
        *{box-sizing:border-box}
        body{margin:0;font-family:'Poppins',system-ui,Arial,sans-serif;background:var(--bg);color:var(--ink)}
        .container{max-width:1200px;margin:auto;padding:0 20px}
        header{background:#fff;box-shadow:var(--sh);position:sticky;top:0;z-index:1000}
        header .container{display:flex;justify-content:space-between;align-items:center;height:70px}
        .logo{font-size:1.6rem;font-weight:700;color:var(--pri);margin:0; text-decoration:none}
        nav ul{list-style:none;display:flex;align-items:center;gap:30px;margin:0;padding:0}
        nav a{text-decoration:none;color:var(--ink);font-weight:500;position:relative;padding-bottom:5px;transition:color .3s ease}
        nav a::after{content:'';position:absolute;left:0;bottom:0;height:2px;width:0;background:var(--sec);transition:width .3s ease}
        nav a:hover{color:var(--pri)}
        nav a:hover::after{width:100%}
        nav a.active{color:var(--pri)}
        nav a.active::after{width:100%}
        .wrap{max-width:900px;margin:0 auto;padding:20px}
        h1{margin:10px 0 16px;color:var(--pri)}
        .panel{background:var(--panel);border-radius:12px;box-shadow:var(--sh);padding:16px;margin:14px 0}
        .meta{color:var(--mut);font-size:.9rem;margin-bottom:8px}
        form input, form textarea{width:100%;padding:10px 12px;margin:8px 0;border:1px solid #e5e7eb;border-radius:10px;font:inherit}
        .btn{display:inline-block;background:var(--sec);color:#fff;border:0;border-radius:10px;padding:10px 16px;font-weight:600;cursor:pointer}
        .btn:hover{background:var(--pri)}
        .reply{margin-top:10px;padding-top:10px;border-top:1px dashed #e5e7eb}
        .reply .item{background:#f9fafb;border:1px solid #eef2f7;border-radius:10px;padding:10px 12px;margin:8px 0}
        /* Dropdown profil */
        .profile-dropdown{position:relative;display:inline-block}
        .profile-icon{font-size:1.5rem;cursor:pointer;padding:5px}
        .profile-icon::after{display:none!important}
        .dropdown-content{display:none;position:absolute;background:#fff;min-width:220px;box-shadow:var(--sh);border-radius:12px;padding:20px;right:0;top:100%;z-index:1001;text-align:center}
        .profile-dropdown:hover .dropdown-content{display:block}
        .dropdown-content a{display:block;padding:10px 15px;margin-bottom:8px;border-radius:5px;text-decoration:none;font-weight:500;color:#fff!important}
        .dropdown-content a::after{display:none}
        .profile-button{background:var(--sec)}
        .profile-button:hover{background:var(--pri)}
        .logout-button{background:#e74c3c}
        .logout-button:hover{background:#c0392b}
        /* Navigation entre les canaux */
        .canaux-navigation {
            display: flex;
            gap: 10px;
            margin: 20px 0 30px;
            flex-wrap: wrap;
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: var(--sh);
        }
        .btn-canal {
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 2px solid #e0e0e0;
            font-size: 0.95rem;
        }
        .btn-canal:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
<!-- Début du débogage - À supprimer après -->
<div style="background: #f8f9fa; padding: 15px; margin: 10px; border: 1px solid #ddd; border-radius: 5px;">
    <h3>Informations de débogage :</h3>
    <pre><?php 
    echo 'Rôle détecté : ' . htmlspecialchars($role) . "\n";
    echo 'Peut voir Alumni & Entreprises : ' . ($pRepo->canViewCanal($role, 'alumni_entreprises') ? 'Oui' : 'Non') . "\n";
    echo 'Peut voir Étudiants & Professeurs : ' . ($pRepo->canViewCanal($role, 'etudiants_professeurs') ? 'Oui' : 'Non') . "\n";
    echo 'Session : ' . print_r($_SESSION, true);
    ?></pre>
</div>
<!-- Fin du débogage -->

<header>
    <div class="container">
        <a href="../index.php" class="logo">École Sup.</a>
        <nav>
            <ul>
                <li><a href="../index.php">Accueil</a></li>
                <li><a href="formations.php">Formations</a></li>
                <li><a href="entreprise.php">Entreprises</a></li>
                <li><a href="evenement.php">Evenements</a></li>
                <li><a href="supportContact.php">Contact</a></li>
                <?php if ($userId > 0): ?>
                    <li><a class="active" href="forum_v2.php">Forum</a></li>
                    <li class="profile-dropdown">
                        <a href="profilUser.php" class="profile-icon">👤</a>
                        <div class="dropdown-content">
                            <span>Bonjour, <?= htmlspecialchars((string)$userName) ?> !</span>
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

<div class="wrap">
    <h1>Forum — <?= ucfirst(str_replace('_', ' ', $canalActif)) ?></h1>
    
    <!-- Navigation entre les canaux -->
    <div class="canaux-navigation">
        <a href="?canal=general" class="btn-canal" style="background: <?= $canalActif === 'general' ? 'var(--sec)' : '#f0f0f0' ?>; color: <?= $canalActif === 'general' ? '#fff' : '#333' ?>">
            Général
        </a>
        <?php if ($pRepo->canViewCanal($role, 'alumni_entreprises')): ?>
            <a href="?canal=alumni_entreprises" class="btn-canal" style="background: <?= $canalActif === 'alumni_entreprises' ? 'var(--sec)' : '#f0f0f0' ?>; color: <?= $canalActif === 'alumni_entreprises' ? '#fff' : '#333' ?>">
                Alumni & Entreprises
            </a>
        <?php endif; ?>
        <?php if ($pRepo->canViewCanal($role, 'etudiants_professeurs')): ?>
            <a href="?canal=etudiants_professeurs" class="btn-canal" style="background: <?= $canalActif === 'etudiants_professeurs' ? 'var(--sec)' : '#f0f0f0' ?>; color: <?= $canalActif === 'etudiants_professeurs' ? '#fff' : '#333' ?>">
                Étudiants & Professeurs
            </a>
        <?php endif; ?>
    </div>

    <?php if ($userId > 0 && $peutPoster): ?>
        <div class="panel">
            <h3>Nouveau post</h3>
            <form method="post" action="forum_v2.php?canal=<?= $canalActif ?>">
                <input type="hidden" name="action" value="post">
                <input type="hidden" name="canal" value="<?= $canalActif ?>">
                <input type="text" name="titre" placeholder="Titre" required>
                <textarea name="contenue" rows="5" placeholder="Votre message…" required></textarea>
                <button class="btn" type="submit">Publier</button>
            </form>
        </div>
    <?php elseif ($userId > 0): ?>
        <div class="panel" style="background-color: #fff3cd; color: #856404; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
            <p>Vous n'avez pas les droits pour créer un nouveau post dans ce canal.</p>
        </div>
    <?php else: ?>
        <div class="panel" style="text-align: center; padding: 20px;">
            <p>Veuillez vous <a href="connexion.php" style="color: var(--sec); font-weight: 500;">connecter</a> pour participer à la discussion.</p>
        </div>
    <?php endif; ?>

    <?php if (empty($posts)): ?>
        <div class="panel" style="text-align: center; padding: 20px;">
            <p>Aucun message dans ce canal pour le moment.</p>
        </div>
    <?php else: ?>
        <?php foreach ($posts as $p): ?>
            <div id="post-<?= (int)$p->getIdPost() ?>" class="panel">
                <div class="meta">
                    Posté le <?= htmlspecialchars((string)$p->getDateCreation()) ?>
                    — utilisateur #<?= (int)$p->getRefUser() ?>
                </div>
                <h3 style="margin:0 0 6px"><?= htmlspecialchars($p->getTitre()) ?></h3>
                <p style="margin:0 0 10px"><?= nl2br(htmlspecialchars($p->getContenue())) ?></p>

                <?php
                $replies = $rRepo->forPost((int)$p->getIdPost());
                if ($replies):
                    // Regrouper par parent (null => 0)
                    $byParent = [];
                    foreach ($replies as $r) {
                        $pid = $r->getParentId() ?? 0;
                        $byParent[$pid][] = $r;
                    }

                    $top = $byParent[0] ?? [];
                    ?>
                    <div class="reply">
                        <strong>Réponses (<?= count($replies) ?>)</strong>
                        <?php foreach ($top as $r): ?>
                            <div class="item">
                                <div class="meta">
                                    Le <?= htmlspecialchars((string)$r->getDateCreation()) ?>
                                    — utilisateur #<?= (int)$r->getRefUser() ?>
                                </div>
                                <div><?= nl2br(htmlspecialchars($r->getContenue())) ?></div>
                                <?php if ($userId > 0): ?>
                                    <form class="reply" method="post" action="forum_v2.php?canal=<?= $canalActif ?>#post-<?= (int)$p->getIdPost() ?>">
                                        <input type="hidden" name="action" value="reply">
                                        <input type="hidden" name="post_id" value="<?= (int)$p->getIdPost() ?>">
                                        <input type="hidden" name="parent_id" value="<?= (int)$r->getIdReply() ?>">
                                        <textarea name="contenue" rows="2" placeholder="Répondre à cette réponse…" required></textarea>
                                        <button class="btn" type="submit">Répondre</button>
                                    </form>
                                <?php endif; ?>

                                <?php if (!empty($byParent[(int)$r->getIdReply()] ?? [])): ?>
                                    <div class="reply" style="margin-left:14px">
                                        <?php foreach ($byParent[(int)$r->getIdReply()] as $c): ?>
                                            <div class="item">
                                                <div class="meta">
                                                    Le <?= htmlspecialchars((string)$c->getDateCreation()) ?>
                                                    — utilisateur #<?= (int)$c->getRefUser() ?>
                                                </div>
                                                <div><?= nl2br(htmlspecialchars($c->getContenue())) ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($userId > 0): ?>
                    <form class="reply" method="post" action="forum_v2.php?canal=<?= $canalActif ?>#post-<?= (int)$p->getIdPost() ?>">
                        <input type="hidden" name="action" value="reply">
                        <input type="hidden" name="post_id" value="<?= (int)$p->getIdPost() ?>">
                        <textarea name="contenue" rows="3" placeholder="Répondre…" required></textarea>
                        <button class="btn" type="submit">Répondre</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
