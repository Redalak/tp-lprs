<?php
require_once __DIR__ . '/../src/repository/eventRepo.php';
use repository\eventRepo;

$repo = new eventRepo();
$rows = $repo->getAllRaw();

function alertBox() {
    if (!isset($_GET['msg'])) return '';
    $cls = ''; $txt = '';
    switch ($_GET['msg']) {
        case 'added':   $cls='success'; $txt="Événement ajouté."; break;
        case 'deleted': $cls='warning'; $txt="Événement supprimé."; break;
        case 'updated': $cls='info';    $txt="Événement modifié."; break;
        case 'error':   $cls='danger';  $txt="Veuillez remplir tous les champs requis."; break;
        default: return '';
    }
    return '<div class="alert alert-'.$cls.'">'.$txt.'</div>';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des événements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{background:#f8f9fa}
        th,td{vertical-align:top}
        .w-desc{white-space:pre-line; word-break:break-word;}
    </style>
</head>
<style>
    header {
        text-align: center;
        font-size: 1.8em;
        font-weight: 700;
        color: #0A4D68;
        padding: 20px 0;
        font-family: 'Poppins', sans-serif;
    }

    nav.tabs {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-bottom: 40px;
    }

    nav.tabs a {
        text-decoration: none;
        background: #fff;
        border: 2px solid #0A4D68;
        padding: 10px 25px;
        font-weight: 600;
        color: #0A4D68;
        border-radius: 8px;
        transition: background 0.3s, color 0.3s;
        font-family: 'Poppins', sans-serif;
    }

    nav.tabs a.active {
        background: #0A4D68;
        color: #f8f9fa;
        box-shadow: 0 4px 10px rgba(0, 77, 104, 0.4);
    }

    nav.tabs a:hover:not(.active) {
        background: #088395;
        color: #f8f9fa;
    }
</style>

<header>Admin - École</header>

<nav class="tabs" role="navigation" aria-label="Navigation principale">
    <a href="adminUser.php" class="">Utilisateurs</a>
    <a href="adminOffre.php" class="">Offres d'emploi / stage</a>
    <a href="adminEvent.php" class="active">Événements</a>
</nav>

<body>
<div class="container my-4">
    <h1 class="mb-3">Gestion des événements</h1>
    <?= alertBox(); ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white"><strong>Liste des événements</strong></div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-secondary">
                <tr>
                    <th style="width:90px">ID</th>
                    <th style="width:160px">Type</th>
                    <th style="width:220px">Titre</th>
                    <th>Description</th>
                    <th style="width:220px">Lieu</th>
                    <th style="width:240px">Éléments requis</th>
                    <th style="width:120px">Places</th>
                    <th style="width:150px">État</th>
                    <th style="width:220px">Date création</th>
                    <th style="width:170px">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!$rows): ?>
                    <tr><td colspan="10" class="text-center text-muted">Aucun événement</td></tr>
                <?php else: foreach ($rows as $r): ?>
                    <tr>
                        <td><?= (int)$r['id_evenement'] ?></td>
                        <td><?= htmlspecialchars($r['type']) ?></td>
                        <td><?= htmlspecialchars($r['titre']) ?></td>
                        <td class="w-desc"><?= htmlspecialchars($r['description']) ?></td>
                        <td><?= htmlspecialchars($r['lieu']) ?></td>
                        <td class="w-desc"><?= htmlspecialchars($r['element_requis']) ?></td>
                        <td><?= (int)$r['nombre_place'] ?></td>
                        <td>
                            <?php
                            $map = ['brouillon'=>'secondary','publie'=>'success','archive'=>'dark'];
                            $color = isset($map[$r['etat']]) ? $map[$r['etat']] : 'secondary';
                            ?>
                            <span class="badge text-bg-<?= $color ?>"><?= htmlspecialchars($r['etat']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($r['date_creation']) ?></td>
                        <td class="text-nowrap">
                            <a class="btn btn-sm btn-warning" href="modifEvent.php?id_evenement=<?= (int)$r['id_evenement'] ?>">Modifier</a>
                            <a class="btn btn-sm btn-danger"
                               href="../src/traitement/suppEvent.php?id_evenement=<?= (int)$r['id_evenement'] ?>"
                               onclick="return confirm('Supprimer cet événement ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-success text-white"><strong>Ajouter un événement</strong></div>
        <div class="card-body">
            <form method="post" action="../src/traitement/ajoutEvent.php">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Type *</label>
                        <input type="text" name="type" class="form-control" required placeholder="portes-ouvertes / conférence / ...">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Titre *</label>
                        <input type="text" name="titre" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description *</label>
                        <textarea name="description" class="form-control" rows="5" required></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Lieu *</label>
                        <input type="text" name="lieu" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Éléments requis</label>
                        <textarea name="element_requis" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nombre de places</label>
                        <input type="number" name="nombre_place" class="form-control" min="0" value="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">État *</label>
                        <select name="etat" class="form-select" required>
                            <option value="brouillon">brouillon</option>
                            <option value="publie">publie</option>
                            <option value="archive">archive</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-success">Ajouter</button>
                    </div>
                </div>
            </form>
            <p class="text-muted mt-2">* requis — <em>date_creation</em> est remplie automatiquement par la BDD.</p>
        </div>
    </div>
</div>
</body>
</html>
