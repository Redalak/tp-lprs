<?php
// Définir le titre de la page
$pageTitle = 'Postuler';

// Inclure l'en-tête qui gère la session et l'authentification
require_once __DIR__ . '/../includes/header.php';

$base_path = '/lprs/tp-lprs';
$offreId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($offreId <= 0) {
    $_SESSION['error'] = "Offre inconnue.";
    header('Location: ' . $base_path . '/vue/offres.php');
    exit();
}
?>

<style>
  .apply-wrapper { max-width: 820px; margin: 32px auto; padding: 0 16px; }
  .apply-card { background: #fff; border: 1px solid #e9eef0; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,.06); }
  .apply-card .card-header { padding: 20px 22px; border-bottom: 1px solid #eef2f5; }
  .apply-card .card-header h1 { margin: 0; font-size: 1.35rem; color: #0A4D68; }
  .apply-card .card-body { padding: 22px; }
  .apply-card .card-footer { padding: 18px 22px; border-top: 1px solid #eef2f5; background: #fafcfe; }
  .form-label { display:block; font-weight:600; margin-bottom:8px; color:#243b53; }
  .form-control { width:100%; padding:12px 14px; border:1px solid #d6dee5; border-radius:10px; font-size:1rem; }
  textarea.form-control { min-height: 140px; resize: vertical; }
  .btn { display:inline-block; padding:10px 16px; border-radius:10px; text-decoration:none; font-weight:700; cursor:pointer; border:0; }
  .btn-primary { background:#0A4D68; color:#fff; }
  .btn-primary:hover { background:#088395; }
  .btn-outline { background:#fff; color:#0A4D68; border:1px solid #cfd8df; }
  .btn-outline:hover { background:#f7fafc; }
  .alert { padding:12px 14px; border-radius:10px; margin-bottom:16px; }
  .alert-success { background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; }
  .alert-danger { background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }
</style>

<div class="apply-wrapper">
    <div class="apply-card">
        <div class="card-header">
            <h1>Postuler à cette offre</h1>
        </div>
        <div class="card-body">
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= nl2br(htmlspecialchars($_SESSION['error'])); ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

            <form action="<?= $base_path ?>/src/traitement/candidatures_creer.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="ref_offre" value="<?= (int)$offreId ?>">

                <div class="mb-3">
                    <label for="motivation" class="form-label">Lettre de motivation *</label>
                    <textarea class="form-control" id="motivation" name="motivation" rows="6" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="cv" class="form-label">CV (PDF, max 2 Mo) *</label>
                    <input type="file" class="form-control" id="cv" name="cv" accept=".pdf" required>
                </div>
        </div>
        <div class="card-footer" style="display:flex; gap:10px;">
                <a href="<?= $base_path ?>/vue/offres.php" class="btn btn-outline">Retour</a>
                <button type="submit" class="btn btn-primary">Envoyer ma candidature</button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
