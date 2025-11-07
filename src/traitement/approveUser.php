<?php
// src/traitement/approveUser.php
session_start();

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../repository/UserRepo.php';

use repository\UserRepo;

// Basic guard: only logged-in admins
$isLogged = !empty($_SESSION['connexion']) && $_SESSION['connexion'] === true;
$isAdmin  = !empty($_SESSION['connexionAdmin']) && $_SESSION['connexionAdmin'] === true;
if (!$isLogged || !$isAdmin) {
    header('Location: ../../vue/connexion.php');
    exit;
}

$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = isset($_GET['action']) ? strtolower((string)$_GET['action']) : '';

if ($id <= 0 || !in_array($action, ['approve','deny'], true)) {
    header('Location: ../../vue/adminUser.php?err=bad_params');
    exit;
}

$repo = new UserRepo();
$ok   = $repo->setApproval($id, $action === 'approve');

$qs = $ok ? 'ok=1' : 'ok=0';
header('Location: ../../vue/adminUser.php?'.$qs);
exit;
