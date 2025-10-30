<?php

require_once __DIR__ . '/../src/repository/UserRepo.php';
use repository\UserRepo;
use modele\User;

// Récupération de l'ID de l'utilisateur à modifier
$userRepo = new UserRepo();

if (!isset($_GET['id'])) {
    header('Location: adminUser.php');
    exit;
}

$idUser = (int)$_GET['id'];

// Vérification que l'utilisateur existe
$user = $userRepo->getUserById($idUser);

if (!$user) {
    header('Location: adminUser.php');
    exit;
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $ref_entreprise = !empty($_POST['ref_entreprise']) ? $_POST['ref_entreprise'] : null;
    $ref_formation = !empty($_POST['ref_formation']) ? $_POST['ref_formation'] : null;

    // Mise à jour de l'utilisateur
    $user->setNom($nom);
    $user->setPrenom($prenom);
    $user->setEmail($email);
    $user->setRole($role);
    $user->setRefEntreprise($ref_entreprise);
    $user->setRefFormation($ref_formation);

    // Sauvegarde des modifications
    $success = $userRepo->modifUser($user);
    
    if ($success) {
        $_SESSION['success_message'] = 'L\'utilisateur a été modifié avec succès.';
    } else {
        $_SESSION['error_message'] = 'Une erreur est survenue lors de la modification de l\'utilisateur.';
    }

    // Redirection vers la liste des utilisateurs après modification
    header('Location: adminUser.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un utilisateur - Administration</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/admin-style.css">
    <style>
        .role-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: capitalize;
            display: inline-block;
            margin-right: 5px;
        }
        .role-admin { background-color: #e3f2fd; color: #1976d2; }
        .role-prof { background-color: #e8f5e9; color: #388e3c; }
        .role-etudiant { background-color: #fff3e0; color: #f57c00; }
        .role-entreprise { background-color: #f3e5f5; color: #8e24aa; }
        .role-alumni { background-color: #e0f7fa; color: #00acc1; }
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
                <li><a href="adminEvent.php">Événements</a></li>
                <li><a class="active" href="adminUser.php">Utilisateurs</a></li>
                <li><a href="?deconnexion=1">Déconnexion</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="main-content">
    <div class="container">
        <div class="page-header">
            <h1><i class="bi bi-person-gear"></i> Modifier l'utilisateur</h1>
            <a href="adminUser.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Retour à la liste
            </a>
        </div>
        
        <div class="card">
            <div class="card-body">
                <form method="post" class="form-container">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="nom">Nom :</label>
                                <input type="text" id="nom" name="nom" class="form-control" 
                                       value="<?= htmlspecialchars($user->getNom()) ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="prenom">Prénom :</label>
                                <input type="text" id="prenom" name="prenom" class="form-control" 
                                       value="<?= htmlspecialchars($user->getPrenom()) ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email :</label>
                                <input type="email" id="email" name="email" class="form-control" 
                                       value="<?= htmlspecialchars($user->getEmail()) ?>" required>
                            </div>
                        </div>
                        
                        <div class="col">
                            <div class="form-group">
                                <label for="role">Rôle :</label>
                                <select id="role" name="role" class="form-control" required>
                                    <option value="" disabled>Sélectionnez un rôle</option>
                                    <option value="admin" <?= $user->getRole() === 'admin' ? 'selected' : '' ?>>
                                        <span class="role-badge role-admin">Admin</span> - Administrateur
                                    </option>
                                    <option value="prof" <?= $user->getRole() === 'prof' ? 'selected' : '' ?>>
                                        <span class="role-badge role-prof">Prof</span> - Enseignant
                                    </option>
                                    <option value="etudiant" <?= $user->getRole() === 'etudiant' ? 'selected' : '' ?>>
                                        <span class="role-badge role-etudiant">Étudiant</span>
                                    </option>
                                    <option value="entreprise" <?= $user->getRole() === 'entreprise' ? 'selected' : '' ?>>
                                        <span class="role-badge role-entreprise">Entreprise</span>
                                    </option>
                                    <option value="alumni" <?= $user->getRole() === 'alumni' ? 'selected' : '' ?>>
                                        <span class="role-badge role-alumni">Alumni</span>
                                    </option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="ref_entreprise">Référence entreprise (optionnel) :</label>
                                <input type="text" id="ref_entreprise" name="ref_entreprise" class="form-control" 
                                       value="<?= $user->getRefEntreprise() ? htmlspecialchars($user->getRefEntreprise()) : '' ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="ref_formation">Référence formation (optionnel) :</label>
                                <input type="text" id="ref_formation" name="ref_formation" class="form-control" 
                                       value="<?= $user->getRefFormation() ? htmlspecialchars($user->getRefFormation()) : '' ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Enregistrer les modifications
                        </button>
                        <a href="adminUser.php" class="btn btn-outline-secondary">
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
