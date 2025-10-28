<?php
// On suppose que les modèles sont inclus via un autoloader ou manuellement
require_once __DIR__ . '/../src/repository/EntrepriseRepo.php';
require_once __DIR__ . '/../src/modele/Entreprise.php';
use repository\EntrepriseRepo;

$entrepriseRepo = new EntrepriseRepo();

if (!isset($_GET['id'])) {
    die('ID de l’entreprise manquant');
}

$idEntreprise = (int)$_GET['id'];

// Récupérer l'entreprise à modifier
// NOTE: C'est plus efficace avec getEntrepriseById($idEntreprise) si vous l'avez
$entreprise = null;
foreach($entrepriseRepo->listeEntreprise() as $e) {
    if ($e->getIdEntreprise() === $idEntreprise) {
        $entreprise = $e;
        break;
    }
}

if (!$entreprise) {
    die('Entreprise introuvable');
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mise à jour de l'objet entreprise
    $entreprise->setNom($_POST['nom']);
    $entreprise->setAdresse($_POST['adresse']);
    $entreprise->setSiteWeb($_POST['site_web']);
    $entreprise->setMotifPartenariat($_POST['motif_partenariat']);
    $entreprise->setDateInscription($_POST['date_inscription']); // Format datetime-local

    // Gérer le champ optionnel ref_offre
    $refOffre = !empty($_POST['ref_offre']) ? (int)$_POST['ref_offre'] : null;
    $entreprise->setRefOffre($refOffre);

    // Appel à la méthode de modification du Repo
    $entrepriseRepo->modifEntreprise($entreprise);

    // Redirection vers la liste (adaptez le nom du fichier si besoin)
    header('Location: adminEntreprise.php');
    exit;
}
?>

<h2>Modifier l'entreprise</h2>

<form method="post">
    <label>Nom :</label><br>
    <input type="text" name="nom" value="<?= htmlspecialchars($entreprise->getNom()) ?>" required><br><br>

    <label>Adresse :</label><br>
    <textarea name="adresse" required><?= htmlspecialchars($entreprise->getAdresse()) ?></textarea><br><br>

    <label>Site Web :</label><br>
    <input type="url" name="site_web" value="<?= htmlspecialchars($entreprise->getSiteWeb()) ?>" required><br><br>

    <label>Motif du partenariat :</label><br>
    <textarea name="motif_partenariat" required><?= htmlspecialchars($entreprise->getMotifPartenariat()) ?></textarea><br><br>

    <label>Date d'inscription :</label><br>
    <input type="datetime-local" name="date_inscription" value="<?= date('Y-m-d\TH:i', strtotime($entreprise->getDateInscription())) ?>" required><br><br>

    <label>ID Offre (optionnel) :</label><br>
    <input type="number" name="ref_offre" value="<?= $entreprise->getRefOffre() ?>"><br><br>

    <button type="submit">Modifier</button>
</form>