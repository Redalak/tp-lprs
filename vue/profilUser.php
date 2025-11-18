<?php
declare(strict_types=1);

// Définir le titre de la page
$pageTitle = 'Profil Utilisateur';

// Inclure l'en-tête qui gère la session et l'authentification
require_once __DIR__ . '/../includes/header.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header('Location: connexion.php');
    exit;
}

// Inclure les dépendances nécessaires
require_once __DIR__ . '/../src/modele/User.php';
require_once __DIR__ . '/../src/modele/Event.php';
require_once __DIR__ . '/../src/modele/Entreprise.php';
require_once __DIR__ . '/../src/repository/UserRepo.php';
require_once __DIR__ . '/../src/repository/EventRepo.php';
require_once __DIR__ . '/../src/repository/InscriptionEventRepo.php';
require_once __DIR__ . '/../src/repository/EntrepriseRepo.php';
require_once __DIR__ . '/../src/repository/UserEntrepriseRepo.php';

use repository\UserRepo;
use repository\EventRepo;
use repository\InscriptionEventRepo;
use repository\UserEntrepriseRepo;
use modele\User;
use modele\Event;

$userId = $_SESSION['id_user'];
$userRepo = new UserRepo();
$eventRepo = new EventRepo();
$inscriptionRepo = new InscriptionEventRepo();
$user = $userRepo->getUserById($userId);

// Initialisation du repository UserEntrepriseRepo
try {
    $userEntrepriseRepo = new UserEntrepriseRepo();
} catch (\Exception $e) {
    error_log("Erreur lors de l'initialisation de UserEntrepriseRepo: " . $e->getMessage());
    $userEntrepriseRepo = null;
}

// Gestion de l'entreprise
$userEntreprise = null;
$allEntreprises = [];
$showEntrepriseForm = false;

if ($userEntrepriseRepo) {
    try {
        $userEntreprise = $userEntrepriseRepo->getUserEntreprise($userId);
        $allEntreprises = $userEntrepriseRepo->getAllEntreprises();
        $showEntrepriseForm = !$userEntreprise && ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['submit_entreprise']));
    } catch (\Exception $e) {
        error_log("Erreur lors de la récupération des données d'entreprise: " . $e->getMessage());
        $message = "Une erreur est survenue lors du chargement des données d'entreprise.";
        $messageClass = "error";
    }
}

// Messages de retour
$message = $_SESSION['message'] ?? '';
$messageClass = $_SESSION['messageClass'] ?? '';
// Effacer les messages après les avoir affichés
unset($_SESSION['message']);
unset($_SESSION['messageClass']);

