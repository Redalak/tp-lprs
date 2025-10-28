<?php
require_once __DIR__ . '/../src/repository/OffreRepo.php';
require_once __DIR__ . '/../src/modele/offre.php';

use repository\OffreRepo;
use modele\offre; // Assurez-vous que votre classe offre est incluse

$offreRepo = new OffreRepo();
$offres = $offreRepo->listeOffre();

// Traitement du formulaire de création
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_offre'])) {

    $titre        = $_POST['titre'];
    $rue          = $_POST['rue'];
    $cp           = $_POST['cp'];
    $ville        = $_POST['ville'];
    $description  = $_POST['description'];
    $salaire      = $_POST['salaire'] ?? ''; // facultatif
    $type_offre   = $_POST['type_offre'];
    $etat         = $_POST['etat'];

    // On construit l'objet offre à partir des données du formulaire
    // (grâce à ton constructeur + hydrate, ça va appeler les bons setters)
    $newOffre = new offre([
        'titre'        => $titre,
        'rue'          => $rue,
        'cp'           => $cp,
        'ville'        => $ville,
        'description'  => $description,
        'salaire'      => $salaire,
        'type_offre'   => $type_offre,
        'etat'         => $etat,
        // 'date_creation' est géré par la BDD en auto
        // 'ref_entreprise' on ne l'utilise pas ici
    ]);

    // Insertion en base
    $offreRepo->ajoutOffre($newOffre);

    // Rafraîchir la liste (éviter le resubmit)
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>

<h2>Liste des offres d'emploi</h2>

<table border="1" cellpadding="5" cellspacing="0">
    <thead>
    <tr>
        <th>ID</th>
        <th>Titre</th>
        <th>Adresse</th>
        <th>Description</th>
        <th>Salaire</th>
        <th>Type</th>
        <th>État</th>
        <th>Date création</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($offres as $offre): ?>
        <tr>
            <td><?= $offre->getIdOffre() ?></td>

            <td><?= htmlspecialchars($offre->getTitre()) ?></td>

            <td>
                <?= htmlspecialchars($offre->getRue()) ?><br>
                <?= htmlspecialchars($offre->getCp()) ?> <?= htmlspecialchars($offre->getVille()) ?>
            </td>

            <td style="max-width:250px;">
                <?= nl2br(htmlspecialchars($offre->getDescription())) ?>
            </td>

            <td>
                <?php
                $sal = $offre->getSalaire();
                echo ($sal !== null && $sal !== '' ? htmlspecialchars($sal) : '-');
                ?>
            </td>

            <td><?= htmlspecialchars($offre->getTypeOffre()) ?></td>

            <td><?= htmlspecialchars($offre->getEtat()) ?></td>

            <td><?= htmlspecialchars($offre->getDateCreation()) ?></td>

            <td>
                <a href="modifOffre.php?id=<?= $offre->getIdOffre() ?>">Modifier</a> |
                <a href="suppOffre.php?id=<?= $offre->getIdOffre() ?>"
                   onclick="return confirm('Voulez-vous vraiment supprimer cette offre ?')">
                    Supprimer
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>


<h2>Créer une nouvelle offre</h2>

<form method="post" style="margin-bottom: 30px; border: 1px solid #ccc; padding: 10px;">
    <input type="hidden" name="create_offre" value="1">

    <label>Titre du poste :</label><br>
    <input type="text" name="titre" required><br><br>

    <label>Rue (adresse du poste) :</label><br>
    <input type="text" name="rue" required><br><br>

    <label>Code postal :</label><br>
    <input type="text" name="cp" required><br><br>

    <label>Ville :</label><br>
    <input type="text" name="ville" required><br><br>

    <label>Description du poste :</label><br>
    <textarea name="description" required></textarea><br><br>

    <label>Salaire (facultatif) :</label><br>
    <input
            type="text"
            name="salaire"
            placeholder="ex: 1900€ brut / mois"
    ><br><br>

    <label>Type d'offre :</label><br>
    <select name="type_offre" required>
        <option value="">-- Sélectionner --</option>
        <option value="CDI">CDI</option>
        <option value="CDD">CDD</option>
        <option value="Stage">Stage</option>
        <option value="Alternance">Alternance</option>
        <option value="Autre">Saisonnier</option>
    </select><br><br>

    <label>État :</label><br>
    <select name="etat" required>
        <option value="">-- Sélectionner --</option>
        <option value="ouvert">Ouvert</option>
        <option value="ferme">Ferme</option>
        <option value="brouillon">Brouillon</option>
    </select><br><br>

    <button type="submit">Créer l'offre</button>
</form>
