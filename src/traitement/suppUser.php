<?php
require_once __DIR__ . '/../repository/userRepo.php';

use repository\userRepo;

$repo = new userRepo();

$id = isset($_GET['id_user']) ? (int)$_GET['id_user'] : 0;
if ($id > 0) {
    $repo->suppUser($id);
    header('Location: ../../vue/adminUser.php?msg=deleted');
    exit;
}
header('Location: ../../vue/adminUser.php?msg=error');
exit;
