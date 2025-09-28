<?php
require_once __DIR__ . '/../src/repository/eventRepo.php';

use repository\eventRepo;

$repo = new eventRepo();

$id = isset($_GET['id_evenement']) ? (int)$_GET['id_evenement'] : 0;
if ($id<=0) die('ID manquant.');

$event = $repo->getModelById($id);
if (!$event) die('Événement introuvable.');

$ETATS = ['brouillon','publie','archive'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un événement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-4" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Modification évènement #<?= (int)$event->getIdEvent() ?></h1>
        <a href="adminEvent.php" class="btn btn-secondary">Retour</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (isset($_GET['err'])): ?>
                <div class="alert alert-danger">Veuillez remplir les champs requis.</div>
            <?php endif; ?>

            <form action="../src/traitement/modifEvent.php" method="post">
                <input type="hidden" name="id_evenement" value="<?= (int)$event->getIdEvent() ?>">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Type *</label>
                        <input type="text" name="type" class="form-control" required value="<?= htmlspecialchars($event->getType()) ?>">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Titre *</label>
                        <input type="text" name="titre" class="form-control" required value="<?= htmlspecialchars($event->getTitre()) ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description *</label>
                        <textarea name="description" class="form-control" rows="6" required><?= htmlspecialchars($event->getDescription()) ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Lieu *</label>
                        <input type="text" name="lieu" class="form-control" required value="<?= htmlspecialchars($event->getLieu()) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Éléments requis</label>
                        <textarea name="element_requis" class="form-control" rows="2"><?= htmlspecialchars($event->getElementRequis()) ?></textarea>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nombre de places</label>
                        <input type="number" name="nombre_place" class="form-control" min="0" value="<?= (int)$event->getNombrePlace() ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">État *</label>
                        <select name="etat" class="form-select" required>
                            <?php foreach ($ETATS as $e): ?>
                                <option value="<?= $e ?>" <?= $event->getEtat()===$e?'selected':''; ?>><?= $e ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button class="btn btn-primary">Enregistrer</button>
                    <a href="adminEvent.php" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </form>
            <p class="text-muted mt-3 mb-0"><em>date_creation</em> est gérée automatiquement par la base.</p>
        </div>
    </div>
</div>
</body>
</html>