// Traitement des actions sur l'entreprise
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $entrepriseRepo = new \repository\EntrepriseRepo();
    
    try {
        if ($_POST['action'] === 'link_entreprise' && !$userEntreprise) {
            // Vérifier si une entreprise existante est sélectionnée
            if (!empty($_POST['entreprise_id']) && $_POST['entreprise_id'] !== '') {
                // Lier à une entreprise existante
                $entrepriseId = filter_input(INPUT_POST, 'entreprise_id', FILTER_VALIDATE_INT);
                if ($entrepriseId) {
                    $userEntrepriseRepo->linkUserToEntreprise($userId, $entrepriseId);
                    $message = "L'entreprise a été liée à votre compte avec succès.";
                    $messageClass = "success";
                    // Recharger les données de l'entreprise
                    $userEntreprise = $userEntrepriseRepo->getUserEntreprise($userId);
                    
                    // Mettre à jour la variable pour masquer le formulaire
                    $showEntrepriseForm = false;
                }
            } elseif (isset($_POST['new_nom']) && !empty(trim($_POST['new_nom']))) {
                // Créer une nouvelle entreprise
                $nom = trim($_POST['new_nom']);
                $motifPartenariat = !empty(trim($_POST['new_motif_partenariat'])) 
                    ? trim($_POST['new_motif_partenariat']) 
                    : null;

                if (empty($nom)) {
                    throw new \Exception("Le nom de l'entreprise est obligatoire.");
                }

                $data = [
                    'nom' => $nom,
                    'adresse' => !empty(trim($_POST['new_adresse'])) ? trim($_POST['new_adresse']) : null,
                    'site_web' => !empty(trim($_POST['new_site_web'])) ? trim($_POST['new_site_web']) : null,
                    'motif_partenariat' => $motifPartenariat,
                    'date_inscription' => date('Y-m-d'),
                    'ref_offre' => null
                ];
                
                $entreprise = new \modele\Entreprise($data);
                $entreprise = $entrepriseRepo->ajoutEntreprise($entreprise);
                
                // Lier l'utilisateur à la nouvelle entreprise
                $userEntrepriseRepo->linkUserToEntreprise($userId, $entreprise->getIdEntreprise());
                
                $message = "L'entreprise a été créée et liée à votre compte avec succès.";
                $messageClass = "success";
                // Recharger les données de l'entreprise
                $userEntreprise = $userEntrepriseRepo->getUserEntreprise($userId);
                $showEntrepriseForm = false;
            } else {
                // Si on arrive ici, c'est qu'aucune option valide n'a été sélectionnée
                if (empty($_POST['entreprise_id']) && empty(trim($_POST['new_nom'] ?? ''))) {
                    throw new \Exception("Veuillez sélectionner une entreprise existante ou remplir le formulaire pour en créer une nouvelle.");
                }
            }
        } 
        elseif ($_POST['action'] === 'delete_entreprise' && $userEntreprise) {
            // Supprimer le lien avec l'entreprise
            $userEntrepriseRepo->unlinkUserFromEntreprise($userId, $userEntreprise['id_entreprise']);
            $message = "Le lien avec l'entreprise a été supprimé avec succès.";
            $messageClass = "success";
            $userEntreprise = null;
        }
    } catch (\Exception $e) {
        $message = $e->getMessage();
        $messageClass = "error";
    }
    
    // Stocker le message dans la session pour le réafficher après redirection
    $_SESSION['message'] = $message;
    $_SESSION['messageClass'] = $messageClass;
    
    // Rediriger pour éviter la soumission multiple du formulaire
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Traitement des actions sur les événements
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Annulation de participation
    if (isset($_POST['annuler_participation'])) {
        $idEvenement = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
        if ($idEvenement) {
            $success = $inscriptionRepo->annulerParticipation($userId, $idEvenement);
            if ($success) {
                $message = "Votre participation a été annulée avec succès.";
                $messageClass = "success";
            } else {
                $message = "Une erreur est survenue lors de l'annulation de votre participation.";
                $messageClass = "error";
            }
        }
    }
    // Suppression d'un événement
    elseif (isset($_POST['supprimer_evenement'])) {
        $eventId = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
        if ($eventId && $eventRepo->evenementAppartientA($eventId, $userId)) {
            $eventRepo->suppEvent($eventId);
            $message = "L'événement a été supprimé avec succès.";
            $messageClass = "success";
        } else {
            $message = "Action non autorisée ou événement introuvable.";
            $messageClass = "error";
        }
    }
    // Création/Modification d'un événement
    elseif (isset($_POST['sauvegarder_evenement'])) {
        $eventId = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
        
        // Vérification des droits pour la modification
        if ($eventId && !$eventRepo->evenementAppartientA($eventId, $userId)) {
            $message = "Action non autorisée.";
            $messageClass = "error";
        } else {
            try {
                // Récupération des données du formulaire (clés alignées avec le modèle Event)
                $eventData = [
                    'type'        => $_POST['type'] ?? '',
                    'titre'       => $_POST['titre'] ?? '',
                    'description' => $_POST['description'] ?? '',
                    'lieu'        => $_POST['lieu'] ?? '',
                    'nombrePlace' => (int)($_POST['nombre_place'] ?? 0),
                    'dateEvent'   => $_POST['date_event'] ?? '',
                    'etat'        => 'publie', // créé depuis profil => publié
                    'ref_user'    => $userId
                ];

                // Normaliser la date HTML5 ("YYYY-MM-DDTHH:MM") vers MySQL ("YYYY-MM-DD HH:MM:SS")
                if ($eventData['dateEvent'] !== '') {
                    $dt = str_replace('T', ' ', $eventData['dateEvent']);
                    // Ajouter ":00" si les secondes manquent
                    if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $dt)) {
                        $dt .= ':00';
                    }
                    $eventData['dateEvent'] = $dt;
                }

                // Validation basique
                $missingFields = [];
                if ($eventData['titre'] === '') $missingFields[] = 'titre';
                if ($eventData['type'] === '') $missingFields[] = 'type';
                if ($eventData['dateEvent'] === '') $missingFields[] = 'date_event';
                if ($eventData['lieu'] === '') $missingFields[] = 'lieu';
                if ($eventData['nombrePlace'] <= 0) $missingFields[] = 'nombre_place (> 0)';
                if (!empty($missingFields)) {
                    throw new \Exception("Les champs suivants sont obligatoires : " . implode(', ', $missingFields));
                }

                if ($eventId) {
                    // Mise à jour
                    $event = new Event($eventData + ['idEvent' => $eventId]);
                    $eventRepo->modifEvent($event);
                    $message = "L'événement a été mis à jour avec succès.";
                } else {
                    // Création
                    $event = new Event($eventData);
                    $eventRepo->ajoutEvent($event);
                    $message = "L'événement a été créé avec succès.";
                }
                $messageClass = 'success';

            } catch (\Exception $e) {
                $message = "Erreur lors de la sauvegarde de l'événement : " . $e->getMessage();
                $messageClass = 'error';
                error_log("ERREUR CRITIQUE: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                error_log("=== FIN TRACE ERREUR ===");
            }
        }
    }
}

