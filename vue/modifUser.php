<?php
require_once __DIR__ . '/../src/repository/userRepo.php';
use repository\userRepo;

$repo = new userRepo();

$id = isset($_GET['id_user']) ? (int)$_GET['id_user'] : 0;
if ($id <= 0) die('ID manquant.');

$u = $repo->getModelById($id);
if (!$u) die('Utilisateur introuvable.');

$roles = ['admin', 'prof', 'eleve', 'alumni', 'entreprise'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un utilisateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-4" style="max-width: 1000px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Modification utilisateur #<?= (int)$u->getIdUser() ?></h1>
        <a href="adminUser.php" class="btn btn-secondary">Retour</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (isset($_GET['err'])): ?>
                <div class="alert alert-danger">Veuillez remplir les champs requis.</div>
            <?php endif; ?>

            <form action="../src/traitement/modifUser.php" method="post">
                <input type="hidden" name="id_user" value="<?= (int)$u->getIdUser() ?>">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Nom *</label>
                        <input type="text" name="nom" class="form-control" required value="<?= htmlspecialchars((string)$u->getNom()) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Prénom *</label>
                        <input type="text" name="prenom" class="form-control" required value="<?= htmlspecialchars((string)$u->getPrenom()) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars((string)$u->getEmail()) ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Mot de passe (laisser vide pour conserver)</label>
                        <input type="password" name="mdp" class="form-control" placeholder="Nouveau mot de passe (optionnel)">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Rôle *</label>
                        <select name="role" class="form-select" required>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= $r ?>" <?= $u->getRole() === $r ? 'selected' : '' ?>><?= $r ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Vérifié</label><br>
                        <input type="checkbox" name="est_verifie" value="1" <?= $u->getEstVerifie() ? 'checked' : '' ?>> Compte vérifié
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Spécialité</label>
                        <input type="text" name="specialite" class="form-control" value="<?= htmlspecialchars((string)$u->getSpecialite()) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Matière</label>
                        <input type="text" name="matiere" class="form-control" value="<?= htmlspecialchars((string)$u->getMatiere()) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Poste</label>
                        <input type="text" name="poste" class="form-control" value="<?= htmlspecialchars((string)$u->getPoste()) ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Année promo</label>
                        <input type="number" name="annee_promo" class="form-control" min="0" value="<?= htmlspecialchars((string)$u->getAnneePromo()) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Promo</label>
                        <input type="text" name="promo" class="form-control" value="<?= htmlspecialchars((string)$u->getPromo()) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">CV (lien/nom de fichier)</label>
                        <input type="text" name="cv" class="form-control" value="<?= htmlspecialchars((string)$u->getCv()) ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Motif partenariat</label>
                        <input type="text" name="motif_partenariat" class="form-control" value="<?= htmlspecialchars((string)$u->getMotifPartenariat()) ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Ref. entreprise</label>
                        <input type="number" name="ref_entreprise" class="form-control" min="0" value="<?= htmlspecialchars((string)($u->getRefEntreprise() ?? '')) ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Ref. formation</label>
                        <input type="number" name="ref_formation" class="form-control" min="0" value="<?= htmlspecialchars((string)($u->getRefFormation() ?? '')) ?>">
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button class="btn btn-primary">Enregistrer</button>
                    <a href="adminUser.php" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </form>
            <p class="text-muted mt-3 mb-0"><em>created_at / updated_at</em> gérés par la base.</p>
        </div>
    </div>
</div>
</body>
</html>
