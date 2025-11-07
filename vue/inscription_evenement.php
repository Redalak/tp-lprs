<?php
require_once __DIR__ . '/../src/bootstrap.php';
require_once __DIR__ . '/../src/repository/EventRepo.php';
require_once __DIR__ . '/../src/repository/InscriptionEventRepo.php';

use repository\EventRepo;
use repository\InscriptionEventRepo;

session_start();

// Vérifier si l'utilisateur est connecté
if (empty($_SESSION['connexion']) || $_SESSION['connexion'] !== true || empty($_SESSION['id_user'])) {
    header('Location: connexion.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$idEvent = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$userId = $_SESSION['id_user'];
$message = '';
$success = false;

if (!$idEvent) {
    $message = "ID d'événement invalide.";
} else {
    $eventRepo = new EventRepo();
    $inscriptionRepo = new InscriptionEventRepo();
    
    // Logs de débogage
    error_log("Tentative d'inscription - User ID: $userId, Event ID: $idEvent");
    
    // Vérifier si l'utilisateur est déjà inscrit
    $dejaInscrit = $inscriptionRepo->estInscrit($userId, $idEvent);
    error_log("Déjà inscrit: " . ($dejaInscrit ? 'Oui' : 'Non'));
    
    if ($dejaInscrit) {
        $message = "Vous êtes déjà inscrit à cet événement.";
    } else {
        // Vérifier si l'événement existe et a des places disponibles
        $event = $eventRepo->getEvenementById($idEvent);
        if (!$event) {
            $message = "Événement introuvable.";
            error_log("Événement introuvable: $idEvent");
        } elseif ($event->getNombrePlace() <= 0) {
            $message = "Désolé, il n'y a plus de places disponibles pour cet événement.";
            error_log("Plus de places disponibles pour l'événement: $idEvent");
        } else {
            // Inscrire l'utilisateur à l'événement
            $resultatInscription = $inscriptionRepo->inscrireUtilisateur($userId, $idEvent);
            error_log("Résultat de l'inscription: " . ($resultatInscription ? 'Succès' : 'Échec'));
            
            if ($resultatInscription) {
                $message = "Inscription réussie !";
                $success = true;
            } else {
                $message = "Une erreur est survenue lors de l'inscription. Veuillez réessayer.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription à l'événement - École Sup.</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Shared site styles -->
    <link href="../assets/css/site.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #0A4D68;
            --secondary-color: #088395;
            --success-color: #28a745;
            --error-color: #dc3545;
            --background-color: #f8f9fa;
            --surface-color: #ffffff;
            --text-color: #343a40;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: var(--background-color);
            color: var(--text-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background: var(--surface-color);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 600px;
            width: 100%;
        }

        .icon {
            font-size: 60px;
            margin-bottom: 20px;
        }

        .success { color: var(--success-color); }
        .error { color: var(--error-color); }

        h1 {
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
            transition: background 0.3s;
        }

        .btn:hover {
            background: var(--secondary-color);
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($success): ?>
            <div class="icon success">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <h1>Inscription réussie !</h1>
            <p><?= htmlspecialchars($message) ?></p>
            <p>Vous recevrez un email de confirmation avec les détails de l'événement.</p>
        <?php else: ?>
            <div class="icon error">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <h1>Erreur d'inscription</h1>
            <p><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        
        <a href="evenement.php" class="btn">Retour aux événements</a>
    </div>
<script src="../assets/js/site.js"></script>
</body>
</html>
