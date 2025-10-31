<?php
session_start();

require_once __DIR__ . '/../src/repository/UserRepo.php';
require_once __DIR__ . '/../src/modele/User.php';
require_once __DIR__ . '/../src/repository/InscriptionEventRepo.php';
require_once __DIR__ . '/../src/repository/EventRepo.php';

// Traitement de l'annulation de participation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['annuler_participation'])) {
    if (!isset($_SESSION['id_user'])) {
        header('Location: connexion.php');
        exit();
    }

    $idUtilisateur = $_SESSION['id_user'];
    $idEvenement = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);

    if ($idEvenement) {
        $inscriptionRepo = new \repository\InscriptionEventRepo();
        $success = $inscriptionRepo->annulerParticipation($idUtilisateur, $idEvenement);
        
        if ($success) {
            $message = "Votre participation a été annulée avec succès.";
            $messageClass = "success";
        } else {
            $message = "Une erreur est survenue lors de l'annulation de votre participation.";
            $messageClass = "error";
        }
    }
}

require_once __DIR__ . '/../src/repository/EventRepo.php';
require_once __DIR__ . '/../src/repository/InscriptionEventRepo.php';

use repository\UserRepo;
use repository\EventRepo;
use repository\InscriptionEventRepo;
use modele\User;

// Vérifie la connexion
if (!isset($_SESSION['id_user'])) {
    header('Location: connexion.php');
    exit;
}

// Récupération utilisateur et ses réservations
$userRepo = new UserRepo();
$user = $userRepo->getUserById($_SESSION['id_user']);

// Récupération des réservations de l'utilisateur
$inscriptionRepo = new InscriptionEventRepo();
$eventRepo = new EventRepo();
$reservations = $inscriptionRepo->getReservationsByUser($_SESSION['id_user']);

