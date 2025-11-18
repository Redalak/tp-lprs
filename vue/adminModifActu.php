<?php
declare(strict_types=1);

// Définir le titre de la page
$pageTitle = 'Modifier une actualité';

// Inclure l'en-tête qui gère la session et l'authentification
require_once __DIR__ . '/../includes/header.php';

// Inclure les dépendances nécessaires
require_once __DIR__ . '/../src/bdd/Bdd.php';
require_once __DIR__ . '/../src/repository/UserRepo.php';
require_once __DIR__ . '/../src/repository/ActualitesRepo.php';
require_once __DIR__ . '/../src/modele/User.php';
require_once __DIR__ . '/../src/modele/Actualites.php';

use repository\UserRepo;
use repository\ActualitesRepo;
use modele\Actualites;

// Vérification de l'authentification et des droits d'administration
if (!isset($_SESSION['connexion']) || $_SESSION['connexion'] !== true || empty($_SESSION['id_user'])) {
    header('Location: ../index.php?forbidden=1');
    exit;
}

$userRepo = new UserRepo();
$user = $userRepo->getUserById((int)$_SESSION['id_user']);

// Vérification des droits d'administration
$isAdmin = false;
if ($user) {
    if (method_exists($user, 'isAdmin')) {
        $isAdmin = (bool)$user->isAdmin();
    } elseif (method_exists($user, 'getRole')) {
        $role = strtolower((string)$user->getRole());
        $isAdmin = in_array($role, ['admin','role_admin'], true);
    } elseif (property_exists($user, 'role')) {
        $role = strtolower((string)$user->role);
        $isAdmin = in_array($role, ['admin','role_admin'], true);
    } elseif (!empty($_SESSION['role'])) {
        $role = strtolower((string)$_SESSION['role']);
        $isAdmin = in_array($role, ['admin','role_admin'], true);
    }
}

if (!$isAdmin) {
    header('Location: ../index.php?forbidden=1');
    exit;
}

// Vérifier si un ID a été fourni
if (empty($_GET['id'])) {
    header('Location: adminGestion.php?error=1&message=' . urlencode('Aucun ID d\'actualité fourni'));
    exit;
}

$actualitesRepo = new ActualitesRepo();
$actualite = $actualitesRepo->getActualiteById((int)$_GET['id']);

if (!$actualite) {
    header('Location: adminGestion.php?error=1&message=' . urlencode('Actualité introuvable'));
    exit;
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['contenu'])) {
    try {
        $contenu = trim($_POST['contenu']);
        
        // Validation
        if (strlen($contenu) < 10) {
            throw new Exception('Le contenu doit faire au moins 10 caractères');
        }
        if (strlen($contenu) > 50) {
            throw new Exception('Le contenu ne doit pas dépasser 50 caractères');
        }
        
        // Mise à jour de l'actualité
        $actualite->setContexte($contenu);
        $actualitesRepo->modifActualite($actualite);
        
        // Redirection avec message de succès
        header('Location: adminGestion.php?success=1&message=' . urlencode('L\'actualité a été modifiée avec succès'));
        exit;
        
    } catch (Exception $e) {
        $message = $e->getMessage();
        $alertClass = 'alert-danger';
        error_log('Erreur lors de la modification d\'actualité : ' . $message);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Modifier une actualité | Administration</title>
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/site.css" rel="stylesheet">
    
    <style>
        .table-responsive {
            margin-top: 20px;
        }
        .action-buttons .btn {
            margin: 0 2px;
        }
    </style>
</head>
<body>

<header>
    <div class="container">
        <a class="logo" href="../index.php">École Sup.</a>
        <nav>
            <ul>
                <li><a href="../index.php">Accueil</a></li>
                <li><a href="formations.php">Formations</a></li>
                <li><a href="entreprise.php">Entreprises</a></li>
                <li><a href="offres.php">Offres</a></li>
                <li><a href="evenement.php">Événements</a></li>
                <li><a href="supportContact.php">Contact</a></li>
                <?php if (isset($_SESSION['connexion']) && $_SESSION['connexion'] === true): ?>
                    <li><a href="forum.php">Forum</a></li>
                    <?php if ($isAdmin): ?>
                        <li><a class="active" href="admin.php">Admin</a></li>
                    <?php endif; ?>
                    <li class="profile-dropdown">
                        <a href="profilUser.php" class="profile-icon">👤</a>
                        <div class="dropdown-content">
                            <span>Bonjour, <?= htmlspecialchars($user->getPrenom() ?? 'Utilisateur') ?> !</span>
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

<main class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Modifier une actualité</h1>
        <a href="adminGestion.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <?php if (isset($message)): ?>
        <div class="alert <?= $alertClass ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" onsubmit="return validateForm()">
                <div class="mb-3">
                    <label for="contenu" class="form-label">Contenu de l'actualité</label>
                    <textarea class="form-control" id="contenu" name="contenu" rows="2" required minlength="10" maxlength="50" 
                              oninput="updateCharCount(this)"><?= htmlspecialchars($actualite->getContexte()) ?></textarea>
                    <div class="form-text"><span id="charCount"><?= mb_strlen($actualite->getContexte()) ?></span>/50 caractères</div>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="adminGestion.php" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<footer class="mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5>École Sup.</h5>
                <p>123 Rue de l'Innovation, 75000 Paris</p>
                <p>contact@ecolesup.fr</p>
                <p>+33 1 23 45 67 89</p>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="social-links">
                    <a href="#" class="me-2"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="me-2"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="me-2"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
        </div>
        <div class="text-center mt-3">
            <p class="mb-0">&copy; <?= date('Y') ?> École Sup. Tous droits réservés.</p>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function updateCharCount(textarea) {
    const charCount = textarea.value.length;
    document.getElementById('charCount').textContent = charCount;
    
    // Changer la couleur du compteur si on approche ou dépasse la limite
    const charCountElement = document.getElementById('charCount');
    if (charCount > 45) {
        charCountElement.style.color = 'red';
        charCountElement.style.fontWeight = 'bold';
    } else {
        charCountElement.style.color = '';
        charCountElement.style.fontWeight = '';
    }
}

function validateForm() {
    const contenu = document.getElementById('contenu').value.trim();
    if (contenu.length < 10) {
        alert('Le contenu doit faire au moins 10 caractères');
        return false;
    }
    if (contenu.length > 50) {
        alert('Le contenu ne doit pas dépasser 50 caractères');
        return false;
    }
    return true;
}

// Initialiser le compteur au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('contenu');
    updateCharCount(textarea);
});
</script>
</body>
</html>
