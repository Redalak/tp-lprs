<?php
declare(strict_types=1);

// Inclure les dépendances nécessaires
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../src/bdd/Bdd.php';
require_once __DIR__ . '/../src/repository/UserRepo.php';
require_once __DIR__ . '/../src/repository/ActualitesRepo.php';
require_once __DIR__ . '/../src/modele/User.php';
require_once __DIR__ . '/../src/modele/Actualites.php';

use repository\UserRepo;
use repository\ActualitesRepo;

// Vérification de l'authentification et des droits d'administration
if (!isset($_SESSION['connexion']) || $_SESSION['connexion'] !== true || empty($_SESSION['id_user'])) {
    header('Location: ../index.php?forbidden=1');
    exit;
}

$userRepo = new UserRepo();
$user = $userRepo->getUserById((int)$_SESSION['id_user']);

// Vérification des droits d'administration
$isAdmin = false;
if ($user) {
    if (method_exists($user, 'isAdmin')) {
        $isAdmin = (bool)$user->isAdmin();
    } elseif (method_exists($user, 'getRole')) {
        $role = strtolower((string)$user->getRole());
        $isAdmin = in_array($role, ['admin','role_admin'], true);
    } elseif (property_exists($user, 'role')) {
        $role = strtolower((string)$user->role);
        $isAdmin = in_array($role, ['admin','role_admin'], true);
    } elseif (!empty($_SESSION['role'])) {
        $role = strtolower((string)$_SESSION['role']);
        $isAdmin = in_array($role, ['admin','role_admin'], true);
    }
}

if (!$isAdmin) {
    header('Location: ../index.php?forbidden=1');
    exit;
}

// Vérifier si un ID a été fourni
if (empty($_GET['id'])) {
    header('Location: adminGestion.php?error=1&message=' . urlencode('Aucun ID d\'actualité fourni'));
    exit;
}

$id = (int)$_GET['id'];
$actualitesRepo = new ActualitesRepo();

// Vérifier si l'actualité existe
$actualite = $actualitesRepo->getActualiteById($id);
if (!$actualite) {
    header('Location: adminGestion.php?error=1&message=' . urlencode('Actualité introuvable'));
    exit;
}

try {
    // Supprimer l'actualité
    $actualitesRepo->suppActualite($id);
    
    // Rediriger avec un message de succès
    header('Location: adminGestion.php?success=1&message=' . urlencode('L\'actualité a été supprimée avec succès'));
    exit;
    
} catch (Exception $e) {
    // En cas d'erreur, rediriger avec un message d'erreur
    error_log('Erreur lors de la suppression de l\'actualité : ' . $e->getMessage());
    header('Location: adminGestion.php?error=1&message=' . urlencode('Une erreur est survenue lors de la suppression de l\'actualité'));
    exit;
}
