<?php

require_once __DIR__ . '/../src/repository/EventRepo.php';
require_once __DIR__ . '/../src/modele/Event.php';
require_once __DIR__ . '/../src/repository/InscriptionEventRepo.php';

use repository\EventRepo;
use repository\InscriptionEventRepo;

session_start();

$evenementRepo = new EventRepo();
$inscriptionRepo = new InscriptionEventRepo();

// Vérifier si on affiche un événement spécifique
$evenement = null;
if (isset($_GET['event_id']) && is_numeric($_GET['event_id'])) {
    $evenement = $evenementRepo->getEvenementById($_GET['event_id']);
    if (!$evenement) {
        header('Location: evenement.php');
        exit();
    }
} else {
    // Si pas d'ID spécifique, récupérer tous les événements
    $evenements = $evenementRepo->getTousLesEvenements();
}

// Récupérer l'utilisateur connecté (pour le header)
$userLoggedIn = null;
$estInscrit = false;
if (!empty($_SESSION['connexion']) && $_SESSION['connexion'] === true && !empty($_SESSION['id_user'])) {
    require_once __DIR__ . '/../src/repository/UserRepo.php';
    $userRepo = new \repository\UserRepo();
    $userLoggedIn = $userRepo->getUserById($_SESSION['id_user']);
    
    // Vérifier si l'utilisateur est déjà inscrit à l'événement
    if ($evenement) {
        $estInscrit = $inscriptionRepo->estInscrit($userLoggedIn->getIdUser(), $evenement->getIdEvent());
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements - École Sup.</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-color: #0A4D68;
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
                    <li><a href="?deco=true">Déconnexion</a></li>
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
                <h1><?= htmlspecialchars($evenement->getTitre()) ?></h1>
                
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
                            <div class="alert alert-success" style="padding: 15px; background: #e8f5e9; border-left: 4px solid #4caf50; margin-bottom: 20px;">
                                <i class="bi bi-check-circle"></i> Vous êtes inscrit à cet événement
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
            </article>
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
                                <h3><?= htmlspecialchars($event->getTitre()) ?></h3>

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
                                        <div class="alert alert-success" style="padding: 8px 12px; background: #e8f5e9; border-left: 4px solid #4caf50; margin-bottom: 10px; font-size: 0.9em;">
                                            <i class="bi bi-check-circle"></i> Inscrit
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div style="display: flex; gap: 10px; margin-top: 10px;">
                                        <a href="evenement.php?event_id=<?= $event->getIdEvent() ?>" class="btn-inscription" style="background: #6c757d; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; display: inline-block; font-size: 0.9em; flex: 1; text-align: center;">
                                            Voir les détails
                                        </a>
                                        <?php 
                                        $peutSInscrire = !empty($userLoggedIn) && 
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
</body>
</html>
