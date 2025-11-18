<?php
// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclure le fichier d'authentification
require_once __DIR__ . "/../src/auth/check_auth.php";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LPRS - <?= $pageTitle ?? 'Accueil' ?></title>
    <!-- Ajoutez ici vos liens CSS -->
</head>
<body>
    <!-- Votre en-tête de site ici -->
