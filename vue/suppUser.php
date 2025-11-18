<?php
// Définir le titre de la page
$pageTitle = 'SupprimerUtilisateur';

// Inclure l'en-tête qui gère la session et l'authentification
require_once __DIR__ . '/../includes/header.php';
?>

require_once __DIR__ . '/../src/repository/UserRepo.php';
require_once __DIR__ . '/../src/modele/User.php';  // Assurez-vous que cette ligne est présente pour inclure la classe User
use repository\UserRepo;

$userRepo = new UserRepo();

// Vérifier si l'ID de l'utilisateur est passé via l'URL
if (!isset($_GET['id_user'])) {
    die('ID de l’utilisateur manquant');
}

$idUser = (int)$_GET['id_user'];  // Convertir l'ID en entier pour éviter des injections

// Vérifier que l'utilisateur existe avant de supprimer
$user = $userRepo->getUserById($idUser);  // Récupérer l'objet utilisateur directement depuis la méthode getUserById()

if (!$user) {
    die('Utilisateur introuvable');
}

// Suppression de l'utilisateur
$userRepo->suppUser($user);  // Passer l'objet utilisateur à la méthode suppUser()

// Redirection vers la liste des utilisateurs après suppression
header('Location: adminUser.php');
exit;
