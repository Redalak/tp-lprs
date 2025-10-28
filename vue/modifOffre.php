<?php
require_once __DIR__ . '/../src/repository/OffreRepo.php';

use repository\OffreRepo;

$offreRepo = new OffreRepo();

if (!isset($_GET['id'])) {
    die('ID de l’offre manquant');
}

$idOffre = (int)$_GET['id'];

// Récupérer l'offre à modifier
$offre = null;
foreach($offreRepo->listeOffre() as $o) {
    if ($o->getIdOffre() === $idOffre) {
        $offre = $o;
        break;
    }
}

if (!$offre) {
    die('Offre introuvable');
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $offre->setTitre($_POST['titre']);
    $offre->setRue($_POST['rue']);
    $offre->setCp($_POST['cp']);
    $offre->setVille($_POST['ville']);
    $offre->setDescription($_POST['description']);
    $offre->setSalaire($_POST['salaire']);
    $offre->setTypeOffre($_POST['type_offre']);
    $offre->setEtat($_POST['etat']);

    // ref_entreprise : si vide -> null
    $refEntreprise = !empty($_POST['ref_entreprise']) ? (int)$_POST['ref_entreprise'] : null;
    $offre->setRefEntreprise($refEntreprise);

    // Sauvegarde en base
    $offreRepo->modifOffre($offre);

    // Redirection après modification
    header('Location: adminOffre.php');
    exit;
}
?>

<h2>Modifier l'offre</h2>

<form method="post">

    <label>Titre du poste :</label><br>
    <input
            type="text"
            name="titre"
            value="<?= htmlspecialchars($offre->getTitre()) ?>"
            required
    ><br><br>

    <label>Adresse (rue) :</label><br>
    <input
            type="text"
            name="rue"
            value="<?= htmlspecialchars($offre->getRue()) ?>"
            required
    ><br><br>

    <label>Code postal :</label><br>
    <input
            type="text"
            name="cp"
            value="<?= htmlspecialchars($offre->getCp()) ?>"
            required
    ><br><br>

    <label>Ville :</label><br>
    <input
            type="text"
            name="ville"
            value="<?= htmlspecialchars($offre->getVille()) ?>"
            required
    ><br><br>

    <label>Description du poste :</label><br>
    <textarea
            name="description"
            required
            rows="4"
            cols="50"
    ><?= htmlspecialchars($offre->getDescription()) ?></textarea><br><br>

    <label>Salaire (facultatif) :</label><br>
    <input
            type="text"
            name="salaire"
            value="<?= htmlspecialchars($offre->getSalaire()) ?>"
            placeholder="ex: 1900€ brut / mois"
    ><br><br>

    <label>Type d'offre :</label><br>
    <select name="type_offre" required>
        <?php
        $types = ["CDI","CDD","Intérim","Stage","Alternance","Saisonnier","Freelance"];
        foreach ($types as $t):
            ?>
            <option value="<?= $t ?>" <?= ($offre->getTypeOffre() === $t ? 'selected' : '') ?>>
                <?= $t ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label>État :</label><br>
    <select name="etat" required>
        <option value="actif" <?= $offre->getEtat() === 'actif' ? 'selected' : '' ?>>Actif</option>
        <option value="clos" <?= $offre->getEtat() === 'clos' ? 'selected' : '' ?>>Clos</option>
        <option value="brouillon" <?= $offre->getEtat() === 'brouillon' ? 'selected' : '' ?>>Brouillon</option>
    </select>
    <br><br>

    <label>Entreprise (ref_entreprise) :</label><br>
    <input
            type="number"
            name="ref_entreprise"
            value="<?= htmlspecialchars($offre->getRefEntreprise()) ?>"
            placeholder="ID entreprise"
    ><br><br>

    <label>Date de création :</label><br>
    <input
            type="text"
            value="<?= htmlspecialchars($offre->getDateCreation()) ?>"
            disabled
    ><br><br>

    <button type="submit">Modifier</button>
</form>