// Récupération des événements de l'utilisateur
$evenementsUtilisateur = $eventRepo->getEvenementsParUtilisateur($userId);

// Récupération des réservations de l'utilisateur
$reservations = $inscriptionRepo->getReservationsByUser($userId);

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

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Shared site styles -->
    <link href="../assets/css/site.css" rel="stylesheet">
    
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
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin: 20px auto;
            max-width: 1000px;
        }
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

        button[type="submit"] {
            margin-top: 25px;
            background: var(--secondary-color);
            border: none;
            color: white;
            padding: 12px 20px;
            border-radius: var(--radius);
            cursor: pointer;
            font-weight: 500;
            font-size: 1rem;
            transition: background .2s ease, transform .1s ease;
        }
        
        button[type="submit"]:hover {
            background-color: #0a4d68;
            transform: translateY(-1px);
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
                <li><a href="entreprise.php">Entreprises</a></li>
                <li><a href="offres.php">Offres</a></li>
                <li><a href="supportContact.php">Contact</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <div class="profil-card">
        <?php if ($user->getRole() === 'alumni'): ?>
            <h1>👨‍🎓 Profil Alumni</h1>
        <?php else: ?>
            <h1>👤 Mon Profil</h1>
        <?php endif; ?>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $messageClass === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert" style="margin-bottom: 20px;">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($successMessage)): ?>
            <div class="success"><?= htmlspecialchars($successMessage) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="update_profile" value="1">

            <label>Nom :</label>
            <input type="text" name="nom" value="<?= htmlspecialchars($user->getNom()) ?>" readonly>

            <label>Prénom :</label>
            <input type="text" name="prenom" value="<?= htmlspecialchars($user->getPrenom()) ?>" readonly>

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

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $messageClass === 'success' ? 'success' : 'danger' ?>" 
                 style="padding: 15px; margin: 20px 0; border-radius: 4px;">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Section Gestion de l'entreprise -->
        <section class="entreprise-section mt-5">
            <h2 class="mb-4">
                <i class="bi bi-building"></i> Mon entreprise
            </h2>
            
            <?php if ($userEntreprise): ?>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($userEntreprise['nom']) ?></h5>
                        <p class="card-text">
                            <strong>Adresse :</strong> <?= htmlspecialchars($userEntreprise['adresse']) ?><br>
                            <strong>Site web :</strong> 
                            <?php if ($userEntreprise['site_web']): ?>
                                <a href="<?= htmlspecialchars($userEntreprise['site_web']) ?>" target="_blank">
                                    <?= htmlspecialchars($userEntreprise['site_web']) ?>
                                </a>
                            <?php else: ?>
                                Non renseigné
                            <?php endif; ?>
                        </p>
                        <form method="POST" style="display: inline-block;">
                            <input type="hidden" name="action" value="delete_entreprise">
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer le lien avec cette entreprise ?')">
                                <i class="bi bi-trash"></i> Supprimer le lien
                            </button>
                        </form>
                    </div>
                </div>
            <?php elseif ($showEntrepriseForm): ?>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Lier ou créer une entreprise</h5>
                        <form method="POST" id="entrepriseForm">
                            <input type="hidden" name="action" value="link_entreprise">
                            
                            <div class="mb-3">
                                <label class="form-label">Choisir une entreprise existante :</label>
                                <select name="entreprise_id" id="entreprise_id" class="form-select mb-3" onchange="toggleEntrepriseFields()">
                                    <option value="">Sélectionner une entreprise...</option>
                                    <?php foreach ($allEntreprises as $entreprise): ?>
                                        <option value="<?= $entreprise['id_entreprise'] ?>">
                                            <?= htmlspecialchars($entreprise['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="text-center mb-3">
                                    <strong>OU</strong>
                                </div>
                            </div>
                            
                            <h6>Créer une nouvelle entreprise :</h6>
                            <div id="newEntrepriseFields">
                                <div class="mb-3">
                                    <label class="form-label">Nom de l'entreprise *</label>
                                    <input type="text" name="new_nom" id="new_nom" class="form-control">
                                </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Adresse</label>
                                <input type="text" name="new_adresse" class="form-control">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Site web</label>
                                <input type="url" name="new_site_web" class="form-control">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Motif du partenariat *</label>
                                <textarea name="new_motif_partenariat" class="form-control" required></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Enregistrer
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <!-- Section des événements créés par l'utilisateur -->
        <?php if ($user->getRole() === 'alumni'): ?>
            <section class="alumni-section mt-5">
                <h2 class="mb-4">
                    <i class="bi bi-award"></i> Espace Alumni
                </h2>
                <div class="alumni-content">
                    <div class="alert alert-info">
                        <h4>Bienvenue dans votre espace Alumni</h4>
                        <p>En tant qu'ancien élève, bénéficiez d'avantages exclusifs :</p>
                        <ul>
                            <li>Accès aux événements réservés aux anciens élèves</li>
                            <li>Réseautage avec d'autres diplômés</n                            <li>Accès aux offres d'emploi du réseau alumni</li>
                            <li>Invitations aux conférences et ateliers</li>
                        </ul>
                    </div>
                    <!-- Vous pouvez ajouter plus de contenu spécifique aux alumni ici -->
                </div>
            </section>
        <?php endif; ?>

        <section class="mes-evenements mt-5">
            <h2 class="mb-4">
                <i class="bi bi-calendar-event"></i> 
                <?= $user->getRole() === 'alumni' ? 'Événements pour les anciens élèves' : 'Mes événements' ?>
            </h2>
            
            <?php if (empty($evenementsUtilisateur)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Vous n'avez pas encore créé d'événement.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Lieu</th>
                                <th>Places</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($evenementsUtilisateur as $event): 
                                $dateEvent = new DateTime($event->getDateEvent());
                                $now = new DateTime();
                                $isPastEvent = $dateEvent < $now;
                            ?>
                            <tr class="<?= $isPastEvent ? 'table-secondary' : '' ?>">
                                <td><?= htmlspecialchars($event->getTitre()) ?></td>
                                <td><?= htmlspecialchars($event->getType()) ?></td>
                                <td><?= $dateEvent->format('d/m/Y H:i') ?></td>
                                <td><?= htmlspecialchars($event->getLieu()) ?></td>
                                <td><?= $event->getNombrePlace() ?></td>
                                <td>
                                    <?php if ($isPastEvent): ?>
                                        <span class="badge bg-secondary">Terminé</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">À venir</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="modifUtilisateurEvent.php?id=<?= $event->getRefUser() ?>" 
                                           class="btn btn-outline-primary"
                                           title="Modifier l'événement">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="post" class="d-inline" 
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?');">
                                            <input type="hidden" name="event_id" value="<?= $event->getIdEvent() ?>">
                                            <button type="submit" name="supprimer_evenement" class="btn btn-outline-danger">
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
        </section>

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

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Formulaire de création d'événement -->
    <section class="mt-5">
        <h2>Créer un nouvel événement</h2>
        
        <form method="post" action="profilUser.php" class="form-container">
            <input type="hidden" name="ref_user" value="<?= htmlspecialchars((string)$userId) ?>">
            <input type="hidden" name="etat" value="publie">
            <input type="hidden" name="sauvegarder_evenement" value="1">
            
            <div class="row">
                <div class="col">
                    <div class="form-group mb-3">
                        <label for="titre">Titre :</label>
                        <input type="text" id="titre" name="titre" class="form-control" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="type">Type :</label>
                        <select id="type" name="type" class="form-control" required>
                            <option value="">Sélectionnez un type</option>
                            <option value="conférence">Conférence</option>
                            <option value="atelier">Atelier</option>
                            <option value="séminaire">Séminaire</option>
                            <option value="formation">Formation</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="lieu">Lieu :</label>
                        <input type="text" id="lieu" name="lieu" class="form-control" required>
                    </div>
                </div>
                
                <div class="col">
                    <div class="form-group mb-3">
                        <label for="date_event">Date et heure :</label>
                        <input type="datetime-local" id="date_event" name="date_event" class="form-control" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="nombre_place">Nombre de places :</label>
                        <input type="number" id="nombre_place" name="nombre_place" class="form-control" min="1" required>
                    </div>
                    
                    <!-- État déjà défini en haut du formulaire -->
                </div>
            </div>
            
            <div class="form-group mb-3">
                <label for="description">Description :</label>
                <textarea id="description" name="description" class="form-control" rows="4" required></textarea>
            </div>
            
            <div class="form-actions mt-4">
                <button type="submit" name="sauvegarder_evenement" class="btn btn-primary">
                    <i class="bi bi-calendar-plus"></i> Créer l'événement
                </button>
                <button type="reset" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Réinitialiser
                </button>
            </div>
        </form>
    </section>

    <script>
        // Afficher le formulaire de création d'événement
        function afficherFormulaireEvenement() {
            const form = document.getElementById('eventForm');
            form.reset();
            form.classList.remove('was-validated');
            document.getElementById('modalTitle').textContent = 'Nouvel événement';
            document.getElementById('eventId').value = '';

            // Définir la date et l'heure minimales à maintenant
            const now = new Date();
            const timezoneOffset = now.getTimezoneOffset() * 60000; // en millisecondes
            const localISOTime = (new Date(now - timezoneOffset)).toISOString().slice(0, 16);
            
            const dateInput = document.getElementById('date_event');
            dateInput.min = localISOTime;
            dateInput.value = localISOTime;
            
            // Afficher la modal
            const modal = new bootstrap.Modal(document.getElementById('eventModal'));
            modal.show();
        }
        
        // Remplir le formulaire avec les données d'un événement existant
        function editerEvenement(event) {
            const form = document.getElementById('eventForm');
            form.reset();
            form.classList.remove('was-validated');
            
            document.getElementById('modalTitle').textContent = "Modifier l'événement";
            document.getElementById('eventId').value = event.idEvent;
            document.getElementById('titre').value = event.titre || '';
            document.getElementById('type').value = event.type || '';
            document.getElementById('description').value = event.description || '';
            document.getElementById('lieu').value = event.lieu || '';
            document.getElementById('nombre_place').value = event.nombre_place || 1;
            
            // Formater la date pour l'input datetime-local
            const dateEvent = new Date(event.date_event);
            const timezoneOffset = dateEvent.getTimezoneOffset() * 60000; // en millisecondes
            const localISOTime = (new Date(dateEvent - timezoneOffset)).toISOString().slice(0, 16);
            document.getElementById('date_event').value = localISOTime;
            
            // Afficher la modal
            const modal = new bootstrap.Modal(document.getElementById('eventModal'));
            modal.show();
        }
        
        // Gestion de la soumission du formulaire avec validation Bootstrap
        document.addEventListener('DOMContentLoaded', function() {
            'use strict';
            
            // Fonction pour comparer les dates en ignorant les secondes et millisecondes
            function isFutureDate(dateString) {
                if (!dateString) return false;
                
                // Créer une date à partir de la chaîne fournie
                const selectedDate = new Date(dateString);
                const now = new Date();
                
                // Mettre les deux dates à la même heure (minuit) pour la comparaison
                const selectedDateOnly = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate(), selectedDate.getHours(), selectedDate.getMinutes());
                const nowDateOnly = new Date(now.getFullYear(), now.getMonth(), now.getDate(), now.getHours(), now.getMinutes());
                
                return selectedDateOnly > nowDateOnly;
            }
            
            // Récupérer le formulaire
            const form = document.getElementById('eventForm');
            if (!form) return;
            
            // Désactiver la soumission du formulaire si des champs ne sont pas valides
            form.addEventListener('submit', function(event) {
                // Réinitialiser les messages de validation personnalisés
                const dateInput = document.getElementById('date_event');
                dateInput.setCustomValidity('');
                
                // Vérifier d'abord la validité HTML5 de base
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                } else {
                    // Validation personnalisée de la date
                    if (dateInput && dateInput.value) {
                        if (!isFutureDate(dateInput.value)) {
                            dateInput.setCustomValidity('La date doit être dans le futur');
                            dateInput.reportValidity();
                            event.preventDefault();
                            event.stopPropagation();
                            return false;
                        }
                    }
                }
                
                form.classList.add('was-validated');
            }, false);
            
            // Réinitialiser la validation lorsque la modal est fermée
            const modal = document.getElementById('eventModal');
            if (modal) {
                modal.addEventListener('hidden.bs.modal', function() {
                    form.classList.remove('was-validated');
                    // Réinitialiser les messages de validation personnalisés
                    const dateInput = document.getElementById('date_event');
                    if (dateInput) {
                        dateInput.setCustomValidity('');
                    }
                });
            }
            
            // Validation en temps réel pour la date
            const dateInput = document.getElementById('date_event');
            if (dateInput) {
                dateInput.addEventListener('change', function() {
                    if (this.value) {
                        if (!isFutureDate(this.value)) {
                            this.setCustomValidity('La date doit être dans le futur');
                        } else {
                            this.setCustomValidity('');
                        }
                        this.reportValidity();
                    }
                });
            }
        });
    </script>
    
    <script>
    // Fonction pour gérer l'affichage des champs de nouvelle entreprise
    function toggleEntrepriseFields() {
        const entrepriseId = document.getElementById('entreprise_id');
        const newEntrepriseFields = document.getElementById('newEntrepriseFields');
        
        if (entrepriseId.value !== '') {
            // Désactiver les champs de nouvelle entreprise
            const inputs = newEntrepriseFields.getElementsByTagName('input');
            for (let input of inputs) {
                input.required = false;
            }
        } else {
            // Réactiver les champs requis pour la nouvelle entreprise
            document.getElementById('new_nom').required = true;
        }
    }
    
    // Validation personnalisée du formulaire d'entreprise
    document.addEventListener('DOMContentLoaded', function() {
        const entrepriseForm = document.getElementById('entrepriseForm');
        if (entrepriseForm) {
            entrepriseForm.addEventListener('submit', function(e) {
                const entrepriseId = document.getElementById('entreprise_id').value;
                const newNom = document.getElementById('new_nom').value;
                
                if (entrepriseId === '' && newNom.trim() === '') {
                    e.preventDefault();
                    alert('Veuillez sélectionner une entreprise existante ou remplir le formulaire pour en créer une nouvelle.');
                    return false;
                }
                
                return true;
            });
        }
    });
    </script>
<script src="../assets/js/site.js"></script>
</body>
</html>
