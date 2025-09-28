<?php
require_once __DIR__ . '/../repository/eventRepo.php';
require_once __DIR__ . '/../modele/event.php';

use repository\eventRepo;
use modele\event;

$repo = new eventRepo();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id             = (int)($_POST['id_evenement'] ?? 0);
    $type           = trim($_POST['type'] ?? '');
    $titre          = trim($_POST['titre'] ?? '');
    $description    = trim($_POST['description'] ?? '');
    $lieu           = trim($_POST['lieu'] ?? '');
    $elementRequis  = trim($_POST['element_requis'] ?? '');
    $nombrePlace    = (int)($_POST['nombre_place'] ?? 0);
    $etat           = $_POST['etat'] ?? 'brouillon';

    $ETATS = ['brouillon','publie','archive'];
    if (!in_array($etat, $ETATS, true)) $etat = 'brouillon';

    if ($id>0 && $type!=='' && $titre!=='' && $description!=='' && $lieu!=='') {
        $e = new event([
            'idEvent'       => $id,
            'type'          => $type,
            'titre'         => $titre,
            'description'   => $description,
            'lieu'          => $lieu,
            'elementRequis' => $elementRequis ?: null,
            'nombrePlace'   => $nombrePlace,
            'etat'          => $etat
        ]);
        $repo->modifEvent($e);
        header('Location: ../../vue/adminEvent.php?msg=updated');
        exit;
    }
    header('Location: ../../vue/modifEvent.php?id_evenement='.$id.'&err=1');
    exit;
}
header('Location: ../../vue/adminEvent.php');
exit;
