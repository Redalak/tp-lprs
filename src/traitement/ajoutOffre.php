<?php
// Pour voir les erreurs (en dev)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../repository/OffreRepo.php';
require_once __DIR__ . '/../modele/Offre.php';

use repository\offreRepo;
use modele\offre;

// Vérifie les champs requis
if (
    empty($_POST['titre']) ||
    empty($_POST['description']) ||
    empty($_POST['type_offre']) ||
    empty($_POST['etat'])
) {
    header('Location: ../../vue/OffreAdmin.php?msg=error');
    exit;
}

// Récupération et nettoyage des données
$titre = trim($_POST['titre']);
$description = trim($_POST['description']);
$mission = trim($_POST['mission'] ?? '');
$salaire = $_POST['salaire'] ?? null;
$typeOffre = $_POST['type_offre'];
$etat = $_POST['etat'];

// Convertir salaire en float ou null
$salaire = ($salaire === '' || $salaire === null) ? null : (float)$salaire;

// Création de l'objet Offre
$o = new offre([
    'titre' => $titre,
    'description' => $description,
    'mission' => $mission,
    'salaire' => $salaire,
    'typeOffre' => $typeOffre,
    'etat' => $etat,
]);

$repo = new offreRepo();

try {
    $repo->ajoutOffre($o);
    // Redirection vers la page admin avec message de succès
    header('Location: ../../vue/adminOffre.php?msg=added');
    exit;
} catch (Exception $e) {
    // En dev, affiche l’erreur sinon redirige avec msg d’erreur
    if (ini_get('display_errors')) {
        echo "Erreur : " . $e->getMessage();
    } else {
        header('Location: ../../vue/adminOffre.php?msg=error');
    }
    exit;
}
