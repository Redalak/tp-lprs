<?php
// Définir le titre de la page
$pageTitle = 'AdminÉvénement';

// Inclure l'en-tête qui gère la session et l'authentification
require_once __DIR__ . '/../includes/header.php';
?>

session_start();
require_once __DIR__ . '/../src/repository/EventRepo.php';
require_once __DIR__ . '/../src/repository/UserRepo.php';
use repository\EventRepo;
use modele\Event;
use repository\UserRepo;

$eventRepo = new EventRepo();
$events = $eventRepo->listeEvent();
$prochains = $eventRepo->getProchainsEvents(3);

// Récupérer prénom/nom pour l'en-tête
$prenom = $_SESSION['prenom'] ?? '';
$nom    = $_SESSION['nom'] ?? '';
if (!empty($_SESSION['id_user'])) {
    try {
        $uRepo = new UserRepo();
        $u = $uRepo->getUserById((int)$_SESSION['id_user']);
        if ($u && method_exists($u, 'getPrenom')) { $prenom = $u->getPrenom(); }
        if ($u && method_exists($u, 'getNom'))    { $nom    = $u->getNom(); }
    } catch (\Throwable $e) {}
}

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

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Gestion des Événements</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS (match index) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Shared site styles -->
    <link href="../assets/css/site.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/admin-style.css">
    <style>
        /* Dropdown profil minimal */
        .profile-dropdown{position:relative;display:inline-block}
        .profile-icon{font-size:1.5rem;cursor:pointer;padding:5px}
        .profile-icon::after{display:none!important}
        .dropdown-content{display:none;position:absolute;background:#fff;min-width:220px;box-shadow:0 6px 24px rgba(0,0,0,.06);border-radius:12px;padding:20px;right:0;top:100%;z-index:1001;text-align:center}
        .profile-dropdown:hover .dropdown-content{display:block}
        .dropdown-content a{display:block;padding:10px 15px;margin-bottom:8px;border-radius:5px;text-decoration:none;font-weight:500;color:#fff!important}
        .dropdown-content a::after{display:none}
        .profile-button{background:#088395}
        .profile-button:hover{background:#0A4D68}
        .logout-button{background:#e74c3c}
        .logout-button:hover{background:#c0392b}
    </style>
</head>
<body>
<header>
    <div class="container">
        <a class="logo">École Sup.</a>
        <nav>
            <ul>
                <li><a href="../index.php">Accueil</a></li>
                <li><a href="formations.php">Formations</a></li>
                <li><a href="entreprise.php">Entreprises</a></li>
                <li><a href="offres.php">Offres</a></li>
                <li><a href="evenement.php">Evenement</a></li>
                <li><a href="supportContact.php">Contact</a></li>
                <?php if (!empty($_SESSION['id_user'])): ?>
                    <li><a href="forum.php">Forum</a></li>
                    <li class="profile-dropdown">
                        <a href="profilUser.php" class="profile-icon">👤</a>
                        <div class="dropdown-content">
                            <span>Bonjour, <?= htmlspecialchars((string)($_SESSION['prenom'] ?? '')) ?> !</span>
                            <a href="profilUser.php" class="profile-button">Mon Profil</a>
                            <a href="../index.php?deco=true" class="logout-button">Déconnexion</a>
                        </div>
                    </li>
                <?php else: ?>
                    <li><a href="connexion.php">Connexion</a></li>
                    <li><a href="inscription.php">Inscription</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<main class="main-content">
    <div class="container">
        <h1>Gestion des Événements</h1>
        
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Gestion des événements de l'école.
        </div>

        <section class="mt-4">
            <h2>Événements à venir (3 prochains)</h2>
            <div class="table-responsive">
                <?php if (!empty($prochains)): ?>
                    <table>
                        <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Type</th>
                            <th>Lieu</th>
                            <th>Date</th>
                            <th>État</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($prochains as $ev): ?>
                            <tr>
                                <td><?= htmlspecialchars($ev->getTitre()) ?></td>
                                <td><?= htmlspecialchars($ev->getType()) ?></td>
                                <td><?= htmlspecialchars($ev->getLieu()) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($ev->getDateEvent())) ?></td>
                                <td>
                                    <span class="badge <?= $ev->getEtat() === 'publie' ? 'badge-success' : 'badge-secondary' ?>">
                                        <?= ucfirst(htmlspecialchars($ev->getEtat())) ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <a href="modifEvent.php?id=<?= $ev->getIdEvent() ?>" class="btn btn-sm btn-primary" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="suppEvent.php?id=<?= $ev->getIdEvent() ?>" class="btn btn-sm btn-danger" title="Supprimer" onclick="return confirm('Voulez-vous vraiment supprimer cet événement ?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-warning"><i class="bi bi-exclamation-triangle"></i> Aucun événement à venir.</div>
                <?php endif; ?>
            </div>
        </section>

        <div class="table-responsive">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Lieu</th>
                    <th>Places</th>
                    <th>Date</th>
                    <th>État</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($events as $event): ?>
                    <tr>
                        <td><?= $event->getIdEvent() ?></td>
                        <td><?= htmlspecialchars($event->getTitre()) ?></td>
                        <td><?= htmlspecialchars($event->getType()) ?></td>
                        <td><?= htmlspecialchars(substr($event->getDescription(), 0, 50)) ?>...</td>
                        <td><?= htmlspecialchars($event->getLieu()) ?></td>
                        <td class="text-center"><?= $event->getNombrePlace() ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($event->getDateEvent())) ?></td>
                        <td>
                            <span class="badge <?= $event->getEtat() === 'actif' ? 'badge-success' : 'badge-danger' ?>">
                                <?= ucfirst(htmlspecialchars($event->getEtat())) ?>
                            </span>
                        </td>
                        <td class="actions">
                            <a href="modifEvent.php?id=<?= $event->getIdEvent() ?>" class="btn btn-sm btn-primary" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="suppEvent.php?id=<?= $event->getIdEvent() ?>" 
                               class="btn btn-sm btn-danger" 
                               title="Supprimer"
                               onclick="return confirm('Voulez-vous vraiment supprimer cet événement ?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <section class="mt-5">
            <h2>Créer un nouvel événement</h2>
            
            <form method="post" class="form-container">
                <input type="hidden" name="create_event" value="1">
                
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="titre">Titre :</label>
                            <input type="text" id="titre" name="titre" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="type">Type :</label>
                            <select id="type" name="type" class="form-control" required>
                                <option value="">Sélectionnez un type</option>
                                <option value="conférence">Conférence</option>
                                <option value="atelier">Atelier</option>
                                <option value="séminaire">Séminaire</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="lieu">Lieu :</label>
                            <input type="text" id="lieu" name="lieu" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="col">
                        <div class="form-group">
                            <label for="date_event">Date et heure :</label>
                            <input type="datetime-local" id="date_event" name="date_event" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="nombre_place">Nombre de places :</label>
                            <input type="number" id="nombre_place" name="nombre_place" class="form-control" min="1" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="etat">État :</label>
                            <select id="etat" name="etat" class="form-control" required>
                                <option value="publie">Publié</option>
                                <option value="brouillon">Brouillon</option>
                                <option value="archive">Archive</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description :</label>
                    <textarea id="description" name="description" class="form-control" rows="4" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="ref_user">ID utilisateur responsable (optionnel) :</label>
                    <input type="number" id="ref_user" name="ref_user" class="form-control">
                </div>
                
                <div class="form-actions mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-calendar-plus"></i> Créer l'événement
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Réinitialiser
                    </button>
                </div>
            </form>
        </section>
    </div>
</main>

<footer>
    <div class="container">
        <p>&copy; <?= date('Y') ?> École Supérieure. Tous droits réservés.</p>
    </div>
</footer>

<script>
    // Script pour confirmer la suppression
    document.addEventListener('DOMContentLoaded', function() {
        // Mise en forme des dates dans le tableau
        const dateCells = document.querySelectorAll('td:nth-child(7)');
        dateCells.forEach(cell => {
            const date = new Date(cell.textContent);
            if (!isNaN(date.getTime())) {
                cell.textContent = date.toLocaleString('fr-FR', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
        });
    });
</script>

</body>
</html>

