<?php
// Définir le titre de la page
$pageTitle = 'ÉvénementUtilisateur';

// Inclure l'en-tête qui gère la session et l'authentification
require_once __DIR__ . '/../includes/header.php';
?>

require_once __DIR__ . '/../src/repository/EventRepo.php';
use repository\eventRepo;

// Récupération de l'utilisateur depuis l'URL : ex. eventUser.php?id_utilisateur=3
$idUser = isset($_GET['id_utilisateur']) ? intval($_GET['id_utilisateur']) : null;

if (!$idUser) {
    die('ID utilisateur manquant.');
}

$repo = new eventRepo();
$events = $repo->getByUser($idUser); // Retourne un tableau d'objets event
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Événements de l’utilisateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Shared site styles -->
    <link href="../assets/css/site.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-4">
    <h1 class="mb-4">Événements de l’utilisateur #<?= htmlspecialchars($idUser) ?></h1>

    <?php if (empty($events)): ?>
        <div class="alert alert-info">Cet utilisateur n’a créé aucun événement.</div>
    <?php else: ?>
        <table class="table table-striped align-middle">
            <thead>
            <tr>
                <th>Titre</th>
                <th>Type</th>
                <th>Lieu</th>
                <th>Places</th>
                <th>Date de création</th>
                <th>État</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($events as $event): ?>
                <tr>
                    <td><?= htmlspecialchars($event->getTitre()) ?></td>
                    <td><?= htmlspecialchars($event->getType()) ?></td>
                    <td><?= htmlspecialchars($event->getLieu()) ?></td>
                    <td><?= htmlspecialchars($event->getNombrePlace()) ?></td>
                    <td><?= htmlspecialchars($event->getDateCreation()) ?></td>
                    <td><?= htmlspecialchars($event->getEtat()) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<script src="../assets/js/site.js"></script>
</body>
</html>
