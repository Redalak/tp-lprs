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

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Gestion des Entreprises</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/admin-style.css">
</head>
<body>
<header>
    <div class="container">
        <a href="#" class="logo">Administration</a>
        <nav>
            <ul>
                <li><a href="adminEntreprise.php">Entreprises</a></li>
                <li><a href="adminOffre.php">Offres</a></li>
                <li><a href="adminEvent.php">Événements</a></li>
                <li><a href="adminUser.php">Utilisateurs</a></li>
                <li><a href="?deconnexion=1">Déconnexion</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="main-content">
    <div class="container">
        <h1>Gestion des Entreprises</h1>
        
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Gestion des entreprises partenaires de l'école.
        </div>

        <h2>Liste des Entreprises</h2>

        <div class="table-responsive">
            <table>
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
                <td class="actions">
                    <a href="modifEntreprise.php?id=<?= $entreprise->getIdEntreprise() ?>" class="btn btn-sm btn-primary" title="Modifier">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <a href="suppEntreprise.php?id=<?= $entreprise->getIdEntreprise() ?>" 
                       class="btn btn-sm btn-danger" 
                       title="Supprimer"
                       onclick="return confirm('Voulez-vous vraiment supprimer cette entreprise ?')">
                        <i class="bi bi-trash"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>


    <section class="mt-5">
        <h2>Ajouter une nouvelle entreprise</h2>
        
        <form method="post" class="form-container">
            <input type="hidden" name="create_entreprise" value="1">
            
            <div class="form-group">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="adresse">Adresse :</label>
                <textarea id="adresse" name="adresse" class="form-control" rows="3" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="site_web">Site Web :</label>
                <input type="url" id="site_web" name="site_web" class="form-control" placeholder="https://www.exemple.com" required>
            </div>
            
            <div class="form-group">
                <label for="motif_partenariat">Motif du partenariat :</label>
                <textarea id="motif_partenariat" name="motif_partenariat" class="form-control" rows="3" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="date_inscription">Date d'inscription :</label>
                <input type="datetime-local" id="date_inscription" name="date_inscription" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="ref_offre">Référence Offre (optionnel) :</label>
                <input type="number" id="ref_offre" name="ref_offre" class="form-control">
            </div>
            
            <div class="form-actions mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Ajouter l'entreprise
                </button>
                <button type="reset" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Réinitialiser
                </button>
            </div>
        </form>
    </section>
</main>

<footer>
    <div class="container">
        <p>&copy; <?= date('Y') ?> École Supérieure. Tous droits réservés.</p>
    </div>
</footer>

<script>
    // Script pour confirmer la suppression
    document.addEventListener('DOMContentLoaded', function() {
        const deleteLinks = document.querySelectorAll('a[onclick*="confirm"]');
        
        deleteLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer cette entreprise ? Cette action est irréversible.')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>

</body>
</html>
