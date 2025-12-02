<?php
// Définir le titre de la page
$pageTitle = 'Evenement';

// Inclure l'en-tête qui gère la session et l'authentification
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../src/repository/EventRepo.php';
require_once __DIR__ . '/../src/modele/Event.php';
require_once __DIR__ . '/../src/repository/InscriptionEventRepo.php';

use repository\EventRepo;
use repository\InscriptionEventRepo;

// La session est déjà démarrée dans header.php

// Initialisation des variables
$userLoggedIn = null;
$estInscrit = false;
$estCreateur = false;
$evenement = null;
$participants = [];
$message = '';
$messageClass = '';

// Initialisation des repositories
$evenementRepo = new EventRepo();
$inscriptionRepo = new InscriptionEventRepo();

// Récupérer l'utilisateur connecté (pour le header)
if (!empty($_SESSION['connexion']) && $_SESSION['connexion'] === true && !empty($_SESSION['id_user'])) {
    require_once __DIR__ . '/../src/repository/UserRepo.php';
    $userRepo = new \repository\UserRepo();
    $userLoggedIn = $userRepo->getUserById($_SESSION['id_user']);
}

// Vérifier si on affiche un événement spécifique
if (isset($_GET['event_id']) && is_numeric($_GET['event_id'])) {
    $evenement = $evenementRepo->getEvenementById($_GET['event_id']);
    if (!$evenement) {
        header('Location: evenement.php');
        exit();
    }
    
    // Traitement de la suppression d'un participant (doit être avant la récupération des participants)
    if ($userLoggedIn && isset($_POST['supprimer_participant'])) {
        $idParticipant = filter_input(INPUT_POST, 'participant_id', FILTER_VALIDATE_INT);
        if ($idParticipant) {
            $estCreateur = ($userLoggedIn->getIdUser() === $evenement->getRefUser());
            
            if ($estCreateur) {
                $suppressionReussie = $inscriptionRepo->supprimerParticipant(
                    $evenement->getIdEvent(),
                    $idParticipant
                );
                if ($suppressionReussie) {
                    $_SESSION['message'] = "Le participant a été supprimé avec succès.";
                    $_SESSION['messageClass'] = "success";
                    // Recharger la page pour actualiser la liste
                    header("Location: evenement.php?event_id=" . $evenement->getIdEvent());
                    exit();
                } else {
                    $message = "Une erreur est survenue lors de la suppression du participant.";
                    $messageClass = "error";
                }
            }
        }
    }
    
    // Traitement de la désinscription d'un utilisateur
    if ($userLoggedIn && isset($_POST['desinscrire_event']) && isset($_POST['event_id'])) {
        $eventId = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
        $userId = $userLoggedIn->getIdUser();
        
        if ($eventId) {
            // Utiliser la méthode annulerParticipation qui gère la transaction complète
            $annulationReussie = $inscriptionRepo->annulerParticipation($userId, $eventId);
            
            if ($annulationReussie) {
                $_SESSION['message'] = "Vous avez été désinscrit de l'événement avec succès.";
                $_SESSION['messageClass'] = "success";
                // Recharger la page pour actualiser l'affichage
                header("Location: evenement.php" . (isset($_GET['event_id']) ? '?event_id=' . $_GET['event_id'] : ''));
                exit();
            } else {
                $message = "Une erreur est survenue lors de la désinscription.";
                $messageClass = "error";
            }
        }
    }
    
    // Vérifier si l'utilisateur est déjà inscrit à l'événement
    if ($userLoggedIn) {
        $estInscrit = $inscriptionRepo->estInscrit($userLoggedIn->getIdUser(), $evenement->getIdEvent());
        // Vérifier si l'utilisateur est le créateur de l'événement
        $estCreateur = ($userLoggedIn->getIdUser() === $evenement->getRefUser());
        
        // Récupérer la liste des participants si l'utilisateur est le créateur de l'événement
        if ($estCreateur) {
            $participants = $inscriptionRepo->getParticipantsEvenement($evenement->getIdEvent());
        }
    }
} else {
    // Si pas d'ID spécifique, récupérer tous les événements
    $evenements = $evenementRepo->getTousLesEvenements();
}

