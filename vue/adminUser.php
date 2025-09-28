<?php
require_once __DIR__ . '/../src/repository/userRepo.php';
use repository\userRepo;

$repo = new userRepo();
$rows = $repo->getAllRaw();

function alertBox() {
    if (!isset($_GET['msg'])) return '';
    $cls=''; $txt='';
    switch ($_GET['msg']) {
        case 'added':   $cls='success'; $txt='Utilisateur ajouté.'; break;
        case 'deleted': $cls='warning'; $txt='Utilisateur supprimé.'; break;
        case 'updated': $cls='info';    $txt='Utilisateur modifié.'; break;
        case 'error':   $cls='danger';  $txt='Veuillez remplir les champs requis.'; break;
        default: return '';
    }
    return '<div class="alert alert-'.$cls.'">'.$txt.'</div>';
}
$roles = ['admin','prof','eleve','alumni','entreprise'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des utilisateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body{background:#f8f9fa} th,td{vertical-align:top}</style>
</head>
<body>
<div class="container my-4">
    <h1 class="mb-3">Gestion des utilisateurs</h1>
    <?= alertBox(); ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white"><strong>Liste des utilisateurs</strong></div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-secondary">
                <tr>
                    <th style="width:80px">ID</th>
                    <th style="width:160px">Nom</th>
                    <th style="width:160px">Prénom</th>
                    <th style="width:240px">Email</th>
                    <th style="width:140px">Rôle</th>
                    <th style="width:110px">Vérifié</th>
                    <th style="width:190px">Créé le</th>
                    <th style="width:170px">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!$rows): ?>
                    <tr><td colspan="8" class="text-center text-muted">Aucun utilisateur</td></tr>
                <?php else: foreach ($rows as $r): ?>
                    <tr>
                        <td><?= (int)$r['id_user'] ?></td>
                        <td><?= htmlspecialchars($r['nom']) ?></td>
                        <td><?= htmlspecialchars($r['prenom']) ?></td>
                        <td><?= htmlspecialchars($r['email']) ?></td>
                        <td><span class="badge text-bg-secondary"><?= htmlspecialchars($r['role']) ?></span></td>
                        <td><?= ((int)$r['est_verifie']===1)?'<span class="badge text-bg-success">oui</span>':'<span class="badge text-bg-secondary">non</span>' ?></td>
                        <td><?= htmlspecialchars($r['created_at']) ?></td>
                        <td class="text-nowrap">
                            <a class="btn btn-sm btn-warning" href="modifUser.php?id_user=<?= (int)$r['id_user'] ?>">Modifier</a>
                            <a class="btn btn-sm btn-danger"
                               href="../src/traitement/suppUser.php?id_user=<?= (int)$r['id_user'] ?>"
                               onclick="return confirm('Supprimer cet utilisateur ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-success text-white"><strong>Ajouter un utilisateur</strong></div>
        <div class="card-body">
            <form method="post" action="../src/traitement/ajoutUser.php">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Nom *</label>
                        <input type="text" name="nom" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Prénom *</label>
                        <input type="text" name="prenom" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Mot de passe *</label>
                        <input type="password" name="mdp" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Rôle *</label>
                        <select name="role" class="form-select" required>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= $r ?>"><?= $r ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Vérifié</label><br>
                        <input type="checkbox" name="est_verifie" value="1"> Compte vérifié
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Spécialité</label>
                        <input type="text" name="specialite" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Matière</label>
                        <input type="text" name="matiere" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Poste</label>
                        <input type="text" name="poste" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Année promo</label>
                        <input type="number" name="annee_promo" class="form-control" min="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Promo</label>
                        <input type="text" name="promo" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">CV (lien/nom de fichier)</label>
                        <input type="text" name="cv" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Motif partenariat</label>
                        <input type="text" name="motif_partenariat" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Ref. entreprise</label>
                        <input type="number" name="ref_entreprise" class="form-control" min="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Ref. formation</label>
                        <input type="number" name="ref_formation" class="form-control" min="0">
                    </div>

                    <div class="col-12">
                        <button class="btn btn-success">Ajouter</button>
                    </div>
                </div>
            </form>
            <p class="text-muted mt-2">* requis — <em>created_at/updated_at</em> gérés par la BDD.</p>
        </div>
    </div>
</div>
</body>
</html>
