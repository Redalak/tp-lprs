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
require_once __DIR__ . '/../src/modele/RForum.php';
require_once __DIR__ . '/../src/repository/PForumRepo.php';
require_once __DIR__ . '/../src/repository/RForumRepo.php';
require_once __DIR__ . '/../src/repository/UserRepo.php';
use repository\UserRepo;

$pRepo = new \repository\PForumRepo();
$rRepo = new \repository\RForumRepo();
$userRepo = new UserRepo();

// Fonction pour obtenir le nom complet d'un utilisateur
function getUserFullName($userId, $userRepo) {
    try {
        $user = $userRepo->getUserById($userId);
        if ($user && method_exists($user, 'getPrenom') && method_exists($user, 'getNom')) {
            return trim($user->getPrenom() . ' ' . $user->getNom());
        }
    } catch (\Throwable $e) {}
    return 'Utilisateur #' . $userId;
}

// Récupérer le rôle de l'utilisateur
$role = '';
$userId = (int)($_SESSION['id_user'] ?? 0);

// Récupérer les informations de l'utilisateur
$prenom = $_SESSION['prenom'] ?? '';
$nom = $_SESSION['nom'] ?? '';

if ($userId > 0) {
    try {
        $uRepo = new UserRepo();
        $u = $uRepo->getUserById($userId);
        if ($u) {
            if (method_exists($u, 'getPrenom')) { $prenom = $u->getPrenom(); }
            if (method_exists($u, 'getNom'))    { $nom = $u->getNom(); }
            if (method_exists($u, 'getRole'))   { $role = strtolower((string)$u->getRole()); }
        }
    } catch (\Throwable $e) {}
}

// Si le rôle n'est pas défini, on utilise une valeur par défaut
if (empty($role)) {
    $role = 'etudiant';
}

$userName = trim((string)$prenom . ' ' . (string)$nom);
if ($userName === '' || $userName === ' ') {
    $userName = (string)($_SESSION['email'] ?? 'Mon compte');
}

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
    header('Location: forum.php?canal=' . $canalActif);
    exit;
}

/* Mise à jour d'un post */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_post') {
    $postId = (int)($_POST['post_id'] ?? 0);
    $titre = trim((string)($_POST['titre'] ?? ''));
    $contenue = trim((string)($_POST['contenue'] ?? ''));
    
    if ($postId > 0 && $titre !== '' && $contenue !== '') {
        $pRepo->update($postId, $userId, $titre, $contenue);
    }
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

/* Suppression d'un post */
if (isset($_GET['delete_post'])) {
    $postId = (int)$_GET['delete_post'];
    if ($postId > 0) {
        $pRepo->delete($postId, $userId);
    }
    header('Location: forum.php?canal=' . $canalActif);
    exit;
}

/* Mise à jour d'une réponse */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_reply') {
    $replyId = (int)($_POST['reply_id'] ?? 0);
    $contenu = trim((string)($_POST['contenu'] ?? ''));
    
    if ($replyId > 0 && $contenu !== '') {
        $rRepo->update($replyId, $userId, $contenu);
    }
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

