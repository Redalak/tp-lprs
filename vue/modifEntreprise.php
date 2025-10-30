<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['connexionAdmin']) || $_SESSION['connexionAdmin'] !== true) {
    header('Location: ../index.php');
    exit;
}

require_once __DIR__ . '/../src/repository/EntrepriseRepo.php';
require_once __DIR__ . '/../src/modele/Entreprise.php';
use repository\EntrepriseRepo;

$entrepriseRepo = new EntrepriseRepo();

if (!isset($_GET['id'])) {
    header('Location: adminEntreprise.php');
    exit;
}

$idEntreprise = (int)$_GET['id'];

// Récupérer l'entreprise à modifier
$entreprise = null;
foreach($entrepriseRepo->listeEntreprise() as $e) {
    if ($e->getIdEntreprise() === $idEntreprise) {
        $entreprise = $e;
        break;
    }
}

if (!$entreprise) {
    header('Location: adminEntreprise.php');
    exit;
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mise à jour de l'objet entreprise
    $entreprise->setNom($_POST['nom']);
    $entreprise->setAdresse($_POST['adresse']);
    $entreprise->setSiteWeb($_POST['site_web']);
    $entreprise->setMotifPartenariat($_POST['motif_partenariat']);
    $entreprise->setDateInscription($_POST['date_inscription']);
    
    // Gérer le champ optionnel ref_offre
    $refOffre = !empty($_POST['ref_offre']) ? (int)$_POST['ref_offre'] : null;
    $entreprise->setRefOffre($refOffre);

    // Appel à la méthode de modification du Repo
    $success = $entrepriseRepo->modifEntreprise($entreprise);
    
    if ($success) {
        $_SESSION['success_message'] = 'L\'entreprise a été modifiée avec succès.';
    } else {
        $_SESSION['error_message'] = 'Une erreur est survenue lors de la modification de l\'entreprise.';
    }
    
    header('Location: adminEntreprise.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une entreprise - Administration</title>
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
        <div class="page-header">
            <h1><i class="bi bi-building"></i> Modifier l'entreprise</h1>
            <a href="adminEntreprise.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Retour à la liste
            </a>
        </div>
        
        <div class="card">
            <div class="card-body">
                <form method="post" class="form-container">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="nom">Nom de l'entreprise :</label>
                                <input type="text" id="nom" name="nom" class="form-control" 
                                       value="<?= htmlspecialchars($entreprise->getNom()) ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="site_web">Site web :</label>
                                <div class="input-group">
                                    <span class="input-group-text">https://</span>
                                    <input type="url" id="site_web" name="site_web" class="form-control" 
                                           value="<?= htmlspecialchars($entreprise->getSiteWeb()) ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="date_inscription">Date d'inscription :</label>
                                <input type="datetime-local" id="date_inscription" name="date_inscription" 
                                       class="form-control" 
                                       value="<?= date('Y-m-d\TH:i', strtotime($entreprise->getDateInscription())) ?>" 
                                       required>
                            </div>
                        </div>
                        
                        <div class="col">
                            <div class="form-group">
                                <label for="ref_offre">Référence offre (optionnel) :</label>
                                <input type="number" id="ref_offre" name="ref_offre" class="form-control" 
                                       value="<?= $entreprise->getRefOffre() ? $entreprise->getRefOffre() : '' ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="adresse">Adresse :</label>
                        <textarea id="adresse" name="adresse" class="form-control" rows="3" required><?= 
                            htmlspecialchars($entreprise->getAdresse()) 
                        ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="motif_partenariat">Motif du partenariat :</label>
                        <textarea id="motif_partenariat" name="motif_partenariat" class="form-control" rows="4" required><?= 
                            htmlspecialchars($entreprise->getMotifPartenariat()) 
                        ?></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Enregistrer les modifications
                        </button>
                        <a href="adminEntreprise.php" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<footer>
    <div class="container">
        <p>&copy; <?= date('Y') ?> École Supérieure. Tous droits réservés.</p>
    </div>
</footer>

</body>
</html>