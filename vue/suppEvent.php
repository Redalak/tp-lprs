<?php
require_once __DIR__ . '/../src/repository/EventRepo.php';
use repository\EventRepo;

$eventRepo = new EventRepo();

if (!isset($_GET['id'])) {
    die('ID de l’événement manquant');
}

$idEvent = (int)$_GET['id'];

// Vérifier que l'événement existe avant de supprimer
$event = null;
foreach($eventRepo->listeEvent() as $e) {
    if ($e->getIdEvent() === $idEvent) {
        $event = $e;
        break;
    }
}

if (!$event) {
    die('Événement introuvable');
}

// Suppression de l'événement
$eventRepo->suppEvent($idEvent);

// Redirection vers la liste des événements
header('Location: listeEvents.php');
exit;
