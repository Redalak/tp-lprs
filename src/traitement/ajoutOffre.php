<?php
session_start();

// Configuration pour le développement
if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

use modele\offre;
use repository\OffreRepo;
use repository\UserRepo;

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/offre.php';
require_once __DIR__ . '/../repository/OffreRepo.php';
require_once __DIR__ . '/../repository/UserRepo.php';

// Vérifier si l'utilisateur est connecté et a le rôle entreprise
if (!isset($_SESSION['id_user']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'entreprise') {
    $_SESSION['error'] = "Accès refusé. Seules les entreprises peuvent créer des offres.";
    header('Location: /offres');
    exit();
}

// Vérifier que le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nettoyage des entrées
    $titre = trim(filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_STRING));
    $rue = trim(filter_input(INPUT_POST, 'rue', FILTER_SANITIZE_STRING));
    $cp = trim(filter_input(INPUT_POST, 'cp', FILTER_SANITIZE_STRING));
    $ville = trim(filter_input(INPUT_POST, 'ville', FILTER_SANITIZE_STRING));
    $description = trim(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING));
    $type_offre = filter_input(INPUT_POST, 'type_offre', FILTER_SANITIZE_STRING);
    $etat = filter_input(INPUT_POST, 'etat', FILTER_SANITIZE_STRING);

    // Validation des champs obligatoires
    $required = [
        'titre' => $titre,
        'rue' => $rue,
        'cp' => $cp,
        'ville' => $ville,
        'description' => $description,
        'type_offre' => $type_offre
    ];

    $errors = [];

    foreach ($required as $field => $value) {
        if (empty($value)) {
            $errors[] = "Le champ " . ucfirst(str_replace('_', ' ', $field)) . " est obligatoire.";
        }
    }

    // Validation du code postal
    if (!empty($cp) && !preg_match('/^\d{5}$/', $cp)) {
        $errors[] = "Le code postal doit contenir exactement 5 chiffres.";
    }

    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        // Sauvegarder uniquement les données nettoyées
        $_SESSION['form_data'] = [
            'titre' => $titre,
            'rue' => $rue,
            'cp' => $cp,
            'ville' => $ville,
            'description' => $description,
            'type_offre' => $type_offre,
            'etat' => $etat
        ];
        header('Location: /offres/creer');
        exit();
    }

    try {
        // Récupérer l'ID de l'entreprise connectée
        $userRepo = new UserRepo();
        $user = $userRepo->trouverParId($_SESSION['id_user']);

        if (!$user) {
            throw new Exception("Utilisateur non trouvé.");
        }

        $ref_entreprise = $user->getRefEntreprise();

        if (empty($ref_entreprise)) {
            throw new Exception("Aucune entreprise associée à votre compte.");
        }

        // Construire l'objet offre (sans salaire)
        $nouvelleOffre = new offre([
            'titre'          => $titre,
            'rue'            => $rue,
            'cp'             => $cp,
            'ville'          => $ville,
            'description'    => $description,
            'type_offre'     => $type_offre,
            'etat'           => 'ouvert', // Par défaut, l'offre est ouverte
            'ref_entreprise' => $ref_entreprise
        ]);

        // Ajout de l'offre via le repository
        $offreRepo = new OffreRepo();
        $result = $offreRepo->ajouterOffre($nouvelleOffre);

        if ($result) {
            $_SESSION['success'] = "L'offre a été créée avec succès !";
            header('Location: /offres/detail.php?id=' . $result);
            exit();
        } else {
            throw new Exception("Échec de l'ajout de l'offre dans la base de données.");
        }
    } catch (Exception $e) {
        error_log('Erreur création offre : ' . $e->getMessage());
        $_SESSION['error'] = "Une erreur est survenue : " . $e->getMessage();
        $_SESSION['form_data'] = [
            'titre' => $titre,
            'rue' => $rue,
            'cp' => $cp,
            'ville' => $ville,
            'description' => $description,
            'type_offre' => $type_offre,
            'etat' => $etat
        ];
        header('Location: /offres/creer');
        exit();
    }

} else {
    // Si le formulaire n'a pas été soumis en POST
    header('Location: /offres/creer');
    exit();
}
