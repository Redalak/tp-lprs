<?php
require_once __DIR__ . '/../repository/eventRepo.php';
require_once __DIR__ . '/../modele/event.php';

use repository\eventRepo;
use modele\event;

$repo = new eventRepo();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type           = trim($_POST['type'] ?? '');
    $titre          = trim($_POST['titre'] ?? '');
    $description    = trim($_POST['description'] ?? '');
    $lieu           = trim($_POST['lieu'] ?? '');
    $elementRequis  = trim($_POST['element_requis'] ?? '');
    $nombrePlace    = (int)($_POST['nombre_place'] ?? 0);
    $etat           = $_POST['etat'] ?? 'brouillon';

    $ETATS = ['brouillon','publie','archive'];
    if (!in_array($etat, $ETATS, true)) $etat = 'brouillon';

    if ($type!=='' && $titre!=='' && $description!=='' && $lieu!=='') {
        $e = new event([
            'type'          => $type,
            'titre'         => $titre,
            'description'   => $description,
            'lieu'          => $lieu,
            'elementRequis' => $elementRequis ?: null,
            'nombrePlace'   => $nombrePlace,
            'etat'          => $etat
        ]);
        $repo->ajoutEvent($e);
        header('Location: ../../vue/adminEvent.php?msg=added');
        exit;
    }
}
header('Location: ../../vue/adminEvent.php?msg=error');
exit;
