<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="h4 mb-0">Modifier l'offre</h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($_SESSION['form_errors'])): ?>
                        <div class="alert alert-danger">
                            <h5 class="alert-heading">Veuillez corriger les erreurs suivantes :</h5>
                            <ul class="mb-0">
                                <?php foreach ($_SESSION['form_errors'] as $error): ?>
                                    <li><?= htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php unset($_SESSION['form_errors']); ?>
                    <?php endif; ?>

                    <form action="/offres/<?= $offre['id_offre']; ?>" method="POST">
                        <input type="hidden" name="_method" value="PUT">
                        
                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre de l'offre *</label>
                            <input type="text" class="form-control" id="titre" name="titre" 
                                   value="<?= htmlspecialchars($_SESSION['form_data']['titre'] ?? $offre['titre']); ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="type_offre" class="form-label">Type d'offre *</label>
                                <select class="form-select" id="type_offre" name="type_offre" required>
                                    <option value="">Sélectionnez un type</option>
                                    <option value="CDI" <?= ($_SESSION['form_data']['type_offre'] ?? $offre['type_offre']) === 'CDI' ? 'selected' : ''; ?>>CDI</option>
                                    <option value="CDD" <?= ($_SESSION['form_data']['type_offre'] ?? $offre['type_offre']) === 'CDD' ? 'selected' : ''; ?>>CDD</option>
                                    <option value="Stage" <?= ($_SESSION['form_data']['type_offre'] ?? $offre['type_offre']) === 'Stage' ? 'selected' : ''; ?>>Stage</option>
                                    <option value="Alternance" <?= ($_SESSION['form_data']['type_offre'] ?? $offre['type_offre']) === 'Alternance' ? 'selected' : ''; ?>>Alternance</option>
                                    <option value="Autre" <?= ($_SESSION['form_data']['type_offre'] ?? $offre['type_offre']) === 'Autre' ? 'selected' : ''; ?>>Autre</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description détaillée *</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="6" required><?= htmlspecialchars($_SESSION['form_data']['description'] ?? $offre['description']); ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="rue" class="form-label">Rue</label>
                                <input type="text" class="form-control" id="rue" name="rue" 
                                       value="<?= htmlspecialchars($_SESSION['form_data']['rue'] ?? $offre['rue']); ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="cp" class="form-label">Code postal</label>
                                <input type="text" class="form-control" id="cp" name="cp" 
                                       value="<?= htmlspecialchars($_SESSION['form_data']['cp'] ?? $offre['cp']); ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="ville" class="form-label">Ville</label>
                                <input type="text" class="form-control" id="ville" name="ville" 
                                       value="<?= htmlspecialchars($_SESSION['form_data']['ville'] ?? $offre['ville']); ?>">
                            </div>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="etat" name="etat" 
                                   value="ouvert" <?= ($_SESSION['form_data']['etat'] ?? $offre['etat']) === 'ouvert' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="etat">Offre publiée (visible par les utilisateurs)</label>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/offres/<?= $offre['id_offre']; ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Annuler
                            </a>
                            
                            <div class="btn-group">
                                <a href="/offres/<?= $offre['id_offre']; ?>" class="btn btn-outline-info">
                                    <i class="fas fa-eye me-1"></i> Voir l'offre
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Enregistrer les modifications
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card border-danger mt-4">
                <div class="card-header bg-danger text-white">
                    <h3 class="h5 mb-0">Zone dangereuse</h3>
                </div>
                <div class="card-body">
                    <h4 class="h6 text-danger">Supprimer cette offre</h4>
                    <p class="text-muted">
                        La suppression est définitive. Cette action ne peut pas être annulée.
                    </p>
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash-alt me-1"></i> Supprimer cette offre
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer définitivement cette offre ?
                <div class="alert alert-warning mt-2">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Cette action est irréversible. Toutes les candidatures associées seront également supprimées.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="/offres/<?= $offre['id_offre']; ?>" method="POST" class="d-inline">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i> Supprimer définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
// Nettoyer les données du formulaire après affichage
unset($_SESSION['form_data']); 
?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
