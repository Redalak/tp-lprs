<?php
// Définir le titre de la page
$pageTitle = 'DétailsEntreprise';

// Inclure l'en-tête qui gère la session et l'authentification
require_once __DIR__ . '/../includes/header.php';
?>

require_once __DIR__ . '/../src/repository/EntrepriseRepo.php';
require_once __DIR__ . '/../src/repository/OffreRepo.php';

use repository\EntrepriseRepo;
use repository\OffreRepo;

// Vérifier si l'ID de l'entreprise est présent dans l'URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: adminEntreprise.php');
    exit;
}

$id_entreprise = (int)$_GET['id'];
$entrepriseRepo = new EntrepriseRepo();
$offreRepo = new OffreRepo();

// Récupérer les informations de l'entreprise
$entreprise = $entrepriseRepo->getEntrepriseById($id_entreprise);

// Vérifier si l'entreprise existe
if (!$entreprise) {
    header('Location: adminEntreprise.php');
    exit;
}

// Récupérer les offres de l'entreprise
$offres = $entrepriseRepo->getOffresParEntreprise($id_entreprise);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'entreprise - Administration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Bootstrap CSS (match index) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/admin-style.css">
    <!-- Shared site styles -->
    <link href="../assets/css/site.css" rel="stylesheet">
    <style>
        .company-header {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .company-name {
            color: #0d6efd;
            margin-bottom: 1rem;
        }
        .company-info {
            margin-bottom: 1.5rem;
        }
        .company-info p {
            margin: 0.5rem 0;
            color: #555;
        }
        .offres-list {
            margin-top: 2rem;
        }
        .offre-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .offre-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .offre-title {
            color: #0d6efd;
            margin-bottom: 0.5rem;
        }
        .offre-meta {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        .offre-description {
            color: #495057;
            margin: 1rem 0;
        }
        .no-offres {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
            background: #f8f9fa;
            border-radius: 8px;
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
            <div class="company-header">
                <h1 class="company-name"><?= htmlspecialchars($entreprise->getNom()) ?></h1>
                
                <div class="company-info">
                    <?php if ($entreprise->getAdresse()): ?>
                        <p><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($entreprise->getAdresse()) ?></p>
                    <?php endif; ?>
                    
                    <?php if ($entreprise->getSiteWeb()): ?>
                        <p><i class="bi bi-globe"></i> 
                            <a href="<?= htmlspecialchars($entreprise->getSiteWeb()) ?>" target="_blank">
                                <?= htmlspecialchars($entreprise->getSiteWeb()) ?>
                            </a>
                        </p>
                    <?php endif; ?>
                    
                    <?php if ($entreprise->getMotifPartenariat()): ?>
                        <p><i class="bi bi-info-circle"></i> <?= htmlspecialchars($entreprise->getMotifPartenariat()) ?></p>
                    <?php endif; ?>
                    
                    <p><i class="bi bi-calendar"></i> Inscrite le <?= date('d/m/Y', strtotime($entreprise->getDateInscription())) ?></p>
                    <p><i class="bi bi-briefcase"></i> <?= count($offres) ?> offre(s) publiée(s)</p>
                </div>
                
                <a href="adminEntreprise.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Retour à la liste
                </a>
            </div>

            <div class="offres-list">
                <h2>Offres d'emploi</h2>
                
                <?php if (count($offres) > 0): ?>
                    <?php foreach ($offres as $offre): ?>
                        <div class="offre-card">
                            <h3 class="offre-title">
                                <a href="modifOffre.php?id=<?= $offre->getIdOffre() ?>">
                                    <?= htmlspecialchars($offre->getTitre()) ?>
                                </a>
                            </h3>
                            
                            <div class="offre-meta">
                                <span class="badge"><?= htmlspecialchars($offre->getTypeOffre()) ?></span>
                                <?php if ($offre->getSalaire()): ?>
                                    <span class="ms-2">• <?= htmlspecialchars($offre->getSalaire()) ?></span>
                                <?php endif; ?>
                                <span class="ms-2">• 📍 <?= htmlspecialchars($offre->getVille()) ?></span>
                                <span class="ms-2">• 📅 <?= date('d/m/Y', strtotime($offre->getDateCreation())) ?></span>
                            </div>
                            
                            <div class="offre-description">
                                <?= nl2br(htmlspecialchars(substr($offre->getDescription(), 0, 250) . (strlen($offre->getDescription()) > 250 ? '...' : ''))) ?>
                            </div>
                            
                            <a href="modifOffre.php?id=<?= $offre->getIdOffre() ?>" class="btn btn-sm btn-outline-primary">
                                Voir l'offre <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-offres">
                        <i class="bi bi-info-circle" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                        <p>Aucune offre publiée pour le moment.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> Tous droits réservés</p>
        </div>
    </footer>
<script src="../assets/js/site.js"></script>
</body>
</html>
