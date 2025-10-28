<?php
// Active l'affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Changement des 'use' pour Entreprise
use modele\Entreprise;
use repository\EntrepriseRepo;

// Changement des 'require_once' pour Entreprise
require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Entreprise.php';
require_once __DIR__ . '/../repository/EntrepriseRepo.php';

// Vérifie que tous les champs obligatoires sont présents et non vides
// (Basé sur les champs de l'entreprise)
if (
    !empty($_POST["nom"]) &&
    !empty($_POST["adresse"]) &&
    !empty($_POST["site_web"]) &&
    !empty($_POST["motif_partenariat"]) &&
    !empty($_POST["date_inscription"])
) {

    // Conversion correcte du datetime-local pour la date d'inscription
    $dateInscriptionRaw = $_POST['date_inscription']; // ex: "2025-10-27T14:30"
    $dateInscriptionRaw = str_replace('T', ' ', $dateInscriptionRaw); // => "2025-10-27 14:30"
    $timestamp = strtotime($dateInscriptionRaw);

    if ($timestamp === false) {
        die('Format de date invalide');
    }

    $dateInscriptionTimestamp = date('Y-m-d H:i:s', $timestamp); // => "2025-10-27 14:30:00"

    // Création de l'objet Entreprise
    $nouvelleEntreprise = new Entreprise([
        'nom'               => $_POST["nom"],
        'adresse'           => $_POST["adresse"],
        'siteWeb'           => $_POST["site_web"], // Clé camelCase pour le constructeur
        'motifPartenariat'  => $_POST["motif_partenariat"], // Clé camelCase
        'dateInscription'   => $dateInscriptionTimestamp,    // La date formatée
        'refOffre'          => $_POST["ref_offre"] // Clé camelCase
    ]);

    // Instanciation du bon repository
    $entrepriseRepository = new EntrepriseRepo();

    try {
        // Insertion en base via la méthode d'EntrepriseRepo
        $entrepriseRepository->ajoutEntreprise($nouvelleEntreprise);

        // Redirection après succès (on garde la même page de succès générique)
        header("Location: ../../vue/pageAjoutReussit.html");
        exit;

    } catch (Exception $e) {
        die('Erreur lors de l\'insertion en BDD : ' . $e->getMessage());
    }

} else {
    // Si un champ est vide, retour au formulaire d'ajout d'entreprise
    header("Location: ../../vue/ajoutEntreprise.php?error=champs_vides");
    exit;
}