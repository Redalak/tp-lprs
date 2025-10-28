<?php
session_start();

// ✅ Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

require_once __DIR__ . '/../src/repository/EventRepo.php';
require_once __DIR__ . '/../src/repository/ReservationRepo.php';

use repository\EventRepo;
use repository\ReservationRepo;

$eventRepo = new EventRepo();
$resRepo = new ReservationRepo();

$userId = $_SESSION['user_id'];
$eventId = intval($_GET['id']);

// ✅ Vérifie que l'événement existe
$event = $eventRepo->findEventById($eventId);

if (!$event) {
    die("Événement introuvable.");
}

// ✅ Vérifie qu'il reste des places
if ($event->getNombrePlace() <= 0) {
    header('Location: events.php?msg=complet');
    exit;
}

// ✅ Vérifie que l'utilisateur n'est pas déjà inscrit
if ($resRepo->dejaReserve($userId, $eventId)) {
    header('Location: events.php?msg=deja');
    exit;
}

// ✅ Enregistre la réservation + maj des places
$resRepo->ajouterReservation($userId, $eventId);
$eventRepo->decrementerPlaces($eventId);

// ✅ Succès ✅
header('Location: events.php?msg=ok');
exit;
