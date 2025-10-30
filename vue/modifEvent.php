<?php


require_once __DIR__ . '/../src/repository/EventRepo.php';
use repository\EventRepo;

$eventRepo = new EventRepo();

if (!isset($_GET['id'])) {
    header('Location: adminEvent.php');
    exit;
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
    header('Location: adminEvent.php');
    exit;
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

    $success = $eventRepo->modifEvent($event);
    
    if ($success) {
        $_SESSION['success_message'] = 'L\'événement a été modifié avec succès.';
    } else {
        $_SESSION['error_message'] = 'Une erreur est survenue lors de la modification de l\'événement.';
    }

    header('Location: adminEvent.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un événement - Administration</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/admin-style.css">
    <style>
        .etat-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: capitalize;
        }
        .etat-actif { background-color: #e8f5e9; color: #388e3c; }
        .etat-annule { background-color: #ffebee; color: #d32f2f; }
    </style>
</head>
<body>
<header>
    <div class="container">
        <a href="#" class="logo">Administration</a>
        <nav>
            <ul>
                <li><a href="adminEntreprise.php">Entreprises</a></li>
                <li><a href="adminOffre.php">Offres</a></li>
                <li><a class="active" href="adminEvent.php">Événements</a></li>
                <li><a href="adminUser.php">Utilisateurs</a></li>
                <li><a href="?deconnexion=1">Déconnexion</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="main-content">
    <div class="container">
        <div class="page-header">
            <h1><i class="bi bi-calendar-event"></i> Modifier l'événement</h1>
            <a href="adminEvent.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Retour à la liste
            </a>
        </div>
        
        <div class="card">
            <div class="card-body">
                <form method="post" class="form-container">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="titre">Titre de l'événement :</label>
                                <input type="text" id="titre" name="titre" class="form-control" 
                                       value="<?= htmlspecialchars($event->getTitre()) ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="type">Type d'événement :</label>
                                <select id="type" name="type" class="form-control" required>
                                    <option value="conférence" <?= $event->getType() === 'conférence' ? 'selected' : '' ?>>Conférence</option>
                                    <option value="atelier" <?= $event->getType() === 'atelier' ? 'selected' : '' ?>>Atelier</option>
                                    <option value="séminaire" <?= $event->getType() === 'séminaire' ? 'selected' : '' ?>>Séminaire</option>
                                    <option value="autre" <?= $event->getType() === 'autre' ? 'selected' : '' ?>>Autre</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="lieu">Lieu :</label>
                                <input type="text" id="lieu" name="lieu" class="form-control" 
                                       value="<?= htmlspecialchars($event->getLieu()) ?>" required>
                            </div>
                        </div>
                        
                        <div class="col">
                            <div class="form-group">
                                <label for="date_event">Date et heure :</label>
                                <input type="datetime-local" id="date_event" name="date_event" class="form-control" 
                                       value="<?= date('Y-m-d\TH:i', strtotime($event->getDateEvent())) ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="nombre_place">Nombre de places :</label>
                                <input type="number" id="nombre_place" name="nombre_place" class="form-control" 
                                       value="<?= $event->getNombrePlace() ?>" min="1" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="etat">État :</label>
                                <select id="etat" name="etat" class="form-control" required>
                                    <option value="actif" <?= $event->getEtat() === 'actif' ? 'selected' : '' ?>>Actif</option>
                                    <option value="annulé" <?= $event->getEtat() === 'annulé' ? 'selected' : '' ?>>Annulé</option>
                                    <option value="complet" <?= $event->getEtat() === 'complet' ? 'selected' : '' ?>>Complet</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description :</label>
                        <textarea id="description" name="description" class="form-control" rows="5" required><?= 
                            htmlspecialchars($event->getDescription()) 
                        ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="ref_user">ID utilisateur responsable (optionnel) :</label>
                        <input type="number" id="ref_user" name="ref_user" class="form-control" 
                               value="<?= $event->getRefUser() ?>">
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Enregistrer les modifications
                        </button>
                        <a href="adminEvent.php" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<footer>
    <div class="container">
        <p>&copy; <?= date('Y') ?> École Supérieure. Tous droits réservés.</p>
    </div>
</footer>

</body>
</html>
