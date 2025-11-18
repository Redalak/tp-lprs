<?php
declare(strict_types=1);

// Inclure les dépendances nécessaires
require_once __DIR__ . '/../src/bdd/Bdd.php';
require_once __DIR__ . '/../src/repository/ActualitesRepo.php';
require_once __DIR__ . '/../src/modele/Actualites.php';

use repository\ActualitesRepo;
use modele\Actualites;

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['contenu'])) {
    header('Location: adminGestion.php?error=1&message=' . urlencode('Le contenu est requis'));
    exit;
}

// Récupérer et nettoyer le contenu
$contenu = trim($_POST['contenu']);

// Valider la longueur du contenu
if (strlen($contenu) < 10) {
    header('Location: adminGestion.php?error=1&message=' . urlencode('Le contenu doit faire au moins 10 caractères'));
    exit;
}

try {
    // Créer une nouvelle actualité
    $actualite = new Actualites([
        'contexte' => $contenu
    ]);
    
    // Enregistrer en base de données
    $actualitesRepo = new ActualitesRepo();
    $actualite = $actualitesRepo->ajoutActualite($actualite);
    
    // Rediriger avec un message de succès
    header('Location: adminGestion.php?success=1&message=' . urlencode('L\'actualité a été ajoutée avec succès'));
    exit;
    
} catch (Exception $e) {
    // En cas d'erreur, rediriger avec un message d'erreur
    error_log('Erreur lors de l\'ajout de l\'actualité : ' . $e->getMessage());
    header('Location: adminGestion.php?error=1&message=' . urlencode('Une erreur est survenue lors de l\'ajout de l\'actualité'));
    exit;
}
