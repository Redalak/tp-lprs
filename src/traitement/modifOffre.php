<?php
require_once __DIR__ . '/../repository/offreRepo.php';
require_once __DIR__ . '/../modele/offre.php';

use repository\offreRepo;
use modele\offre;

$repo = new offreRepo();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id_offre'] ?? 0);
    if ($id <= 0) { header('Location: ../../vue/adminOffre.php?msg=error'); exit; }

    $type  = $_POST['type_offre'] ?? 'Autre';
    $okTypes = ['CDI','CDD','Stage','Alternance','Autre'];
    if (!in_array($type, $okTypes, true)) $type = 'Autre';

    $etat  = $_POST['etat'] ?? 'brouillon';
    $okEtats = ['ouvert','ferme','brouillon'];
    if (!in_array($etat, $okEtats, true)) $etat = 'brouillon';

    $o = new offre([
        'idOffre'     => $id,
        'titre'       => trim($_POST['titre'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'mission'     => trim($_POST['mission'] ?? ''),
        'salaire'     => trim($_POST['salaire'] ?? ''),
        'typeOffre'   => $type,
        'etat'        => $etat,
    ]);

    if ($o->getTitre()!=='' && $o->getDescription()!=='') {
        $repo->modifOffre($o);
        header('Location: ../../vue/adminOffre.php?msg=updated');
        exit;
    }
    header('Location: ../../vue/modifOffre.php?id_offre='.$id.'&err=1');
    exit;
}
header('Location: ../../vue/adminOffre.php');
exit;
