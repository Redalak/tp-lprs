<?php
require_once __DIR__ . '/../src/repository/OffreRepo.php';

use repository\OffreRepo;

$offreRepo = new OffreRepo();

if (!isset($_GET['id'])) {
    die('ID de l’offre manquant');
}

$idOffre = (int)$_GET['id'];

// Vérifier que l'offre existe avant de supprimer
$offre = null;
foreach($offreRepo->listeOffre() as $o) {
    if ($o->getIdOffre() === $idOffre) {
        $offre = $o;
        break;
    }
}

if (!$offre) {
    die('Offre introuvable');
}

// Suppression de l'offre
$offreRepo->suppOffre($idOffre);

// Redirection vers la liste des offres
header('Location: adminOffre.php');
exit;
