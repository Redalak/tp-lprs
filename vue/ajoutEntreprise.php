<?php
// Définir le titre de la page
$pageTitle = 'AjouterEntreprise';

// Inclure l'en-tête qui gère la session et l'authentification
require_once __DIR__ . '/../includes/header.php';
?>

// Optionnel : Démarrer la session si vous avez besoin d'infos utilisateur (ex: pour ref_user)
// session_start();

// Vérifie s'il y a un message d'erreur venant du script de traitement
$errorMessage = '';
if (isset($_GET['error']) && $_GET['error'] === 'champs_vides') {
    $errorMessage = 'Erreur : Tous les champs doivent être remplis.';
}
?>

<h2>Ajouter une nouvelle entreprise</h2>

<?php if ($errorMessage): ?>
    <p style="color: red;"><?= htmlspecialchars($errorMessage) ?></p>
<?php endif; ?>

<form action="../src/traitement/ajoutEntreprise.php" method="post">
    <label>Nom :</label><br>
    <input type="text" name="nom" required><br><br>

    <label>Adresse :</label><br>
    <textarea name="adresse" required></textarea><br><br>

    <label>Site Web :</label><br>
    <input type="url" name="site_web" placeholder="https://www.exemple.com" required><br><br>

    <label>Motif du partenariat :</label><br>
    <textarea name="motif_partenariat" required></textarea><br><br>

    <label>Date d'inscription :</label><br>
    <input type="datetime-local" name="date_inscription" required><br><br>

    <label>ID Offre (optionnel) :</label><br>
    <input type="number" name="ref_offre"><br><br>

    <button type="submit">Ajouter</button>
</form>