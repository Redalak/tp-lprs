<?php
require_once __DIR__ . '/../repository/eventRepo.php';

use repository\eventRepo;

$repo = new eventRepo();

$id = isset($_GET['id_evenement']) ? (int)$_GET['id_evenement'] : 0;
if ($id > 0) {
    $repo->suppEvent($id);
    header('Location: ../../vue/adminEvent.php?msg=deleted');
    exit;
}
header('Location: ../../vue/adminEvent.php?msg=error');
exit;