/* Suppression d'une réponse */
if (isset($_GET['delete_reply'])) {
    $replyId = (int)$_GET['delete_reply'];
    if ($replyId > 0) {
        $rRepo->delete($replyId, $userId);
    }
    header('Location: ' . $_SERVER['HTTP_REFERER']);
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
    header('Location: forum_v3.php?canal=' . $canalActif . '#post-' . $postId);
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
<header>
    <div class="container">
        <a href="../index.php" class="logo">École Sup.</a>
        <nav>
            <ul>
                <li><a href="../index.php">Accueil</a></li>
                <li><a href="formations.php">Formations</a></li>
                <li><a href="entreprise.php">Entreprises</a></li>
                <li><a href="offres.php">Offres</a></li>
                <li><a href="evenement.php">Evenements</a></li>
                <li><a href="supportContact.php">Contact</a></li>
                <?php if ($userId > 0): ?>
                    <li><a class="active" href="forum.php">Forum</a></li>
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
            <form method="post" action="forum_v3.php?canal=<?= $canalActif ?>">
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
                    — <?= htmlspecialchars(getUserFullName((int)$p->getRefUser(), $userRepo)) ?>
                    <?php if ($userId === (int)$p->getRefUser() || $role === 'admin'): ?>
                        <div class="post-actions" style="display: inline-block; margin-left: 10px;">
                            <a href="#" onclick="event.preventDefault(); document.getElementById('edit-post-<?= $p->getIdPost() ?>').style.display='block'" style="color: var(--sec); text-decoration: none; margin-right: 10px;">
                                <small>Modifier</small>
                            </a>
                            <a href="?delete_post=<?= $p->getIdPost() ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce post ?')" style="color: #e74c3c; text-decoration: none;">
                                <small>Supprimer</small>
                            </a>
                        </div>
                        
                        <!-- Formulaire de modification (caché par défaut) -->
                        <div id="edit-post-<?= $p->getIdPost() ?>" style="display: none; margin-top: 10px; background: #f8f9fa; padding: 10px; border-radius: 5px;">
                            <form method="post" action="forum.php?canal=<?= $canalActif ?>">
                                <input type="hidden" name="action" value="update_post">
                                <input type="hidden" name="post_id" value="<?= $p->getIdPost() ?>">
                                <input type="text" name="titre" value="<?= htmlspecialchars($p->getTitre()) ?>" style="width: 100%; margin-bottom: 5px;" required>
                                <textarea name="contenue" rows="3" style="width: 100%; margin-bottom: 5px;" required><?= htmlspecialchars($p->getContenue()) ?></textarea>
                                <button type="submit" class="btn" style="padding: 5px 10px; font-size: 0.9em;">Enregistrer</button>
                                <button type="button" class="btn" onclick="document.getElementById('edit-post-<?= $p->getIdPost() ?>').style.display='none'" style="padding: 5px 10px; font-size: 0.9em; background: #6c757d;">Annuler</button>
                            </form>
                        </div>
                    <?php endif; ?>
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
                                    — <?= htmlspecialchars(getUserFullName((int)$r->getRefUser(), $userRepo)) ?>
                                    <?php if ($userId === (int)$r->getRefUser() || $role === 'admin'): ?>
                                        <div class="reply-actions" style="display: inline-block; margin-left: 5px;">
                                            <a href="#" onclick="event.preventDefault(); document.getElementById('edit-reply-<?= $r->getIdReply() ?>').style.display='block'" style="color: var(--sec); text-decoration: none; margin-right: 5px; font-size: 0.9em;">
                                                <small>Modifier</small>
                                            </a>
                                            <a href="?delete_reply=<?= $r->getIdReply() ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réponse ?')" style="color: #e74c3c; text-decoration: none; font-size: 0.9em;">
                                                <small>Supprimer</small>
                                            </a>
                                        </div>
                                        
                                        <!-- Formulaire de modification de réponse (caché par défaut) -->
                                        <div id="edit-reply-<?= $r->getIdReply() ?>" style="display: none; margin-top: 5px; background: #f1f3f5; padding: 8px; border-radius: 5px;">
                                            <form method="post" action="forum.php?canal=<?= $canalActif ?>">
                                                <input type="hidden" name="action" value="update_reply">
                                                <input type="hidden" name="reply_id" value="<?= $r->getIdReply() ?>">
                                                <textarea name="contenu" rows="2" style="width: 100%; margin-bottom: 5px;" required><?= htmlspecialchars($r->getContenue()) ?></textarea>
                                                <button type="submit" class="btn" style="padding: 4px 8px; font-size: 0.8em;">Enregistrer</button>
                                                <button type="button" class="btn" onclick="document.getElementById('edit-reply-<?= $r->getIdReply() ?>').style.display='none'" style="padding: 4px 8px; font-size: 0.8em; background: #6c757d;">Annuler</button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div><?= nl2br(htmlspecialchars($r->getContenue())) ?></div>
                                <?php if ($userId > 0): ?>
                                    <form class="reply" method="post" action="forum_v3.php?canal=<?= $canalActif ?>#post-<?= (int)$p->getIdPost() ?>">
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
                                                — <?= htmlspecialchars(getUserFullName((int)$c->getRefUser(), $userRepo)) ?>
                                                <?php if ($userId === (int)$c->getRefUser() || $role === 'admin'): ?>
                                                    <div class="reply-actions" style="display: inline-block; margin-left: 5px;">
                                                        <a href="#" onclick="event.preventDefault(); document.getElementById('edit-reply-<?= $c->getIdReply() ?>').style.display='block'" style="color: var(--sec); text-decoration: none; margin-right: 5px; font-size: 0.8em;">
                                                            <small>Modifier</small>
                                                        </a>
                                                        <a href="?delete_reply=<?= $c->getIdReply() ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réponse ?')" style="color: #e74c3c; text-decoration: none; font-size: 0.8em;">
                                                            <small>Supprimer</small>
                                                        </a>
                                                    </div>
                                                    
                                                    <!-- Formulaire de modification de réponse imbriquée (caché par défaut) -->
                                                    <div id="edit-reply-<?= $c->getIdReply() ?>" style="display: none; margin-top: 5px; background: #f1f3f5; padding: 8px; border-radius: 5px;">
                                                        <form method="post" action="forum.php?canal=<?= $canalActif ?>">
                                                            <input type="hidden" name="action" value="update_reply">
                                                            <input type="hidden" name="reply_id" value="<?= $c->getIdReply() ?>">
                                                            <textarea name="contenu" rows="2" style="width: 100%; margin-bottom: 5px; font-size: 0.9em;" required><?= htmlspecialchars($c->getContenue()) ?></textarea>
                                                            <button type="submit" class="btn" style="padding: 3px 6px; font-size: 0.8em;">Enregistrer</button>
                                                            <button type="button" class="btn" onclick="document.getElementById('edit-reply-<?= $c->getIdReply() ?>').style.display='none'" style="padding: 3px 6px; font-size: 0.8em; background: #6c757d;">Annuler</button>
                                                        </form>
                                                    </div>
                                                <?php endif; ?>
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
                    <form class="reply" method="post" action="forum_v3.php?canal=<?= $canalActif ?>#post-<?= (int)$p->getIdPost() ?>">
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
