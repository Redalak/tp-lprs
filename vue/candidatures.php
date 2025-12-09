<?php
// Définir le titre de la page
$pageTitle = 'Candidatures reçues';

// Démarrer la session et charger l'auth de base AVANT tout output HTML
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../src/auth/check_auth.php';

// Dépendances
require_once __DIR__ . '/../src/bdd/Bdd.php';
require_once __DIR__ . '/../src/helpers/AuthHelper.php';
require_once __DIR__ . '/../src/repository/UserRepo.php';

use bdd\Bdd;
use helpers\AuthHelper;
use repository\UserRepo;

// Exiger un compte entreprise
if (!isset($_SESSION['connexion']) || $_SESSION['connexion'] !== true) {
    $_SESSION['error'] = 'Veuillez vous connecter.';
    header('Location: ' . ($base_path ?? '/lprs/tp-lprs') . '/vue/connexion.php');
    exit();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'entreprise') {
    $_SESSION['error'] = 'Accès réservé aux entreprises.';
    header('Location: ' . ($base_path ?? '/lprs/tp-lprs') . '/index.php');
    exit();
}

// Récupérer l'entreprise liée à l'utilisateur
$userId = $_SESSION['id_user'] ?? $_SESSION['user_id'] ?? null;
$userRepo = new UserRepo();
$user = $userRepo->getUserById((int)$userId);

$refEntreprise = null;
if ($user && method_exists($user, 'getRefEntreprise')) {
    $refEntreprise = $user->getRefEntreprise();
}

if (empty($refEntreprise)) {
    $_SESSION['error'] = "Aucune entreprise n'est associée à votre compte.";
    header('Location: ' . ($base_path ?? '/lprs/tp-lprs') . '/index.php');
    exit();
}

// Charger les candidatures pour toutes les offres de l'entreprise
$pdo = (new Bdd())->getBdd();
$stmt = $pdo->prepare('
    SELECT c.id_candidature, c.motivation, c.cv, c.date_candidature, c.ref_offre, c.ref_user,
           u.prenom, u.nom, u.email, o.titre
    FROM candidature c
    JOIN offre o ON o.id_offre = c.ref_offre
    JOIN user u  ON u.id_user  = c.ref_user
    WHERE o.ref_entreprise = :idEntreprise
    ORDER BY c.date_candidature DESC
');
$stmt->execute(['idEntreprise' => (int)$refEntreprise]);
$candidatures = $stmt->fetchAll();
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>
<div class="container py-4">
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Candidatures reçues</h1>
        <a class="btn btn-outline-secondary" href="<?= ($base_path ?? '') ?>/vue/entreprise.php">Retour espace entreprise</a>
    </div>

    <?php if (empty($candidatures)): ?>
        <div class="alert alert-info">Aucune candidature reçue pour le moment.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Offre</th>
                        <th>Candidat</th>
                        <th>Email</th>
                        <th>Date</th>
                        <th>CV</th>
                        <th>Motivation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($candidatures as $c): ?>
                        <tr>
                            <td>
                                <a href="<?= ($base_path ?? '') ?>/vue/offres/detail.php?id=<?= (int)$c['ref_offre']; ?>">
                                    <?= htmlspecialchars($c['titre']); ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']); ?></td>
                            <td><a href="mailto:<?= htmlspecialchars($c['email']); ?>"><?= htmlspecialchars($c['email']); ?></a></td>
                            <td><?= (new DateTime($c['date_candidature']))->format('d/m/Y H:i'); ?></td>
                            <td>
                                <a class="btn btn-sm btn-outline-primary" target="_blank" href="<?= ($base_path ?? '') ?>/uploads/cv/<?= htmlspecialchars($c['cv']); ?>">Télécharger</a>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#motivationModal" data-motivation="<?= htmlspecialchars($c['motivation']); ?>">Voir</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Modal motivation -->
<div class="modal fade" id="motivationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Lettre de motivation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="motivationContent"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

<script>
const modalEl = document.getElementById('motivationModal');
modalEl?.addEventListener('show.bs.modal', (e) => {
  const btn = e.relatedTarget;
  const motivation = btn?.getAttribute('data-motivation') || '';
  document.getElementById('motivationContent').innerHTML = motivation.replace(/\n/g,'<br>');
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