// Récupérer les messages de session s'ils existent
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageClass = $_SESSION['messageClass'] ?? 'info';
    unset($_SESSION['message']);
    unset($_SESSION['messageClass']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Shared site styles -->
    <link href="../assets/css/site.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

    <style>
        /* Style pour la section des participants */
        .participants-section {
            margin-top: 30px;
            background: var(--surface-color);
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
        }

        .participants-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .participant-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .participant-name {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .participant-email {
            font-size: 0.9em;
            color: #666;
            word-break: break-word;
        }

        .participants-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .participants-count {
            background: var(--primary-color);
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8em;
        }
        
        .event-creator-badge {
            display: inline-block;
            background-color: #e3f2fd;
            color: #0d6efd;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.8em;
            margin-left: 10px;
            font-weight: 500;
        }

        :root {
            --primary-color: #0A4D68;
{{ ... }}
            --secondary-color: #088395;
            --background-color: #f8f9fa;
            --surface-color: #ffffff;
            --text-color: #343a40;
            --light-text-color: #f8f9fa;
            --shadow: 0 4px 15px rgba(0,0,0,.07);
            --radius: 12px;
            --chip: #eef6f8;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: var(--background-color);
            color: var(--text-color);
        }

        header {
            background: var(--surface-color);
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        header .container {
            max-width: 1200px;
            margin: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
            padding: 0 20px;
        }

        .logo {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--primary-color);
            text-decoration: none;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 30px;
            margin: 0;
            padding: 0;
        }

        nav a {
            text-decoration: none;
            color: var(--text-color);
            font-weight: 500;
            position: relative;
            padding-bottom: 5px;
        }

        nav a::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            height: 2px;
            width: 0;
            background: var(--secondary-color);
            transition: width .3s ease;
        }

        nav a:hover { color: var(--primary-color); }
        nav a:hover::after { width: 100%; }
        nav a.active { color: var(--primary-color); }
        nav a.active::after { width: 100%; }

        main { padding: 50px 0; }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        h1 {
            text-align: center;
            color: var(--primary-color);
            font-size: 2rem;
            margin-bottom: 30px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
            padding: 0 20px;
        }

        .card {
            background: var(--surface-color);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .card-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .card-content {
            padding: 20px;
        }

        .card h3 {
            margin: 0 0 10px 0;
            color: var(--primary-color);
            font-size: 1.3rem;
        }

        .card p {
            color: #666;
            margin: 0 0 15px 0;
            line-height: 1.5;
        }

        .event-meta {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 15px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #555;
            font-size: 0.9rem;
        }

        .meta-item i {
            color: var(--secondary-color);
            width: 20px;
            text-align: center;
        }

        .btn-inscription {
            display: block;
            width: 100%;
            padding: 10px;
            background: var(--primary-color);
            color: white;
            text-align: center;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        .btn-inscription:hover {
            background: var(--secondary-color);
        }

        .no-events {
            text-align: center;
            grid-column: 1 / -1;
            padding: 40px 20px;
            color: #666;
        }

        footer {
            background: var(--primary-color);
            color: var(--light-text-color);
            text-align: center;
            padding: 30px 20px;
            margin-top: 50px;
        }
        /* Dropdown profil (minimal) */
        .profile-dropdown{position:relative;display:inline-block}
        .profile-icon{font-size:1.5rem;cursor:pointer;padding:5px}
        .profile-icon::after{display:none!important}
        .dropdown-content{display:none;position:absolute;background:#fff;min-width:220px;box-shadow:var(--shadow);border-radius:12px;padding:20px;right:0;top:100%;z-index:1001;text-align:center}
        .profile-dropdown:hover .dropdown-content{display:block}
        .dropdown-content a{display:block;padding:10px 15px;margin-bottom:8px;border-radius:5px;text-decoration:none;font-weight:500;color:#fff!important}
        .dropdown-content a::after{display:none}
        .profile-button{background:var(--secondary-color)}
        .profile-button:hover{background:var(--primary-color)}
        .logout-button{background:#e74c3c}
        .logout-button:hover{background:#c0392b}
    </style>
</head>
<body>
<header>
    <div class="container">
        <a href="../index.php" class="logo">École Sup.</a>
        <nav>
            <ul>
                <li><a href="../index.php">Accueil</a></li>
                <li><a href="formations.php">Formations</a></li>
                <li><a href="entreprise.php">Entreprises</a></li>
                <li><a href="offres.php">Offres</a></li>
                <li><a href="evenement.php" class="active">Événements</a></li>
                <li><a href="supportContact.php">Contact</a></li>

                <?php if (!empty($userLoggedIn)): ?>
                    <li><a href="forum.php">Forum</a></li>
                    <li class="profile-dropdown">
                        <a href="profilUser.php" class="profile-icon">👤</a>
                        <div class="dropdown-content">
                            <span>Bonjour, <?= htmlspecialchars($userLoggedIn->getPrenom()) ?> <?= htmlspecialchars($userLoggedIn->getNom()) ?> !</span>
                            <a href="profilUser.php" class="profile-button">Mon Profil</a>
                            <a href="?deco=true" class="logout-button">Déconnexion</a>
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

<?php if ($evenement): ?>
    <!-- Affichage d'un événement spécifique -->
    <main>
        <div class="container">
            <a href="evenement.php" class="back-link" style="display: inline-block; margin-bottom: 20px; color: var(--primary-color); text-decoration: none;">
                &larr; Retour aux événements
            </a>
            
            <article class="event-detail">
                <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap; justify-content: center; margin-bottom: 15px;">
    <h1 style="margin: 0;"><?= htmlspecialchars($evenement->getTitre()) ?></h1>
    <?php if ($userLoggedIn && $evenement->getRefUser() === $userLoggedIn->getIdUser()): ?>
        <span class="event-creator-badge" style="font-size: 1rem;">Créé par vous</span>
    <?php endif; ?>
</div>
                
                <div class="event-meta" style="margin: 20px 0; display: flex; gap: 20px; flex-wrap: wrap;">
                    <div class="meta-item" style="display: flex; align-items: center; gap: 5px;">
                        <i class="bi bi-calendar-event"></i>
                        <span><strong>Date :</strong> <?= (new DateTime($evenement->getDateEvent()))->format('d/m/Y H:i') ?></span>
                    </div>
                    
                    <?php if ($evenement->getLieu()): ?>
                    <div class="meta-item" style="display: flex; align-items: center; gap: 5px;">
                        <i class="bi bi-geo-alt"></i>
                        <span><strong>Lieu :</strong> <?= htmlspecialchars($evenement->getLieu()) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="meta-item" style="display: flex; align-items: center; gap: 5px;">
                        <i class="bi bi-tag"></i>
                        <span><strong>Type :</strong> <?= htmlspecialchars($evenement->getType()) ?></span>
                    </div>
                </div>
                
                <?php if ($evenement->getDescription()): ?>
                <div class="event-description" style="margin: 30px 0; line-height: 1.6;">
                    <h3>Description</h3>
                    <p><?= nl2br(htmlspecialchars($evenement->getDescription())) ?></p>
                </div>
                <?php endif; ?>
                
                <div class="event-actions" style="margin-top: 40px;">
                    <?php if (!empty($userLoggedIn)): ?>
                        <?php if ($estInscrit): ?>
                            <div class="alert alert-success" style="padding: 15px; background: #e8f5e9; border-left: 4px solid #4caf50; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <i class="bi bi-check-circle"></i> Vous êtes inscrit à cet événement
                                </div>
                                <form method="post" action="" style="margin: 0;">
                                    <input type="hidden" name="event_id" value="<?= $evenement->getIdEvent() ?>">
                                    <button type="submit" name="desinscrire_event" class="btn btn-sm" style="background: #f8d7da; color: #721c24; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; font-size: 0.9em;" onclick="return confirm('Êtes-vous sûr de vouloir vous désinscrire de cet événement ?')">
                                        <i class="bi bi-x-circle"></i> Se désinscrire
                                    </button>
                                </form>
                            </div>
                        <?php elseif (strtotime($evenement->getDateEvent()) > time()): ?>
                            <a href="./inscription_evenement.php?id=<?= $evenement->getIdEvent() ?>" class="btn-inscription" style="background: var(--primary-color); color: white; padding: 12px 20px; border-radius: 5px; text-decoration: none; display: inline-block; font-weight: 500;">
                                S'inscrire à cet événement
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="connexion.php?redirect=evenement.php?event_id=<?= $evenement->getIdEvent() ?>" class="btn-inscription" style="background: var(--primary-color); color: white; padding: 12px 20px; border-radius: 5px; text-decoration: none; display: inline-block; font-weight: 500;">
                            Se connecter pour s'inscrire
                        </a>
                    <?php endif; ?>
                </div>

                <?php if ($estCreateur): ?>
                    <div class="participants-section" style="margin: 20px 0; background: #fff; border-radius: 8px; padding: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); max-width: 600px;">
                        <div class="participants-header" style="margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #f0f0f0;">
                            <h3 style="margin: 0; color: #2c3e50; font-size: 1.2em; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                                <i class="bi bi-people-fill" style="color: var(--primary-color); font-size: 1.1em;"></i>
                                Participants
                                <span class="badge bg-primary" style="font-size: 0.75em; padding: 4px 8px; border-radius: 10px; font-weight: 500; margin-left: 5px;">
                                    <?= count($participants) ?>
                                </span>
                            </h3>
                        </div>
                        
                        <?php if (!empty($participants)): ?>
                            <div class="table-responsive" style="border-radius: 6px; overflow: hidden; border: 1px solid #eaeaea; font-size: 0.9em;">
                                <table class="table table-hover align-middle" style="margin: 0;">
                                    <thead style="background-color: #f8f9fa;">
                                        <tr>
                                            <th style="padding: 10px 15px; font-weight: 600; color: #495057; border-bottom: 1px solid #eaeaea;">Nom</th>
                                            <th style="padding: 10px 15px; font-weight: 600; color: #495057; border-bottom: 1px solid #eaeaea; width: 120px;">Rôle</th>
                                            <th style="padding: 10px 15px; font-weight: 600; color: #495057; border-bottom: 1px solid #eaeaea; width: 100px; text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($participants as $participant): ?>
                                            <tr style="border-bottom: 1px solid #f5f5f5;">
                                                <td style="padding: 12px 15px; color: #2c3e50;">
                                                    <?= htmlspecialchars($participant['prenom'] . ' ' . $participant['nom']) ?>
                                                </td>
                                                <td style="padding: 12px 15px;">
                                                    <?php 
                                                    $role = $participant['role'] ?? 'Utilisateur';
                                                    $role = str_replace(['CC', 'DC'], '', $role);
                                                    $role = trim($role) ?: 'Utilisateur';
                                                    ?>
                                                    <span class="badge" style="background-color: #e9ecef; color: #495057; font-size: 0.85em; padding: 4px 8px; border-radius: 4px; font-weight: 500;">
                                                        <?= htmlspecialchars($role) ?>
                                                    </span>
                                                </td>
                                                <td style="padding: 12px 15px; text-align: center;">
                                                    <?php if ($participant['id_user'] != $userLoggedIn->getIdUser()): ?>
                                                        <form method="post" class="d-inline" onsubmit="return confirm('Supprimer ce participant ?');">
                                                            <input type="hidden" name="participant_id" value="<?= $participant['id_user'] ?>">
                                                            <button type="submit" name="supprimer_participant" class="btn btn-sm btn-outline-danger" style="padding: 3px 8px; border-radius: 4px; font-size: 0.85em;" title="Supprimer">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="text-muted" style="font-size: 0.9em;">Vous</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info mb-0" style="background-color: #e7f5ff; border: 1px solid #d0ebff; color: #1864ab; padding: 10px; border-radius: 6px; font-size: 0.9em; margin: 0;">
                                <i class="bi bi-info-circle-fill" style="margin-right: 6px; font-size: 0.9em;"></i> Aucun participant
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
<?php else: ?>
    <!-- Liste de tous les événements -->
    <main>
        <div class="container">
            <h1>Nos Événements à Venir</h1>

            <div class="grid">
                <?php if (empty($evenements)): ?>
                    <div class="no-events">
                        <p>Aucun événement prévu pour le moment.</p>
                        <p>Revenez bientôt pour découvrir nos prochains événements !</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($evenements as $event): ?>
                        <article class="card">
                            <div class="card-content">
                                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
    <h3 style="margin: 0;"><?= htmlspecialchars($event->getTitre()) ?></h3>
    <?php if ($userLoggedIn && $event->getRefUser() === $userLoggedIn->getIdUser()): ?>
        <span class="event-creator-badge">Créé par vous</span>
    <?php endif; ?>
</div>

                                <div class="event-meta">
                                    <div class="meta-item">
                                        <i class="bi bi-calendar-event"></i>
                                        <span>Date: <?= (new DateTime($event->getDateEvent()))->format('d/m/Y H:i') ?></span>
                                    </div>
                                    <?php if ($event->getLieu()): ?>
                                        <div class="meta-item">
                                            <i class="bi bi-geo-alt"></i>
                                            <span><?= htmlspecialchars($event->getLieu()) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php 
                                    // Récupérer le statut des places disponibles
                                    $places = $inscriptionRepo->getPlacesDisponibles($event->getIdEvent());
                                    
                                    // Afficher le statut
                                    if ($places['statut'] === 'complet'): ?>
                                        <div class="meta-item" style="color: #dc3545; font-weight: 500;">
                                            <i class="bi bi-x-circle"></i>
                                            <span>Complet</span>
                                        </div>
                                    <?php elseif ($places['statut'] === 'bientot_complet'): ?>
                                        <div class="meta-item" style="color: #ffc107; font-weight: 500;">
                                            <i class="bi bi-exclamation-triangle"></i>
                                            <span>Bientôt complet ! (<?= $places['disponibles'] ?>)</span>
                                        </div>
                                    <?php else: ?>
                                        <div class="meta-item" style="color: #28a745; font-weight: 500;">
                                            <i class="bi bi-check-circle"></i>
                                            <span>Places disponibles : <?= $places['disponibles'] ?>/<?= $places['total'] ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if ($event->getDescription()): ?>
                                    <p style="margin: 15px 0;"><?= nl2br(htmlspecialchars(substr($event->getDescription(), 0, 150))) ?>...</p>
                                <?php endif; ?>

                                <div style="margin-top: 15px;">
                                    <?php 
                                    // Vérifier si l'utilisateur est connecté et déjà inscrit à cet événement
                                    $userInscrit = false;
                                    if (!empty($userLoggedIn)) {
                                        $userInscrit = $inscriptionRepo->estInscrit($userLoggedIn->getIdUser(), $event->getIdEvent());
                                    }
                                    
                                    if ($userInscrit): ?>
                                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; background: #e8f5e9; border-left: 4px solid #4caf50; margin-bottom: 10px; font-size: 0.9em; border-radius: 4px;">
                                            <div>
                                                <i class="bi bi-check-circle"></i> Inscrit
                                            </div>
                                            <?php if (isset($_GET['event_id'])): ?>
                                            <form method="post" action="" style="margin: 0;">
                                                <input type="hidden" name="event_id" value="<?= $event->getIdEvent() ?>">
                                                <button type="submit" name="desinscrire_event" class="btn btn-sm" style="background: #f8d7da; color: #721c24; border: none; padding: 3px 8px; border-radius: 3px; cursor: pointer; font-size: 0.8em;" onclick="return confirm('Êtes-vous sûr de vouloir vous désinscrire de cet événement ?')">
                                                    <i class="bi bi-x-circle"></i> Se désinscrire
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                                        <a href="evenement.php?event_id=<?= $event->getIdEvent() ?>" class="btn-inscription" style="background: #6c757d; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; display: inline-block; font-size: 0.9em; flex: 1; text-align: center;">
                                            Voir les détails
                                        </a>
                                        <?php 
                                        $peutSInscrire = !empty($userLoggedIn) && 
                                                       $userLoggedIn->getRole() !== 'admin' && 
                                                       !$userInscrit && 
                                                       strtotime($event->getDateEvent()) > time() && 
                                                       $places['disponibles'] > 0;
                                        
                                        if ($peutSInscrire): ?>
                                            <a href="inscription_evenement.php?id=<?= $event->getIdEvent() ?>" class="btn-inscription" style="background: var(--primary-color); color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; display: inline-block; font-size: 0.9em; flex: 1; text-align: center;">
                                                S'inscrire
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
<?php endif; ?>

<footer>
    &copy; <?= date('Y') ?> École Supérieure — Tous droits réservés
</footer>
<script src="../assets/js/site.js"></script>
</body>
</html>
