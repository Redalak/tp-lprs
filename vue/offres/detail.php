<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="container mt-4">
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0"><?= htmlspecialchars($offre['titre']); ?></h1>
                <span class="badge bg-<?= $offre['etat'] === 'ouvert' ? 'success' : 'secondary'; ?> fs-6">
                    <?= ucfirst($offre['etat']); ?>
                </span>
            </div>
            
            <div class="text-muted mt-2">
                <i class="fas fa-building me-1"></i> 
                <?= htmlspecialchars($offre['nom_entreprise']); ?>
                
                <span class="ms-3">
                    <i class="far fa-calendar-alt me-1"></i>
                    Publiée le <?= (new DateTime($offre['date_creation']))->format('d/m/Y'); ?>
                </span>
                
                <?php if (!empty($offre['ville'])): ?>
                    <span class="ms-3">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        <?= htmlspecialchars($offre['ville']); ?>
                        <?php if (!empty($offre['cp'])): ?>
                            (<?= htmlspecialchars($offre['cp']); ?>)
                        <?php endif; ?>
                    </span>
                <?php endif; ?>
            </div>
            
            <div class="mt-2">
                <span class="badge bg-primary">
                    <?= htmlspecialchars($offre['type_offre']); ?>
                </span>
            </div>
        </div>
        
        <div class="card-body">
            <h5 class="card-title">Description du poste</h5>
            <div class="card-text mb-4">
                <?= nl2br(htmlspecialchars($offre['description'])); ?>
            </div>
            
            <?php if (!empty($offre['rue'])): ?>
                <h5 class="card-title">Adresse</h5>
                <p class="card-text">
                    <?= htmlspecialchars($offre['rue']); ?><br>
                    <?php if (!empty($offre['cp']) && !empty($offre['ville'])): ?>
                        <?= htmlspecialchars($offre['cp'] . ' ' . $offre['ville']); ?>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
            
            <?php if (AuthHelper::hasRole('eleve') || AuthHelper::hasRole('alumni')): ?>
                <hr>
                <div class="mt-4">
                    <h5 class="mb-3">Postuler à cette offre</h5>
                    <form action="/lprs/tp-lprs/src/traitement/candidatures_creer.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="ref_offre" value="<?= $offre['id_offre']; ?>">
                        
                        <div class="mb-3">
                            <label for="motivation" class="form-label">Lettre de motivation *</label>
                            <textarea class="form-control" id="motivation" name="motivation" rows="5" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="cv" class="form-label">CV (PDF, max 2Mo) *</label>
                            <input type="file" class="form-control" id="cv" name="cv" accept=".pdf" required>
                            <div class="form-text">Format accepté : PDF uniquement, taille maximale : 2 Mo</div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i> Envoyer ma candidature
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="card-footer bg-transparent">
            <div class="d-flex justify-content-between">
                <a href="/offres" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                </a>
                
                <?php if (AuthHelper::hasRole('entreprise') && AuthHelper::isOwnerOfOffer($offre['id_offre'], $pdo)): ?>
                    <div class="btn-group">
                        <a href="/offres/<?= $offre['id_offre']; ?>/modifier" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-1"></i> Modifier
                        </a>
                        
                        <button type="button" 
                                class="btn btn-outline-<?= $offre['etat'] === 'ouvert' ? 'danger' : 'success'; ?> toggle-status"
                                data-offre-id="<?= $offre['id_offre']; ?>"
                                data-current-state="<?= $offre['etat']; ?>">
                            <i class="fas fa-<?= $offre['etat'] === 'ouvert' ? 'times' : 'check'; ?> me-1"></i>
                            <?= $offre['etat'] === 'ouvert' ? 'Fermer' : 'Rouvrir'; ?> l'offre
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php if (AuthHelper::hasRole('entreprise') && AuthHelper::isOwnerOfOffer($offre['id_offre'], $pdo)): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h2 class="h5 mb-0">Candidatures reçues</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($candidatures)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Date de candidature</th>
                                    <th>CV</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($candidatures as $candidature): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($candidature['prenom'] . ' ' . $candidature['nom']); ?></td>
                                        <td>
                                            <a href="mailto:<?= htmlspecialchars($candidature['email']); ?>">
                                                <?= htmlspecialchars($candidature['email']); ?>
                                            </a>
                                        </td>
                                        <td><?= (new DateTime($candidature['date_candidature']))->format('d/m/Y H:i'); ?></td>
                                        <td>
                                            <a href="/uploads/cv/<?= htmlspecialchars($candidature['cv']); ?>" 
                                               target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-download me-1"></i> Télécharger
                                            </a>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-info view-motivation" 
                                                    data-bs-toggle="modal" data-bs-target="#motivationModal"
                                                    data-motivation="<?= htmlspecialchars($candidature['motivation']); ?>">
                                                <i class="fas fa-eye me-1"></i> Voir la motivation
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mb-0">
                        Aucune candidature reçue pour le moment.
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Modal pour afficher la motivation -->
        <div class="modal fade" id="motivationModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Lettre de motivation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body" id="motivationContent">
                        <!-- Le contenu sera chargé dynamiquement -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
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
                        // Recharger la page pour mettre à jour l'interface
                        window.location.reload();
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
    
    // Gestion de l'affichage de la motivation dans la modal
    document.querySelectorAll('.view-motivation').forEach(button => {
        button.addEventListener('click', function() {
            const motivation = this.dataset.motivation;
            document.getElementById('motivationContent').innerHTML = 
                motivation.replace(/\n/g, '<br>');
        });
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
