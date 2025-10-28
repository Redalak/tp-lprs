<?php

require_once __DIR__ . '/../src/repository/EventRepo.php';
require_once __DIR__ . '/../src/modele/Event.php';

use repository\EventRepo;

session_start();

$eventRepo = new EventRepo();
$events = $eventRepo->listeEvent();

$today = date("Y-m-d");

// Filtrer : en cours + places restantes > 0
$eventsEnCours = array_filter($events, function($event) use ($today) {
    return ($event->getEtat() === 'en cours'
            || $event->getDateEvent() >= $today)
        && $event->getNombrePlace() > 0;
});

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements en cours</title>

    <style>
        body {
            margin:0;
            font-family: Arial, sans-serif;
            background:#eef2f3;
        }
        h1 {
            text-align:center;
            margin-top:30px;
            color:#0A4D68;
        }
        .container {
            display:flex;
            justify-content:center;
            flex-wrap:wrap;
            gap:20px;
            padding:30px;
        }
        .card {
            width:300px;
            background:#fff;
            padding:20px;
            border-radius:10px;
            box-shadow:0 4px 12px rgba(0,0,0,0.1);
            transition:0.3s;
        }
        .card:hover {
            transform:scale(1.03);
        }
        .card h3 {
            color:#088395;
            margin-bottom:10px;
        }
        .btn {
            display:inline-block;
            margin-top:10px;
            padding:8px 14px;
            border:none;
            border-radius:6px;
            color:white;
            cursor:pointer;
            text-decoration:none;
        }
        .btn-info { background:#0A4D68; }
        .btn-res { background:#27ae60; }
    </style>
</head>
<body>

<?php if (!empty($_GET['msg'])): ?>
    <p style="text-align:center;color:#c62828;font-weight:bold;">
        <?= $_GET['msg'] === 'ok' ? "Réservation confirmée ✅" : "" ?>
        <?= $_GET['msg'] === 'deja' ? "Vous avez déjà réservé cet événement ⚠️" : "" ?>
        <?= $_GET['msg'] === 'complet' ? "Événement complet ❌" : "" ?>
    </p>
<?php endif; ?>


<h1>Événements en cours</h1>

<div class="container">
    <?php if (empty($eventsEnCours)): ?>
        <p>Aucun événement en cours ou disponible pour le moment.</p>
    <?php else: ?>
        <?php foreach ($eventsEnCours as $event): ?>
            <div class="card">
                <h3><?= htmlspecialchars($event->getTitre()) ?></h3>
                <p><strong>Lieu :</strong> <?= htmlspecialchars($event->getLieu()) ?></p>
                <p><strong>Date :</strong> <?= htmlspecialchars($event->getDateEvent()) ?></p>
                <p><strong>Description :</strong> <?= nl2br(htmlspecialchars($event->getDescription())) ?></p>
                <p><strong>Places restantes :</strong> <?= htmlspecialchars($event->getNombrePlace()) ?></p>

                <a class="btn btn-info" href="detailsEvent.php?id=<?= $event->getIdEvent() ?>">Détails</a>

                <!-- ✅ Si pas connecté → on envoie vers connexion.php -->
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a class="btn btn-res" href="connexion.php?redirect=reservation.php&id=<?= $event->getIdEvent() ?>">Réserver</a>

                    <!-- ✅ Si connecté → réservation directe -->
                <?php else: ?>
                    <a class="btn btn-res" href="../src/traitement/reservation.php?id=<?= $event->getIdEvent() ?>">Réserver</a>
                <?php endif; ?>

            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
