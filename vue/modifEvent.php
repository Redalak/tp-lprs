<?php
require_once __DIR__ . '/../src/repository/EventRepo.php';
use repository\EventRepo;

$eventRepo = new EventRepo();

if (!isset($_GET['id'])) {
    die('ID de l’événement manquant');
}

$idEvent = (int)$_GET['id'];

// Récupérer l'événement à modifier
$event = null;
foreach($eventRepo->listeEvent() as $e) {
    if ($e->getIdEvent() === $idEvent) {
        $event = $e;
        break;
    }
}

if (!$event) {
    die('Événement introuvable');
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event->setType($_POST['type']);
    $event->setTitre($_POST['titre']);
    $event->setDescription($_POST['description']);
    $event->setLieu($_POST['lieu']);
    $event->setNombrePlace((int)$_POST['nombre_place']);
    $event->setDateEvent($_POST['date_event']);
    $event->setEtat($_POST['etat']);

    // Vérifier si ref_user est rempli, sinon mettre null
    $refUser = !empty($_POST['ref_user']) ? (int)$_POST['ref_user'] : null;
    $event->setRefUser($refUser);

    $eventRepo->modifEvent($event);

    header('Location: listeEvents.php');
    exit;
}
?>

<h2>Modifier l'événement</h2>

<form method="post">
    <label>Titre :</label><br>
    <input type="text" name="titre" value="<?= htmlspecialchars($event->getTitre()) ?>" required><br><br>

    <label>Type :</label><br>
    <input type="text" name="type" value="<?= htmlspecialchars($event->getType()) ?>" required><br><br>

    <label>Description :</label><br>
    <textarea name="description" required><?= htmlspecialchars($event->getDescription()) ?></textarea><br><br>

    <label>Lieu :</label><br>
    <input type="text" name="lieu" value="<?= htmlspecialchars($event->getLieu()) ?>" required><br><br>

    <label>Nombre de places :</label><br>
    <input type="number" name="nombre_place" value="<?= $event->getNombrePlace() ?>" required><br><br>

    <label>Date de l'événement :</label><br>
    <input type="datetime-local" name="date_event" value="<?= date('Y-m-d\TH:i', strtotime($event->getDateEvent())) ?>" required><br><br>

    <label>Etat :</label><br>
    <select name="etat" required>
        <option value="actif" <?= $event->getEtat() === 'actif' ? 'selected' : '' ?>>Actif</option>
        <option value="annulé" <?= $event->getEtat() === 'annulé' ? 'selected' : '' ?>>Annulé</option>
    </select><br><br>

    <label>ID utilisateur (optionnel) :</label><br>
    <input type="number" name="ref_user" value="<?= $event->getRefUser() ?>"><br><br>

    <button type="submit">Modifier</button>
</form>
