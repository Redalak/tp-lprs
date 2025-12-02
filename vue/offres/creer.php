<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2 class="h4 mb-0">Créer une nouvelle offre</h2>
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

                    <form action="/src/traitement/ajoutOffre.php" method="POST" onsubmit="return validerFormulaire()">
                    <script>
                    function validerFormulaire() {
                        const titre = document.getElementById('titre').value.trim();
                        const description = document.getElementById('description').value.trim();
                        const typeOffre = document.getElementById('type_offre').value;
                        const rue = document.getElementById('rue').value.trim();
                        const cp = document.getElementById('cp').value.trim();
                        const ville = document.getElementById('ville').value.trim();
                        
                        if (!titre || !description || !typeOffre || !rue || !cp || !ville) {
                            alert('Veuillez remplir tous les champs obligatoires.');
                            return false;
                        }
                        
                        // Validation du code postal (5 chiffres)
                        if (!/^\d{5}$/.test(cp)) {
                            alert('Le code postal doit contenir exactement 5 chiffres.');
                            return false;
                        }
                        
                        return true;
                    }
                    </script>
                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre de l'offre *</label>
                            <input type="text" class="form-control" id="titre" name="titre" 
                                   value="<?= htmlspecialchars($_SESSION['form_data']['titre'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description détaillée *</label>
                            <textarea class="form-control" id="description" name="description" 
                                    rows="6" required><?= htmlspecialchars($_SESSION['form_data']['description'] ?? ''); ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="type_offre" class="form-label">Type d'offre *</label>
                                <select class="form-select" id="type_offre" name="type_offre" required>
                                    <option value="">Sélectionnez un type</option>
                                    <option value="CDI" <?= ($_SESSION['form_data']['type_offre'] ?? '') === 'CDI' ? 'selected' : ''; ?>>CDI</option>
                                    <option value="CDD" <?= ($_SESSION['form_data']['type_offre'] ?? '') === 'CDD' ? 'selected' : ''; ?>>CDD</option>
                                    <option value="Stage" <?= ($_SESSION['form_data']['type_offre'] ?? '') === 'Stage' ? 'selected' : ''; ?>>Stage</option>
                                    <option value="Alternance" <?= ($_SESSION['form_data']['type_offre'] ?? '') === 'Alternance' ? 'selected' : ''; ?>>Alternance</option>
                                    <option value="Autre" <?= ($_SESSION['form_data']['type_offre'] ?? '') === 'Autre' ? 'selected' : ''; ?>>Autre</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="etat" class="form-label">État de l'offre *</label>
                                <select class="form-select" id="etat" name="etat" required>
                                    <option value="ouvert" <?= ($_SESSION['form_data']['etat'] ?? '') === 'ouvert' ? 'selected' : ''; ?>>Ouverte</option>
                                    <option value="ferme" <?= ($_SESSION['form_data']['etat'] ?? '') === 'ferme' ? 'selected' : ''; ?>>Fermée</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="rue" class="form-label">Rue</label>
                                <input type="text" class="form-control" id="rue" name="rue" 
                                       value="<?= htmlspecialchars($_SESSION['form_data']['rue'] ?? ''); ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="cp" class="form-label">Code postal</label>
                                <input type="text" class="form-control" id="cp" name="cp" 
                                       value="<?= htmlspecialchars($_SESSION['form_data']['cp'] ?? ''); ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="ville" class="form-label">Ville</label>
                                <input type="text" class="form-control" id="ville" name="ville" 
                                       value="<?= htmlspecialchars($_SESSION['form_data']['ville'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="/offres" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Publier l'offre
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// Nettoyer les données du formulaire après affichage
unset($_SESSION['form_data']); 
?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
