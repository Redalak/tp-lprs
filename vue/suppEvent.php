<?php
// Définir le titre de la page
$pageTitle = 'SupprimerÉvénement';

// Inclure l'en-tête qui gère la session et l'authentification
require_once __DIR__ . '/../includes/header.php';
?>

require_once __DIR__ . '/../src/repository/EventRepo.php';
use repository\EventRepo;

$eventRepo = new EventRepo();

if (!isset($_GET['id'])) {
    die('ID de l’événement manquant');
}

$idEvent = (int)$_GET['id'];

// Vérifier que l'événement existe (sans filtrer les passés)
$event = $eventRepo->getEvenementById($idEvent);
if (!$event) {
    die('Événement introuvable');
}

// Suppression de l'événement
$eventRepo->suppEvent($idEvent);

// Redirection vers la page d'administration des événements
header('Location: adminEvent.php');
exit;