// Mise à jour profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $email = $_POST['email'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $password = $_POST['password'] ?? '';

    $user->setEmail($email);
    $user->setNom($nom);
    $user->setPrenom($prenom);

    if ($password) {
        $user->setMdp(password_hash($password, PASSWORD_DEFAULT));
    }

    $userRepo->modifUser($user);
    $successMessage = "✅ Votre profil a été mis à jour avec succès.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - École Sup.</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color:#0A4D68;
            --secondary-color:#088395;
            --background-color:#f8f9fa;
            --surface-color:#ffffff;
            --text-color:#343a40;
            --light-text-color:#f8f9fa;
            --shadow:0 4px 15px rgba(0,0,0,0.07);
            --radius:12px;
        }

        body {
            margin:0;
            font-family:'Poppins',sans-serif;
            background:var(--background-color);
            color:var(--text-color);
        }

        header {
            background:var(--surface-color);
            box-shadow:var(--shadow);
            position:sticky;
            top:0;
            z-index:1000;
        }

        header .container {
            max-width:1200px;
            margin:auto;
            display:flex;
            justify-content:space-between;
            align-items:center;
            height:70px;
            padding:0 20px;
        }

        .logo {
            font-size:1.6rem;
            font-weight:700;
            color:var(--primary-color);
            text-decoration:none;
        }

        nav ul {
            list-style:none;
            display:flex;
            gap:30px;
            margin:0;
            padding:0;
        }

        nav a {
            text-decoration:none;
            color:var(--text-color);
            font-weight:500;
            position:relative;
            padding-bottom:5px;
            transition:color .3s ease;
        }

        nav a::after {
            content:'';
            position:absolute;
            left:0;
            bottom:0;
            width:0;
            height:2px;
            background:var(--secondary-color);
            transition:width .3s ease;
        }

        nav a:hover, nav a.active { color:var(--primary-color); }
        nav a:hover::after, nav a.active::after { width:100%; }

        /* ------- PAGE PROFIL ------- */
        main {
            display:flex;
            justify-content:center;
            align-items:flex-start;
            padding:60px 20px;
            min-height:calc(100vh - 120px);
        }

        .profil-card {
            background:var(--surface-color);
            border-radius:var(--radius);
            box-shadow:var(--shadow);
            padding:40px 50px;
            width:100%;
            max-width:550px;
            animation:fadeIn .6s ease;
        }

        @keyframes fadeIn {
            from {opacity:0; transform:translateY(20px);}
            to {opacity:1; transform:translateY(0);}
        }

        h1 {
            text-align:center;
            color:var(--primary-color);
            font-size:1.8rem;
            margin-bottom:25px;
        }

        .success {
            background:#eafaf1;
            border:1px solid #27ae60;
            color:#2e7d32;
            padding:12px;
            border-radius:var(--radius);
            text-align:center;
            margin-bottom:20px;
            font-weight:600;
        }

        form {
            display:flex;
            flex-direction:column;
            gap:15px;
        }

        label {
            font-weight:600;
            color:var(--primary-color);
        }

        input {
            padding:10px 12px;
            border:1px solid #ccc;
            border-radius:var(--radius);
            font-size:1rem;
            transition:border .2s ease, box-shadow .2s ease;
        }

        input:focus {
            outline:none;
            border-color:var(--secondary-color);
            box-shadow:0 0 0 2px rgba(8,131,149,0.15);
        }

        .role {
            background:var(--background-color);
            padding:10px;
            border-radius:var(--radius);
            text-align:center;
            font-weight:500;
            color:#555;
            margin-top:10px;
        }

        button {
            margin-top:15px;
            background:var(--secondary-color);
            border:none;
            color:white;
            padding:12px;
            border-radius:var(--radius);
            font-weight:600;
            font-size:1rem;
            cursor:pointer;
            transition:background .2s ease, transform .1s ease;
        }

        button:hover {
            background:var(--primary-color);
            transform:translateY(-2px);
        }

        a.back {
            display:inline-block;
            text-align:center;
            margin-top:25px;
            color:var(--secondary-color);
            text-decoration:none;
            font-weight:500;
            transition:color .2s ease;
        }

        a.back:hover { color:var(--primary-color); }

        footer {
            text-align:center;
            padding:30px;
            background:var(--primary-color);
            color:var(--light-text-color);
            font-size:0.9rem;
        }

        @media (max-width:600px){
            .profil-card {
                padding:30px 25px;
            }
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
                <li><a href="entreprises.php">Entreprises</a></li>
                <li><a href="offres.php">Offres</a></li>
                <li><a href="supportContact.php">Contact</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <div class="profil-card">
        <h1>👤 Mon Profil</h1>

        <?php if (isset($successMessage)): ?>
            <div class="success"><?= htmlspecialchars($successMessage) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="update_profile" value="1">

            <label>Nom :</label>
            <input type="text" name="nom" required value="<?= htmlspecialchars($user->getNom()) ?>">

            <label>Prénom :</label>
            <input type="text" name="prenom" required value="<?= htmlspecialchars($user->getPrenom()) ?>">

            <label>Email :</label>
            <input type="email" name="email" required value="<?= htmlspecialchars($user->getEmail()) ?>">

            <label>Mot de passe (laisser vide si inchangé) :</label>
            <input type="password" name="password" placeholder="••••••••••">

            <div class="role">
                Rôle : <strong><?= htmlspecialchars($user->getRole()) ?></strong> (non modifiable)
            </div>

            <button type="submit">💾 Mettre à jour</button>
        </form>

        <?php if (isset($message)): ?>
            <div class="alert alert-<?= $messageClass === 'success' ? 'success' : 'danger' ?>" 
                 style="padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; 
                        color: <?= $messageClass === 'success' ? '#155724' : '#721c24' ?>; 
                        background-color: <?= $messageClass === 'success' ? '#d4edda' : '#f8d7da' ?>; 
                        border-color: <?= $messageClass === 'success' ? '#c3e6cb' : '#f5c6cb' ?>;">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <a href="../index.php" class="back">⬅ Retour à l'accueil</a>

        <!-- Section des réservations d'événements -->
        <div class="reservations-section" style="margin-top: 50px;">
            <h2 style="color: var(--primary-color); margin-bottom: 20px;">📅 Mes réservations d'événements</h2>
            
            <?php if (empty($reservations)): ?>
                <p>Vous n'avez pas encore réservé d'événement.</p>
                <a href="evenement.php" class="btn" style="display: inline-block; margin-top: 10px;">Voir les événements</a>
            <?php else: ?>
                <div class="reservations-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-top: 20px;">
                    <?php foreach ($reservations as $event): 
                        $dateEvent = new DateTime($event['date_event']);
                        $dateInscription = new DateTime($event['date_inscription']);
                    ?>
                        <div class="reservation-card" style="background: white; border-radius: var(--radius); padding: 20px; box-shadow: var(--shadow);">
                            <h3 style="margin-top: 0; color: var(--primary-color);"><?= htmlspecialchars($event['titre']) ?></h3>
                            <p><strong>Type :</strong> <?= htmlspecialchars($event['type']) ?></p>
                            <p><strong>Lieu :</strong> <?= htmlspecialchars($event['lieu']) ?></p>
                            <p><strong>Date de l'événement :</strong> <?= $dateEvent->format('d/m/Y H:i') ?></p>
                            <p><strong>Date d'inscription :</strong> <?= $dateInscription->format('d/m/Y H:i') ?></p>
                            
                            <?php 
                            // Récupérer le statut des places disponibles
                            $places = $inscriptionRepo->getPlacesDisponibles($event['id_evenement']);
                            
                            // Afficher le statut
                            if ($places['statut'] === 'complet'): ?>
                                <div style="background-color: #f8d7da; color: #721c24; padding: 5px 10px; border-radius: 4px; display: inline-block; margin: 5px 0;">
                                    <i class="bi bi-exclamation-triangle"></i> Complet
                                </div>
                            <?php elseif ($places['statut'] === 'bientot_complet'): ?>
                                <div style="background-color: #fff3cd; color: #856404; padding: 5px 10px; border-radius: 4px; display: inline-block; margin: 5px 0;">
                                    <i class="bi bi-exclamation-triangle"></i> Bientôt complet ! (<?= $places['disponibles'] ?> place<?= $places['disponibles'] > 1 ? 's' : '' ?> restante<?= $places['disponibles'] > 1 ? 's' : '' ?>)
                                </div>
                            <?php else: ?>
                                <div style="background-color: #d4edda; color: #155724; padding: 5px 10px; border-radius: 4px; display: inline-block; margin: 5px 0;">
                                    <i class="bi bi-check-circle"></i> Places disponibles : <?= $places['disponibles'] ?>/<?= $places['total'] ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (strtotime($event['date_event']) > time()): ?>
                                <p style="color: var(--success-color); font-weight: 500;">
                                    <i class="bi bi-check-circle"></i> Réservation confirmée
                                </p>
                            <?php else: ?>
                                <p style="color: #666; font-style: italic;">
                                    <i class="bi bi-calendar-check"></i> Événement terminé
                                </p>
                            <?php endif; ?>
                            
                            <div style="margin-top: 15px; display: flex; gap: 10px;">
                                <a href="evenement.php?event_id=<?= $event['id_evenement'] ?>" class="btn" style="flex: 1; text-align: center;">
                                    Voir l'événement
                                </a>
                                <?php if (strtotime($event['date_event']) > time()): ?>
                                <form method="post" action="" style="flex: 1;">
                                    <input type="hidden" name="event_id" value="<?= $event['id_evenement'] ?>">
                                    <button type="submit" name="annuler_participation" class="btn" 
                                            style="background-color: #dc3545; border-color: #dc3545; width: 100%;"
                                            onclick="return confirm('Êtes-vous sûr de vouloir annuler votre participation à cet événement ?');">
                                        Annuler
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

</body>
</html>
