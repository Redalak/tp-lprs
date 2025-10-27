<?php
// Active l'affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use modele\Event;
use repository\EventRepo;

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Event.php';
require_once __DIR__ . '/../repository/EventRepo.php';

// Vérifie que tous les champs obligatoires sont présents et non vides
if (
    !empty($_POST["type"]) &&
    !empty($_POST["titre"]) &&
    !empty($_POST["description"]) &&
    !empty($_POST["lieu"]) &&
    !empty($_POST["nombre_place"]) &&
    !empty($_POST["date_event"]) &&
    !empty($_POST["etat"]) &&
    !empty($_POST["ref_user"])
) {

    // Conversion correcte du datetime-local
    $dateEventRaw = $_POST['date_event']; // ex: "2025-10-27T14:30"
    $dateEventRaw = str_replace('T', ' ', $dateEventRaw); // => "2025-10-27 14:30"
    $timestamp = strtotime($dateEventRaw);

    if ($timestamp === false) {
        die('Format de date invalide');
    }

    $dateEventTimeStamp = date('Y-m-d H:i:s', $timestamp); // => "2025-10-27 14:30:00"

    // Création de l'objet Event
    $nouveauEvent = new Event([
        'type'          => $_POST["type"],
        'titre'         => $_POST["titre"],
        'description'   => $_POST["description"],
        'lieu'          => $_POST["lieu"],
        'nombrePlace'   => $_POST["nombre_place"], // correspond au setter setNombrePlace
        'dateEvent'     => $dateEventTimeStamp,   // correspond au setter setDateEvent
        'etat'          => $_POST["etat"],
        'ref_user'      => $_POST["ref_user"]
    ]);

    $eventRepository = new EventRepo();

    try {
        // Insertion en base
        $eventRepository->ajoutEvent($nouveauEvent);

        // Redirection après succès
        header("Location: ../../vue/pageAjoutReussit.html");
        exit;

    } catch (Exception $e) {
        die('Erreur lors de l\'insertion en BDD : ' . $e->getMessage());
    }

} else {
    // Si un champ est vide
    header("Location: ../../vue/ajoutEvent.php?error=champs_vides");
    exit;
}
