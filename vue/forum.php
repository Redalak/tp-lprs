<?php
require_once __DIR__ . '/../src/bdd/Bdd.php';
require_once __DIR__ . '/../src/modele/PForum.php';
require_once __DIR__ . '/../src/modele/RForum.php';
require_once __DIR__ . '/../src/repository/PForumRepo.php';
require_once __DIR__ . '/../src/repository/RForumRepo.php';

use repository\PForumRepo;
use repository\RForumRepo;

function s($v){ return htmlspecialchars((string)$v, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); }

$pRepo = new PForumRepo();
$rRepo = new RForumRepo();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = $id ? $pRepo->find($id) : null;
$replies = $post ? $rRepo->forPost($id) : [];
$posts = $id ? [] : $pRepo->all();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Forum – Section générale</title>
    <style>
        body { margin:0; font-family: system-ui, Segoe UI, Roboto, Arial; background:#0b1020; color:#e8ecf5 }
        .container{max-width:900px;margin:0 auto;padding:24px}
        .card{background:#151b2e;padding:16px;border-radius:16px;box-shadow:0 6px 20px rgba(0,0,0,.2);margin-bottom:16px}
        .input,textarea{width:100%;padding:10px;border-radius:10px;border:1px solid #2a3356;background:#0e1430;color:#e8ecf5}
        button{padding:10px 14px;border:none;border-radius:10px;background:#7aa2ff;color:#07122b;font-weight:700;cursor:pointer}
        .small{color:#9aa4bf;font-size:12px}
        .a{color:#fff;text-decoration:none}
        hr{border:0;border-top:1px solid #263056;margin:16px 0}
    </style>
</head>
<body>
<div class="container">
  <?php if(!$post): ?>
    <div class="card">
      <h2 style="margin:0 0 12px;">Forum – Section générale</h2>
      <form method="post" action="../src/traitement/gestionForum.php">
        <input type="hidden" name="action" value="new_post">
        <input class="input" name="author" placeholder="Votre nom" required>
        <input class="input" name="title" placeholder="Titre" required style="margin-top:8px;">
        <textarea class="input" name="content" rows="6" placeholder="Contenu" required style="margin-top:8px;"></textarea>
        <button type="submit" style="margin-top:10px;">Publier</button>
      </form>
    </div>
    <?php foreach($posts as $p): ?>
      <div class="card">
        <h3 style="margin:0"><a class="a" href="?id=<?=s($p->getIdPost())?>"><?=s($p->getTitre())?></a></h3>
        <div class="small">par <?=s($p->getAuthor())?> • <?=date('d/m/Y H:i', strtotime($p->getDateCreation()))?></div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="card">
      <a class="a" href="forum.php">← Retour</a>
      <h2 style="margin:8px 0 0;"><?=s($post->getTitre())?></h2>
      <div class="small">par <?=s($post->getAuthor())?> • <?=date('d/m/Y H:i', strtotime($post->getDateCreation()))?></div>
      <hr>
      <div><?=nl2br(s($post->getContenue()))?></div>
    </div>
    <div class="card">
      <h3 style="margin:0 0 8px;">Réponses (<?=count($replies)?>)</h3>
      <?php foreach($replies as $r): ?>
        <div class="card"><div class="small">par <?=s($r->getAuthor())?> • <?=date('d/m/Y H:i', strtotime($r->getDateCreation()))?></div><div><?=nl2br(s($r->getContenue()))?></div></div>
      <?php endforeach; ?>
      <hr>
      <form method="post" action="../src/traitement/gestionForum.php">
        <input type="hidden" name="action" value="new_reply">
        <input type="hidden" name="post_id" value="<?=s($post->getIdPost())?>">
        <input class="input" name="author" placeholder="Votre nom" required>
        <textarea class="input" name="content" rows="5" placeholder="Votre réponse" required style="margin-top:8px;"></textarea>
        <button type="submit" style="margin-top:10px;">Répondre</button>
      </form>
    </div>
  <?php endif; ?>
</div>
</body>
</html>
