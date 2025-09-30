<?php
require_once __DIR__ . '/../src/repository/offreRepo.php';
use repository\offreRepo;

$repo = new offreRepo();
$rows = $repo->getAllRaw();

function alertBox() {
    if (!isset($_GET['msg'])) return '';
    $cls=''; $txt='';
    switch ($_GET['msg']) {
        case 'added':   $cls='success'; $txt='Offre ajoutée.'; break;
        case 'deleted': $cls='warning'; $txt='Offre supprimée.'; break;
        case 'updated': $cls='info';    $txt='Offre modifiée.'; break;
        case 'error':   $cls='danger';  $txt='Veuillez remplir les champs requis.'; break;
        default: return '';
    }
    return '<div class="alert alert-'.$cls.'">'.$txt.'</div>';
}
$types = ['CDI','CDD','Stage','Alternance','Autre'];
$etats = ['ouvert','ferme','brouillon'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des offres</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{background:#f8f9fa}
        th,td{vertical-align:top}
        .w-pre{white-space:pre-line; word-break:break-word;}
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
    <a href="adminOffre.php" class="active">Offres d'emploi / stage</a>
    <a href="adminEvent.php" class="">Événements</a>
</nav>

<body>
<div class="container my-4">
    <h1 class="mb-3">Gestion des offres</h1>
    <?= alertBox(); ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white"><strong>Liste des offres</strong></div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-secondary">
                <tr>
                    <th style="width:90px">ID</th>
                    <th style="width:220px">Titre</th>
                    <th>Description</th>
                    <th>Mission</th>
                    <th style="width:130px">Salaire</th>
                    <th style="width:160px">Type</th>
                    <th style="width:150px">État</th>
                    <th style="width:220px">Date création</th>
                    <th style="width:170px">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!$rows): ?>
                    <tr><td colspan="9" class="text-center text-muted">Aucune offre</td></tr>
                <?php else: foreach ($rows as $r): ?>
                    <tr>
                        <td><?= (int)$r['id_offre'] ?></td>
                        <td><?= htmlspecialchars($r['titre']) ?></td>
                        <td class="w-pre"><?= htmlspecialchars($r['description']) ?></td>
                        <td class="w-pre"><?= htmlspecialchars($r['mission']) ?></td>
                        <td><?= $r['salaire']!==null && $r['salaire']!=='' ? htmlspecialchars($r['salaire']).' €' : '—' ?></td>
                        <td><?= htmlspecialchars($r['type_offre']) ?></td>
                        <td>
                            <?php
                            $map = ['ouvert'=>'success','ferme'=>'secondary','brouillon'=>'dark'];
                            $color = isset($map[$r['etat']]) ? $map[$r['etat']] : 'secondary';
                            ?>
                            <span class="badge text-bg-<?= $color ?>"><?= htmlspecialchars($r['etat']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($r['date_creation']) ?></td>
                        <td class="text-nowrap">
                            <a class="btn btn-sm btn-warning" href="modifOffre.php?id_offre=<?= (int)$r['id_offre'] ?>">Modifier</a>
                            <a class="btn btn-sm btn-danger"
                               href="../src/traitement/suppOffre.php?id_offre=<?= (int)$r['id_offre'] ?>"
                               onclick="return confirm('Supprimer cette offre ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-success text-white"><strong>Ajouter une offre</strong></div>
        <div class="card-body">
            <form method="post" action="../src/traitement/ajoutOffre.php">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Titre *</label>
                        <input type="text" name="titre" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Type *</label>
                        <select name="type_offre" class="form-select" required>
                            <?php foreach ($types as $t): ?><option value="<?= $t ?>"><?= $t ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description *</label>
                        <textarea name="description" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Mission</label>
                        <textarea name="mission" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Salaire (€/mois)</label>
                        <input type="number" step="0.01" min="0" name="salaire" class="form-control" placeholder="ex: 1800.00">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">État *</label>
                        <select name="etat" class="form-select" required>
                            <?php foreach ($etats as $e): ?><option value="<?= $e ?>"><?= $e ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-success">Ajouter</button>
                    </div>
                </div>
            </form>
            <p class="text-muted mt-2">* requis — <em>date_creation</em> est gérée automatiquement par la BDD.</p>
        </div>
    </div>
</div>
</body>
</html>
