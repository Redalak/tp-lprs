<?php
// Active l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclure le modèle et le repository pour Entreprise
require_once __DIR__ . '/../src/repository/EntrepriseRepo.php';
require_once __DIR__ . '/../src/modele/Entreprise.php';

use repository\EntrepriseRepo;
use modele\Entreprise;

$entrepriseRepo = new EntrepriseRepo();

// Traitement du formulaire de création d'entreprise
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_entreprise'])) {

    // Traitement de la date (identique à votre script d'ajout)
    $dateInscriptionRaw = $_POST['date_inscription'];
    $dateInscriptionRaw = str_replace('T', ' ', $dateInscriptionRaw);
    $dateInscriptionTimestamp = date('Y-m-d H:i:s', strtotime($dateInscriptionRaw));

    // Récupérer la ref_offre (optionnelle)
    $ref_offre = !empty($_POST['ref_offre']) ? (int)$_POST['ref_offre'] : null;

    $newEntreprise = new Entreprise([
        'nom'               => $_POST['nom'],
        'adresse'           => $_POST['adresse'],
        'siteWeb'           => $_POST['site_web'], // 'siteWeb' correspond au constructeur
        'motifPartenariat'  => $_POST['motif_partenariat'], // 'motifPartenariat'
        'dateInscription'   => $dateInscriptionTimestamp, // 'dateInscription'
        'refOffre'          => $ref_offre // 'refOffre'
    ]);

    $entrepriseRepo->ajoutEntreprise($newEntreprise);

    // Rafraîchir la liste (en redirigeant vers la page actuelle)
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Récupérer la liste des entreprises *après* l'ajout potentiel
$entreprises = $entrepriseRepo->listeEntreprise();
?>

    <h2>Liste des Entreprises</h2>

    <table border="1" cellpadding="5" cellspacing="0" style="width: 100%; border-collapse: collapse;">
        <thead>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Adresse</th>
            <th>Site Web</th>
            <th>Motif Partenariat</th>
            <th>Date Inscription</th>
            <th>Ref. Offre</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($entreprises as $entreprise): ?>
            <tr>
                <td><?= $entreprise->getIdEntreprise() ?></td>
                <td><?= htmlspecialchars($entreprise->getNom()) ?></td>
                <td><?= htmlspecialchars($entreprise->getAdresse()) ?></td>
                <td><?= htmlspecialchars($entreprise->getSiteWeb()) ?></td>
                <td><?= htmlspecialchars($entreprise->getMotifPartenariat()) ?></td>
                <td><?= $entreprise->getDateInscription() ?></td>
                <td><?= $entreprise->getRefOffre() ?></td>
                <td>
                    <a href="modifEntreprise.php?id=<?= $entreprise->getIdEntreprise() ?>">Modifier</a> |
                    <a href="suppEntreprise.php?id=<?= $entreprise->getIdEntreprise() ?>" onclick="return confirm('Voulez-vous vraiment supprimer cette entreprise ?')">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>


    <h2>Ajouter une nouvelle entreprise</h2>

    <form method="post" style="margin-top: 30px; border: 1px solid #ccc; padding: 10px;">
        <input type="hidden" name="create_entreprise" value="1">

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

        <button type="submit">Ajouter l'entreprise</button>
    </form><?php
