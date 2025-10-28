<?php
require_once __DIR__ . '/../src/repository/EntrepriseRepo.php';
require_once __DIR__ . '/../src/modele/Entreprise.php';
use repository\EntrepriseRepo;

$entrepriseRepo = new EntrepriseRepo();

if (!isset($_GET['id'])) {
    die('ID de l’entreprise manquant');
}

$idEntreprise = (int)$_GET['id'];

// Vérifier que l'entreprise existe avant de supprimer (bonne pratique)
// NOTE: C'est plus efficace avec getEntrepriseById($idEntreprise) si vous l'avez
$entreprise = null;
foreach($entrepriseRepo->listeEntreprise() as $e) {
    if ($e->getIdEntreprise() === $idEntreprise) {
        $entreprise = $e;
        break;
    }
}

if (!$entreprise) {
    die('Entreprise introuvable');
}

// Suppression de l'entreprise via le Repo
$entrepriseRepo->suppEntreprise($idEntreprise);

// Redirection vers la liste des entreprises
header('Location: adminEntreprise.php');
exit;