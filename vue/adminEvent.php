<?php
require_once __DIR__ . '/../src/repository/EventRepo.php';
use repository\EventRepo;
use modele\Event; // Assurez-vous que votre classe Event est incluse

$eventRepo = new EventRepo();
$events = $eventRepo->listeEvent();

// Traitement du formulaire de création
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_event'])) {
    $type = $_POST['type'];
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $lieu = $_POST['lieu'];
    $nombre_place = (int)$_POST['nombre_place'];
    $date_event = $_POST['date_event'];
    $etat = $_POST['etat'];
    $ref_user = !empty($_POST['ref_user']) ? (int)$_POST['ref_user'] : null;

    $newEvent = new Event([
        'type' => $type,
        'titre' => $titre,
        'description' => $description,
        'lieu' => $lieu,
        'nombrePlace' => $nombre_place,
        'dateEvent' => $date_event,
        'etat' => $etat,
        'ref_user' => $ref_user
    ]);

    $eventRepo->ajoutEvent($newEvent);

    // Rafraîchir la liste
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>

<h2>Liste des événements</h2>

<table border="1" cellpadding="5" cellspacing="0">
    <thead>
    <tr>
        <th>ID</th>
        <th>Titre</th>
        <th>Type</th>
        <th>Description</th>
        <th>Lieu</th>
        <th>Nombre de places</th>
        <th>Date</th>
        <th>Etat</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($events as $event): ?>
        <tr>
            <td><?= $event->getIdEvent() ?></td>
            <td><?= htmlspecialchars($event->getTitre()) ?></td>
            <td><?= htmlspecialchars($event->getType()) ?></td>
            <td><?= htmlspecialchars($event->getDescription()) ?></td>
            <td><?= htmlspecialchars($event->getLieu()) ?></td>
            <td><?= $event->getNombrePlace() ?></td>
            <td><?= $event->getDateEvent() ?></td>
            <td><?= htmlspecialchars($event->getEtat()) ?></td>
            <td>
                <a href="modifEvent.php?id=<?= $event->getIdEvent() ?>">Modifier</a> |
                <a href="suppEvent.php?id=<?= $event->getIdEvent() ?>" onclick="return confirm('Voulez-vous vraiment supprimer cet événement ?')">Supprimer</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>


<h2>Créer un nouvel événement</h2>

<form method="post" style="margin-bottom: 30px; border: 1px solid #ccc; padding: 10px;">
    <input type="hidden" name="create_event" value="1">

    <label>Titre :</label><br>
    <input type="text" name="titre" required><br><br>

    <label>Type :</label><br>
    <input type="text" name="type" required><br><br>

    <label>Description :</label><br>
    <textarea name="description" required></textarea><br><br>

    <label>Lieu :</label><br>
    <input type="text" name="lieu" required><br><br>

    <label>Nombre de places :</label><br>
    <input type="number" name="nombre_place" required><br><br>

    <label>Date de l'événement :</label><br>
    <input type="datetime-local" name="date_event" required><br><br>

    <label>Etat :</label><br>
    <select name="etat" required>
        <option value="actif">Actif</option>
        <option value="annulé">Annulé</option>
    </select><br><br>

    <label>ID utilisateur (optionnel) :</label><br>
    <input type="number" name="ref_user"><br><br>

    <button type="submit">Créer l'événement</button>
</form>

