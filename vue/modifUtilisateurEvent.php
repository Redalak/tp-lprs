<?php
session_start();

require_once __DIR__ . '/../src/repository/UserRepo.php';
require_once __DIR__ . '/../src/repository/EventRepo.php';
use repository\UserRepo;
use repository\EventRepo;

// Vérification de l'authentification
if (!isset($_SESSION['id_user'])) {
    header('Location: connexion.php');
    exit;
}

$userRepo = new UserRepo();
$eventRepo = new EventRepo();

// Récupération de l'ID de l'utilisateur à partir de l'URL
if (!isset($_GET['id'])) {
    header('Location: profilUser.php');
    exit;
}

$userId = (int)$_GET['id'];

// Vérifier que l'utilisateur connecté est bien celui dont on veut modifier le profil
if ($_SESSION['id_user'] !== $userId) {
    $_SESSION['message'] = "Vous n'êtes pas autorisé à modifier ce profil.";
    $_SESSION['messageClass'] = "danger";
    header('Location: profilUser.php');
    exit;
}

// Récupérer l'utilisateur à modifier
$user = $userRepo->getUserById($userId);

if (!$user) {
    $_SESSION['message'] = "Utilisateur non trouvé.";
    $_SESSION['messageClass'] = "danger";
    header('Location: profilUser.php');
    exit;
}

// Récupérer les événements créés par l'utilisateur
$evenementsUtilisateur = $eventRepo->getEvenementsParUtilisateur($userId);

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user->setNom(htmlspecialchars(trim($_POST['nom'])));
    $user->setPrenom(htmlspecialchars(trim($_POST['prenom'])));
    $user->setEmail(htmlspecialchars(trim($_POST['email'])));
    
    // Mise à jour du mot de passe si fourni
    if (!empty($_POST['password'])) {
        $user->setPassword(password_hash($_POST['password'], PASSWORD_DEFAULT));
    }

    $success = $userRepo->updateUser($user);
    
    if ($success) {
        $_SESSION['message'] = 'Votre profil a été mis à jour avec succès.';
        $_SESSION['messageClass'] = 'success';
        header('Location: profilUser.php');
        exit;
    } else {
        $error = 'Une erreur est survenue lors de la mise à jour du profil.';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier mon profil - École Sup.</title>
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
        }
        .main-content {
            padding: 2rem 0;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        .form-container {
            padding: 2rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="main-content">
        <div class="container">
            <div class="page-header mb-4">
                <h1><i class="bi bi-person-gear"></i> Modifier mon profil</h1>
                <a href="profilUser.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Retour à mon profil
                </a>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form method="post" class="form-container">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nom" class="form-label">Nom</label>
                                    <input type="text" class="form-control" id="nom" name="nom" 
                                           value="<?= htmlspecialchars($user->getNom()) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="prenom" class="form-label">Prénom</label>
                                    <input type="text" class="form-control" id="prenom" name="prenom" 
                                           value="<?= htmlspecialchars($user->getPrenom()) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($user->getEmail()) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <div class="form-text">Laissez ce champ vide si vous ne souhaitez pas modifier votre mot de passe.</div>
                        </div>

                        <hr class="my-4">
                        
                        <h4 class="mb-3">Mes événements créés</h4>
                        <?php if (empty($evenementsUtilisateur)): ?>
                            <div class="alert alert-info">
                                Vous n'avez pas encore créé d'événement.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Titre</th>
                                            <th>Date</th>
                                            <th>Lieu</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($evenementsUtilisateur as $event): 
                                            $dateEvent = new DateTime($event->getDateEvent());
                                        ?>
                                            <tr>
                                                <td><?= htmlspecialchars($event->getTitre()) ?></td>
                                                <td><?= $dateEvent->format('d/m/Y H:i') ?></td>
                                                <td><?= htmlspecialchars($event->getLieu()) ?></td>
                                                <td>
                                                    <a href="modifEvent.php?id=<?= $event->getIdEvent() ?>" 
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="Modifier l'événement">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="profilUser.php" class="btn btn-outline-secondary me-md-2">Annuler</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
