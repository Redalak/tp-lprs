<?php
require_once __DIR__ . '/../src/repository/offreRepo.php';
use repository\offreRepo;

$repo = new offreRepo();

$id = isset($_GET['id_offre']) ? (int)$_GET['id_offre'] : 0;
if ($id<=0) die('ID manquant.');

$o = $repo->getModelById($id);
if (!$o) die('Offre introuvable.');

$types = ['CDI','CDD','Stage','Alternance','Autre'];
$etats = ['ouvert','ferme','brouillon'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier une offre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-4" style="max-width: 1000px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Modification offre #<?= (int)$o->getIdOffre() ?></h1>
        <a href="adminOffre.php" class="btn btn-secondary">Retour</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (isset($_GET['err'])): ?>
                <div class="alert alert-danger">Veuillez remplir les champs requis.</div>
            <?php endif; ?>

            <form action="../src/traitement/modifOffre.php" method="post">
                <input type="hidden" name="id_offre" value="<?= (int)$o->getIdOffre() ?>">

                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Titre *</label>
                        <input type="text" name="titre" class="form-control" required value="<?= htmlspecialchars($o->getTitre()) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Type *</label>
                        <select name="type_offre" class="form-select" required>
                            <?php foreach ($types as $t): ?>
                                <option value="<?= $t ?>" <?= $o->getTypeOffre()===$t?'selected':''; ?>><?= $t ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Description *</label>
                        <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($o->getDescription()) ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Mission</label>
                        <textarea name="mission" class="form-control" rows="4"><?= htmlspecialchars($o->getMission()) ?></textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Salaire (€/mois)</label>
                        <input type="number" step="0.01" min="0" name="salaire" class="form-control" value="<?= htmlspecialchars($o->getSalaire()) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">État *</label>
                        <select name="etat" class="form-select" required>
                            <?php foreach ($etats as $e): ?>
                                <option value="<?= $e ?>" <?= $o->getEtat()===$e?'selected':''; ?>><?= $e ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button class="btn btn-primary">Enregistrer</button>
                    <a href="adminOffre.php" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </form>
            <p class="text-muted mt-3 mb-0"><em>date_creation</em> est gérée automatiquement par la base.</p>
        </div>
    </div>
</div>
</body>
</html>
