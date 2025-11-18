<?php
// Définir le titre de la page
$pageTitle = 'ModifierOffre';

// Inclure l'en-tête qui gère la session et l'authentification
require_once __DIR__ . '/../includes/header.php';
?>

require_once __DIR__ . '/../src/repository/OffreRepo.php';
use repository\OffreRepo;

$offreRepo = new OffreRepo();

if (!isset($_GET['id'])) {
    header('Location: adminOffre.php');
    exit;
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
    header('Location: adminOffre.php');
    exit;
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
    $success = $offreRepo->modifOffre($offre);
    
    if ($success) {
        $_SESSION['success_message'] = 'L\'offre a été modifiée avec succès.';
    } else {
        $_SESSION['error_message'] = 'Une erreur est survenue lors de la modification de l\'offre.';
    }

    // Redirection après modification
    header('Location: adminOffre.php');
    exit;
}

// Types d'offre disponibles
$types = ["CDI", "CDD", "Intérim", "Stage", "Alternance", "Saisonnier", "Freelance"];

// Récupérer la liste des entreprises
require_once __DIR__ . '/../src/repository/EntrepriseRepo.php';
use repository\EntrepriseRepo;
$entrepriseRepo = new EntrepriseRepo();
$entreprises = $entrepriseRepo->listeEntreprise();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une offre - Administration</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS (match index) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Shared site styles -->
    <link href="../assets/css/site.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/admin-style.css">
    <style>
        .etat-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: capitalize;
        }
        .etat-actif { background-color: #e8f5e9; color: #388e3c; }
        .etat-clos { background-color: #ffebee; color: #d32f2f; }
        .etat-brouillon { background-color: #fff3e0; color: #f57c00; }
        
        /* Styles pour les champs de formulaire */
        .form-control {
            display: block;
            width: 100%;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border-radius: 0.25rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        .form-control:focus {
            color: #212529;
            background-color: #fff;
            border-color: #86b7fe;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        
        /* Correction pour les champs dans les options d'état */
        .form-select option {
            padding: 0.5rem;
        }
    </style>
</head>
<body>
<header>
    <div class="container">
        <a class="logo">École Sup.</a>
        <nav>
            <ul>
                <li><a href="../index.php">Accueil</a></li>
                <li><a href="formations.php">Formations</a></li>
                <li><a href="entreprise.php">Entreprises</a></li>
                <li><a href="offres.php">Offres</a></li>
                <li><a href="evenement.php">Evenement</a></li>
                <li><a href="supportContact.php">Contact</a></li>
                <?php if (isset($_SESSION['id_user'])): ?>
                    <li><a href="forum.php">Forum</a></li>
                    <li class="profile-dropdown">
                        <a href="profilUser.php" class="profile-icon">👤</a>
                        <div class="dropdown-content">
                            <span>Bonjour, <?= htmlspecialchars((string)($_SESSION['prenom'] ?? '')) ?> !</span>
                            <a href="profilUser.php" class="profile-button">Mon Profil</a>
                            <a href="../index.php?deco=true" class="logout-button">Déconnexion</a>
                        </div>
                    </li>
                <?php else: ?>
                    <li><a href="connexion.php">Connexion</a></li>
                    <li><a href="inscription.php">Inscription</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<main class="main-content">
    <div class="container">
        <div class="page-header">
            <h1><i class="bi bi-briefcase"></i> Modifier l'offre</h1>
            <a href="adminOffre.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Retour à la liste
            </a>
        </div>
        
        <div class="card">
            <div class="card-body">
                <form method="post" class="form-container">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="titre">Titre du poste :</label>
                                <input type="text" id="titre" name="titre" class="form-control" 
                                       value="<?= htmlspecialchars($offre->getTitre()) ?>" 
                                       style="background: #fff; border: 1px solid #ced4da;" 
                                       required autofocus>
                            </div>
                            
                            <div class="form-group">
                                <label for="type_offre">Type d'offre :</label>
                                <select id="type_offre" name="type_offre" class="form-control" required>
                                    <?php foreach ($types as $t): ?>
                                        <option value="<?= $t ?>" <?= $offre->getTypeOffre() === $t ? 'selected' : '' ?>>
                                            <?= $t ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="salaire">Salaire (facultatif) :</label>
                                <div class="input-group">
                                    <input type="text" id="salaire" name="salaire" class="form-control" 
                                           value="<?= htmlspecialchars($offre->getSalaire()) ?>" 
                                           placeholder="ex: 1900€ brut / mois">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="etat">État :</label>
                                <select id="etat" name="etat" class="form-control" required>
                                    <option value="actif" <?= $offre->getEtat() === 'actif' ? 'selected' : '' ?>>
                                        <span class="etat-badge etat-actif">Actif</span>
                                    </option>
                                    <option value="clos" <?= $offre->getEtat() === 'clos' ? 'selected' : '' ?>>
                                        <span class="etat-badge etat-clos">Clos</span>
                                    </option>
                                    <option value="brouillon" <?= $offre->getEtat() === 'brouillon' ? 'selected' : '' ?>>
                                        <span class="etat-badge etat-brouillon">Brouillon</span>
                                    </option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col">
                            <div class="form-group">
                                <label for="ref_entreprise">Entreprise :</label>
                                <select id="ref_entreprise" name="ref_entreprise" class="form-control">
                                    <option value="">Sélectionnez une entreprise (optionnel)</option>
                                    <?php foreach ($entreprises as $entreprise): ?>
                                        <option value="<?= $entreprise->getIdEntreprise() ?>"
                                            <?= $offre->getRefEntreprise() == $entreprise->getIdEntreprise() ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($entreprise->getNom()) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Date de création :</label>
                                <input type="text" class="form-control" 
                                       value="<?= htmlspecialchars($offre->getDateCreation()) ?>" disabled>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="rue">Adresse :</label>
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" id="rue" name="rue" class="form-control mb-2" 
                                       value="<?= htmlspecialchars($offre->getRue()) ?>" 
                                       placeholder="N° et nom de la rue" required>
                            </div>
                            <div class="col-md-2">
                                <input type="text" id="cp" name="cp" class="form-control mb-2" 
                                       value="<?= htmlspecialchars($offre->getCp()) ?>" 
                                       placeholder="Code postal" required>
                            </div>
                            <div class="col-md-2">
                                <input type="text" id="ville" name="ville" class="form-control mb-2" 
                                       value="<?= htmlspecialchars($offre->getVille()) ?>" 
                                       placeholder="Ville" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description du poste :</label>
                        <textarea id="description" name="description" class="form-control" rows="5" required><?= 
                            htmlspecialchars($offre->getDescription()) 
                        ?></textarea>
                    </div>
                    
                    <div class="form-actions" style="margin-top: 1rem; display: flex; gap: 1rem;">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Enregistrer les modifications
                        </button>
                        <a href="adminOffre.php" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Annuler
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

<script src="../assets/js/site.js"></script>
</body>
</html>
