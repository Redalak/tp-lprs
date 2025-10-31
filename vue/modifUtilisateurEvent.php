<?php
session_start();

// Vérification de l'authentification
if (!isset($_SESSION['id_user'])) {
    header('Location: connexion.php');
    exit;
}

require_once __DIR__ . '/../src/repository/EventRepo.php';
use repository\EventRepo;

$userId = (int)$_SESSION['id_user'];
$eventRepo = new EventRepo();
$message = '';
$messageClass = '';
$event = null;
$editionMode = false;

// Récupérer les événements créés par l'utilisateur
$evenementsUtilisateur = $eventRepo->getEvenementsParUtilisateur($userId);

// Vérifier si on est en mode édition d'un événement
// Vérifier si on est en mode édition d'un événement spécifique
if (isset($_GET['id'])) {
    $eventId = (int)$_GET['id'];
    $event = $eventRepo->getEvenementById($eventId);
    
    // Vérifier que l'événement existe et appartient à l'utilisateur
    if ($event) {
        if ($event->getRefUser() === $userId) {
            $editionMode = true;
        } else {
            // L'événement n'appartient pas à l'utilisateur
            $event = null;
            
            $messageClass = 'danger';
        }
    } else {
        // L'événement n'existe pas
        $message = 'L\'événement demandé n\'existe pas.';
        $messageClass = 'danger';
    }
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['supprimer_evenement']) && isset($_POST['event_id'])) {
        // Traitement de la suppression
        $eventId = (int)$_POST['event_id'];
        $eventToDelete = $eventRepo->getEvenementById($eventId);
        
        if ($eventToDelete && $eventToDelete->getRefUser() === $userId) {
            $success = $eventRepo->supprimerEvent($eventId);
            if ($success) {
                $message = 'L\'événement a été supprimé avec succès.';
                $messageClass = 'success';
                $evenementsUtilisateur = $eventRepo->getEvenementsParUtilisateur($userId);
            } else {
                $message = 'Une erreur est survenue lors de la suppression de l\'événement.';
                $messageClass = 'danger';
            }
        }
    } else if (isset($_POST['mettre_a_jour'])) {
        // Traitement de la mise à jour
        $eventId = (int)$_POST['event_id'];
        $event = $eventRepo->getEvenementById($eventId);
        
        if ($event && $event->getRefUser() === $userId) {
            $event->setTitre(htmlspecialchars(trim($_POST['titre'])));
            $event->setType(htmlspecialchars(trim($_POST['type'])));
            $event->setDescription(htmlspecialchars(trim($_POST['description'])));
            $event->setLieu(htmlspecialchars(trim($_POST['lieu'])));
            $event->setNombrePlace((int)$_POST['nombre_place']);
            $event->setDateEvent($_POST['date_event']);
            $event->setEtat(htmlspecialchars(trim($_POST['etat'])));
            
            $success = $eventRepo->modifEvent($event);
            
            if ($success) {
                $message = 'L\'événement a été mis à jour avec succès.';
                $messageClass = 'success';
                $evenementsUtilisateur = $eventRepo->getEvenementsParUtilisateur($userId);
            } else {
                $message = 'Une erreur est survenue lors de la mise à jour de l\'événement.';
                $messageClass = 'danger';
            }
        }
    } else if (isset($_POST['annuler_edition'])) {
        // Annulation de l'édition
        $editionMode = false;
        $event = null;
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $editionMode ? 'Modifier un événement' : 'Mes événements' ?> - Mon Compte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #0A4D68;
            --secondary-color: #088395;
            --background-color: #f8f9fa;
        }
        body {
            background-color: var(--background-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 56px;
        }
        .main-content {
            padding: 2rem 0;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
        }
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        .etat-badge {
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: capitalize;
            display: inline-block;
            min-width: 80px;
            text-align: center;
        }
        .etat-actif { background-color: #e8f5e9; color: #2e7d32; }
        .etat-annule { background-color: #ffebee; color: #c62828; }
        .etat-termine { background-color: #e3f2fd; color: #1565c0; }
        .table th { font-weight: 600; }
        .form-container {
            max-width: 800px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <main class="main-content">
        <div class="container">
            <?php if ($message): ?>
                <div class="alert alert-<?= $messageClass ?> alert-dismissible fade show">
                    <?= $message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($editionMode && $event): ?>
                <!-- Formulaire d'édition -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Modifier l'événement</h5>
                        <a href="modifUtilisateurEvent.php" class="btn btn-sm btn-outline-light">
                            <i class="bi bi-x-lg"></i> Annuler
                        </a>
                    </div>
                    <div class="card-body">
                        <form method="post" class="form-container">
                            <input type="hidden" name="event_id" value="<?= $event->getIdEvent() ?>">
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="titre" class="form-label">Titre de l'événement</label>
                                        <input type="text" class="form-control" id="titre" name="titre" 
                                               value="<?= htmlspecialchars($event->getTitre()) ?>" required>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="type" class="form-label">Type d'événement</label>
                                        <select id="type" name="type" class="form-select" required>
                                            <option value="conférence" <?= $event->getType() === 'conférence' ? 'selected' : '' ?>>Conférence</option>
                                            <option value="atelier" <?= $event->getType() === 'atelier' ? 'selected' : '' ?>>Atelier</option>
                                            <option value="séminaire" <?= $event->getType() === 'séminaire' ? 'selected' : '' ?>>Séminaire</option>
                                            <option value="autre" <?= $event->getType() === 'autre' ? 'selected' : '' ?>>Autre</option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="lieu" class="form-label">Lieu</label>
                                        <input type="text" class="form-control" id="lieu" name="lieu" 
                                               value="<?= htmlspecialchars($event->getLieu()) ?>" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="date_event" class="form-label">Date et heure</label>
                                        <input type="datetime-local" class="form-control" id="date_event" name="date_event" 
                                               value="<?= date('Y-m-d\TH:i', strtotime($event->getDateEvent())) ?>" required>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="nombre_place" class="form-label">Nombre de places</label>
                                        <input type="number" class="form-control" id="nombre_place" name="nombre_place" 
                                               value="<?= $event->getNombrePlace() ?>" min="1" required>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="etat" class="form-label">État</label>
                                        <select id="etat" name="etat" class="form-select" required>
                                            <option value="actif" <?= $event->getEtat() === 'actif' ? 'selected' : '' ?>>Actif</option>
                                            <option value="annulé" <?= $event->getEtat() === 'annulé' ? 'selected' : '' ?>>Annulé</option>
                                            <option value="complet" <?= $event->getEtat() === 'complet' ? 'selected' : '' ?>>Complet</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-4">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" 
                                          rows="5" required><?= htmlspecialchars($event->getDescription()) ?></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="submit" name="mettre_a_jour" class="btn btn-primary">
                                    <i class="bi bi-check-lg"></i> Mettre à jour
                                </button>
                                <a href="modifUtilisateurEvent.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-lg"></i> Annuler
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Liste des événements -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-calendar3"></i> Mes événements</h5>
                    <a href="profilUser.php" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-arrow-left"></i> Retour au profil
                    </a>
                </div>
                <div class="card-body">
                    <?php if (empty($evenementsUtilisateur)): ?>
                        <div class="alert alert-info mb-0">
                            Vous n'avez pas encore créé d'événement.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Titre</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Lieu</th>
                                        <th>Places</th>
                                        <th>État</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($evenementsUtilisateur as $evt): 
                                        $dateEvent = new DateTime($evt->getDateEvent());
                                        $etatClass = '';
                                        switch($evt->getEtat()) {
                                            case 'actif': $etatClass = 'etat-actif'; break;
                                            case 'annulé': $etatClass = 'etat-annule'; break;
                                            case 'complet': $etatClass = 'etat-termine'; break;
                                        }
                                    ?>
                                        <tr>
                                            <td><?= htmlspecialchars($evt->getTitre()) ?></td>
                                            <td><?= ucfirst(htmlspecialchars($evt->getType())) ?></td>
                                            <td><?= $dateEvent->format('d/m/Y H:i') ?></td>
                                            <td><?= htmlspecialchars($evt->getLieu()) ?></td>
                                            <td><?= $evt->getNombrePlace() ?></td>
                                            <td><span class="etat-badge <?= $etatClass ?>"><?= $evt->getEtat() ?></span></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="?id=<?= $evt->getIdEvent() ?>" 
                                                       class="btn btn-outline-primary"
                                                       title="Modifier l'événement">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form method="post" class="d-inline" 
                                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?');">
                                                        <input type="hidden" name="event_id" value="<?= $evt->getIdEvent() ?>">
                                                        <button type="submit" name="supprimer_evenement" class="btn btn-outline-danger"
                                                                title="Supprimer l'événement">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="text-end mt-3">
                <a href="ajoutEvent.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Créer un nouvel événement
                </a>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script pour gérer les tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>
</html>
