<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../src/modele/PForum.php';
require_once __DIR__ . '/../src/modele/RForum.php';
require_once __DIR__ . '/../src/repository/PForumRepo.php';
require_once __DIR__ . '/../src/repository/RForumRepo.php';

$pRepo = new \repository\PForumRepo();
$rRepo = new \repository\RForumRepo();

$userId   = (int)($_SESSION['id_user'] ?? 0);
$userName = trim((string)($_SESSION['prenom'] ?? '') . ' ' . (string)($_SESSION['nom'] ?? ''));

/* Création d’un post */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'post') {
    $titre    = trim((string)($_POST['titre'] ?? ''));
    $contenue = trim((string)($_POST['contenue'] ?? ''));
    if ($userId > 0 && $titre !== '' && $contenue !== '') {
        $pRepo->create($userId, $titre, $contenue);
    }
    header('Location: forum.php'); exit;
}

/* Création d’une réponse */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'reply') {
    $postId   = (int)($_POST['post_id'] ?? 0);
    $contenue = trim((string)($_POST['contenue'] ?? ''));
    if ($userId > 0 && $postId > 0 && $contenue !== '') {
        $rRepo->create($postId, $userId, $contenue);
    }
    header('Location: forum.php#post-' . $postId); exit;
}

/* Liste des posts */
$posts = $pRepo->all();
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forum général</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root{--pri:#0A4D68;--sec:#088395;--bg:#f7f8fa;--panel:#fff;--ink:#222;--mut:#6b7280;--sh:0 6px 24px rgba(0,0,0,.06)}
        *{box-sizing:border-box} body{margin:0;font-family:Poppins,system-ui,Arial,sans-serif;background:var(--bg);color:var(--ink)}
        .wrap{max-width:900px;margin:0 auto;padding:20px}
        h1{margin:10px 0 16px;color:var(--pri)}
        .panel{background:var(--panel);border-radius:12px;box-shadow:var(--sh);padding:16px;margin:14px 0}
        .meta{color:var(--mut);font-size:.9rem;margin-bottom:8px}
        form input, form textarea{width:100%;padding:10px 12px;margin:8px 0;border:1px solid #e5e7eb;border-radius:10px;font:inherit}
        .btn{display:inline-block;background:var(--sec);color:#fff;border:0;border-radius:10px;padding:10px 16px;font-weight:600;cursor:pointer}
        .btn:hover{background:var(--pri)}
        .reply{margin-top:10px;padding-top:10px;border-top:1px dashed #e5e7eb}
        .reply .item{background:#f9fafb;border:1px solid #eef2f7;border-radius:10px;padding:10px 12px;margin:8px 0}
    </style>
</head>
<body>
<div class="wrap">
    <h1>Forum — canal général</h1>

    <?php if ($userId > 0): ?>
        <div class="panel">
            <h3>Nouveau post</h3>
            <form method="post" action="forum.php">
                <input type="hidden" name="action" value="post">
                <input type="text" name="titre" placeholder="Titre" required>
                <textarea name="contenue" rows="5" placeholder="Votre message…" required></textarea>
                <button class="btn" type="submit">Publier</button>
            </form>
        </div>
    <?php else: ?>
        <div class="panel" style="background:#fff8e6;border:1px solid #ffe3a3">
            Connectez-vous pour publier ou répondre.
        </div>
    <?php endif; ?>

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
                ?>
                <div class="reply">
                    <strong>Réponses (<?= count($replies) ?>)</strong>
                    <?php foreach ($replies as $r): ?>
                        <div class="item">
                            <div class="meta">
                                Le <?= htmlspecialchars((string)$r->getDateCreation()) ?>
                                — utilisateur #<?= (int)$r->getRefUser() ?>
                            </div>
                            <div><?= nl2br(htmlspecialchars($r->getContenue())) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($userId > 0): ?>
                <form class="reply" method="post" action="forum.php#post-<?= (int)$p->getIdPost() ?>">
                    <input type="hidden" name="action" value="reply">
                    <input type="hidden" name="post_id" value="<?= (int)$p->getIdPost() ?>">
                    <textarea name="contenue" rows="3" placeholder="Répondre…" required></textarea>
                    <button class="btn" type="submit">Répondre</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

</div>
</body>
</html>