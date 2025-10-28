<?php
// Active l'affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use modele\offre;
use repository\OffreRepo;

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/offre.php';
require_once __DIR__ . '/../repository/OffreRepo.php';

// Vérif champs obligatoires
if (
    !empty($_POST["titre"]) &&
    !empty($_POST["rue"]) &&
    !empty($_POST["cp"]) &&
    !empty($_POST["ville"]) &&
    !empty($_POST["description"]) &&
    !empty($_POST["type_offre"]) &&
    !empty($_POST["etat"])
) {
    $titre        = $_POST["titre"];
    $rue          = $_POST["rue"];
    $cp           = $_POST["cp"];
    $ville        = $_POST["ville"];
    $description  = $_POST["description"];
    $salaire      = $_POST["salaire"] ?? ''; // peut être vide
    $type_offre   = $_POST["type_offre"];
    $etat         = $_POST["etat"];

    // Construire l'objet offre
    $nouvelleOffre = new offre([
        'titre'        => $titre,
        'rue'          => $rue,
        'cp'           => $cp,
        'ville'        => $ville,
        'description'  => $description,
        'salaire'      => $salaire,
        'type_offre'   => $type_offre,
        'etat'         => $etat,
        // date_creation auto par la BDD
        // ref_entreprise retiré si tu ne l'utilises plus
    ]);

    // CEINTURE + BRETELLES : on force les setters pour remplir les propriétés internes
    $nouvelleOffre->setTitre($titre);
    $nouvelleOffre->setRue($rue);
    $nouvelleOffre->setCp($cp);
    $nouvelleOffre->setVille($ville);
    $nouvelleOffre->setDescription($description);
    $nouvelleOffre->setSalaire($salaire);
    $nouvelleOffre->setTypeOffre($type_offre);
    $nouvelleOffre->setEtat($etat);

    $offreRepository = new OffreRepo();

    try {
        $offreRepository->ajoutOffre($nouvelleOffre);

        header("Location: ../../vue/pageAjoutReussit.html");
        exit;
    } catch (Exception $e) {
        die('Erreur lors de l\'insertion en BDD : ' . $e->getMessage());
    }

} else {
    header("Location: ../../vue/ajoutOffre.php?error=champs_vides");
    exit;
}
