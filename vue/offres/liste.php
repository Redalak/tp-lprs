<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Liste des offres</h1>
        <?php if (AuthHelper::hasRole('entreprise')): ?>
            <a href="/offres/creer" class="btn btn-primary">
                <i class="fas fa-plus"></i> Créer une offre
            </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="row">
        <?php foreach ($offres as $offre): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="card-title"><?= htmlspecialchars($offre['titre']); ?></h5>
                            <span class="badge bg-<?= $offre['etat'] === 'ouvert' ? 'success' : 'secondary'; ?>">
                                <?= ucfirst($offre['etat']); ?>
                            </span>
                        </div>
                        
                        <h6 class="card-subtitle mb-2 text-muted">
                            <?= htmlspecialchars($offre['nom_entreprise']); ?>
                        </h6>
                        
                        <p class="card-text text-truncate">
                            <?= nl2br(htmlspecialchars($offre['description'])); ?>
                        </p>
                        
                        <div class="mb-2">
                            <span class="badge bg-info">
                                <?= htmlspecialchars($offre['type_offre']); ?>
                            </span>
                        </div>
                        
                        <div class="text-muted small mb-3">
                            <i class="far fa-calendar-alt me-1"></i>
                            <?= (new DateTime($offre['date_creation']))->format('d/m/Y'); ?>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="/offres/<?= $offre['id_offre']; ?>" class="btn btn-sm btn-outline-primary">
                                Voir les détails
                            </a>
                            
                            <?php if (AuthHelper::hasRole('entreprise') && AuthHelper::isOwnerOfOffer($offre['id_offre'], $pdo)): ?>
                                <div class="btn-group">
                                    <a href="/offres/<?= $offre['id_offre']; ?>/modifier" 
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-<?= $offre['etat'] === 'ouvert' ? 'danger' : 'success'; ?> toggle-status"
                                            data-offre-id="<?= $offre['id_offre']; ?>"
                                            data-current-state="<?= $offre['etat']; ?>">
                                        <i class="fas fa-<?= $offre['etat'] === 'ouvert' ? 'times' : 'check'; ?>"></i>
                                        <?= $offre['etat'] === 'ouvert' ? 'Fermer' : 'Rouvrir'; ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        
        <?php if (empty($offres)): ?>
            <div class="col-12">
                <div class="alert alert-info">Aucune offre disponible pour le moment.</div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du changement d'état (ouverture/fermeture) d'une offre
    document.querySelectorAll('.toggle-status').forEach(button => {
        button.addEventListener('click', function() {
            const offreId = this.dataset.offreId;
            const currentState = this.dataset.currentState;
            const newState = currentState === 'ouvert' ? 'ferme' : 'ouvert';
            
            if (confirm(`Voulez-vous vraiment ${newState === 'ferme' ? 'fermer' : 'rouvrir'} cette offre ?`)) {
                fetch(`/api/offres/${offreId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Mise à jour de l'interface utilisateur
                        const badge = this.closest('.card').querySelector('.badge.bg-success, .badge.bg-secondary');
                        if (badge) {
                            badge.textContent = data.libelleEtat;
                            badge.className = `badge bg-${data.nouvelEtat === 'ouvert' ? 'success' : 'secondary'}`;
                        }
                        
                        // Mise à jour du bouton
                        this.dataset.currentState = data.nouvelEtat;
                        this.innerHTML = `<i class="fas fa-${data.nouvelEtat === 'ouvert' ? 'times' : 'check'}"></i> ${data.nouvelEtat === 'ouvert' ? 'Fermer' : 'Rouvrir'}`;
                        this.className = `btn btn-sm btn-outline-${data.nouvelEtat === 'ouvert' ? 'danger' : 'success'} toggle-status`;
                        
                        // Afficher un message de succès
                        alert(`L'offre a été ${data.nouvelEtat === 'ouvert' ? 'rouverte' : 'fermée'} avec succès.`);
                    } else {
                        alert('Une erreur est survenue. Veuillez réessayer.');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue. Veuillez réessayer.');
                });
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
