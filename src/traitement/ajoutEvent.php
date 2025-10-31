<?php
// Active l'affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Démarrer la session pour les messages
session_start();

use modele\Event;
use repository\EventRepo;

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Event.php';
require_once __DIR__ . '/../repository/EventRepo.php';

// Fonction de redirection avec message d'erreur
function redirectWithError($message) {
    $_SESSION['message'] = $message;
    $_SESSION['messageClass'] = 'danger';
    header('Location: ../../vue/profilUser.php');
    exit;
}

// Dump des données POST pour le débogage
error_log('Données POST reçues : ' . print_r($_POST, true));

// Vérifie que tous les champs obligatoires sont présents et non vides
$requiredFields = [
    'type' => 'Le type est requis',
    'titre' => 'Le titre est requis',
    'description' => 'La description est requise',
    'lieu' => 'Le lieu est requis',
    'nombre_place' => 'Le nombre de places est requis',
    'date_event' => 'La date est requise',
    'etat' => 'L\'état est requis',
    'ref_user' => 'L\'ID utilisateur est manquant'
];

$errors = [];
foreach ($requiredFields as $field => $errorMessage) {
    if (empty($_POST[$field])) {
        $errors[] = $errorMessage;
    }
}

if (!empty($errors)) {
    redirectWithError('Erreur : ' . implode(', ', $errors));
}

// Validation de l'ID utilisateur
$ref_user = filter_var($_POST['ref_user'], FILTER_VALIDATE_INT);
if ($ref_user === false || $ref_user <= 0) {
    redirectWithError('ID utilisateur invalide');
}

    // Conversion correcte du datetime-local
    $dateEventRaw = $_POST['date_event']; // ex: "2025-10-27T14:30"
    $dateEventRaw = str_replace('T', ' ', $dateEventRaw); // => "2025-10-27 14:30"
    $timestamp = strtotime($dateEventRaw);

    if ($timestamp === false) {
        die('Format de date invalide');
    }

    $dateEventTimeStamp = date('Y-m-d H:i:s', $timestamp); // => "2025-10-27 14:30:00"

    // Création de l'objet Event avec les données nettoyées
    try {
        $nouveauEvent = new Event([
            'type'          => htmlspecialchars(trim($_POST["type"])),
            'titre'         => htmlspecialchars(trim($_POST["titre"])),
            'description'   => htmlspecialchars(trim($_POST["description"])),
            'lieu'          => htmlspecialchars(trim($_POST["lieu"])),
            'nombrePlace'   => intval($_POST["nombre_place"]), // Conversion en entier
            'dateEvent'     => $dateEventTimeStamp,
            'etat'          => htmlspecialchars(trim($_POST["etat"])),
            'ref_user'      => $ref_user // Utilisation de la variable validée
        ]);
        
        error_log('Nouvel événement créé : ' . print_r($nouveauEvent, true));
        
        $eventRepository = new EventRepo();
        $eventRepository->ajoutEvent($nouveauEvent);

        // Redirection vers la page de profil avec un message de succès
        $_SESSION['message'] = "L'événement a été créé avec succès !";
        $_SESSION['messageClass'] = "success";
        header("Location: ../../vue/profilUser.php");
        exit;
        
    } catch (Exception $e) {
        error_log('Erreur lors de la création/insertion de l\'événement : ' . $e->getMessage());
        $_SESSION['message'] = "Erreur lors de la création de l'événement : " . $e->getMessage();
        $_SESSION['messageClass'] = "danger";
        header("Location: ../../vue/profilUser.php");
        exit;
    }
