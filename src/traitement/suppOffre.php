<?php
require_once __DIR__ . '/../repository/offreRepo.php';
use repository\offreRepo;

$repo = new offreRepo();

$id = isset($_GET['id_offre']) ? (int)$_GET['id_offre'] : 0;
if ($id > 0) {
    $repo->suppOffre($id);
    header('Location: ../../vue/adminOffre.php?msg=deleted');
    exit;
}
header('Location: ../../vue/adminOffre.php?msg=error');
exit;
