<?php
// Définir le titre de la page
$pageTitle = 'AdminUtilisateur';

// Inclure l'en-tête qui gère la session et l'authentification
require_once __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../src/repository/UserRepo.php';
use repository\UserRepo;
use modele\User;

$userRepo = new UserRepo();
$users = $userRepo->listeUser();

// Récupérer prénom/nom pour l'en-tête
$prenom = $_SESSION['prenom'] ?? '';
$nom    = $_SESSION['nom'] ?? '';
if (!empty($_SESSION['id_user'])) {
    try {
        $u = $userRepo->getUserById((int)$_SESSION['id_user']);
        if ($u && method_exists($u, 'getPrenom')) { $prenom = $u->getPrenom(); }
        if ($u && method_exists($u, 'getNom'))    { $nom    = $u->getNom(); }
    } catch (\Throwable $e) {}
}

// Traitement du formulaire de création
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $mdp = $_POST['mdp'];
    $role = $_POST['role'];
    $ref_entreprise = !empty($_POST['ref_entreprise']) ? $_POST['ref_entreprise'] : null;
    $ref_formation = !empty($_POST['ref_formation']) ? $_POST['ref_formation'] : null;

    // Hachage du mot de passe
    $hashedPassword = password_hash($mdp, PASSWORD_BCRYPT);

    $newUser = new User([
        'nom' => $nom,
        'prenom' => $prenom,
        'email' => $email,
        'mdp' => $hashedPassword,
        'role' => $role,
        'ref_entreprise' => $ref_entreprise,
        'ref_formation' => $ref_formation,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);

    $userRepo->ajoutUser($newUser);

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
    <title>Administration - Gestion des Utilisateurs</title>
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
        .role-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: capitalize;
        }
        .role-admin { background-color: #e3f2fd; color: #1976d2; }
        .role-prof { background-color: #e8f5e9; color: #388e3c; }
        .role-etudiant { background-color: #fff3e0; color: #f57c00; }
        .role-entreprise { background-color: #f3e5f5; color: #8e24aa; }
        .role-alumni { background-color: #e0f7fa; color: #00acc1; }
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
                <?php if (isset($_SESSION['id_user'])): ?>
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
        <h1>Gestion des Utilisateurs</h1>
        
        <div class="alert alert-info">
            <i class="bi bi-people"></i> Gestion des utilisateurs du système.
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Approbation</th>
                    <th>Entreprise</th>
                    <th>Formation</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($users as $user): 
                    $roleClass = 'role-' . strtolower($user->getRole());
                ?>
                    <tr>
                        <td><?= $user->getIdUser() ?></td>
                        <td><?= htmlspecialchars($user->getNom()) ?></td>
                        <td><?= htmlspecialchars($user->getPrenom()) ?></td>
                        <td><?= htmlspecialchars($user->getEmail()) ?></td>
                        <td>
                            <span class="role-badge <?= $roleClass ?>">
                                <?= ucfirst(htmlspecialchars($user->getRole())) ?>
                            </span>
                        </td>
                        <td>
                            <?php $approved = method_exists($user,'getIsApproved') ? (int)$user->getIsApproved() : 0; ?>
                            <?php if ($approved === 1): ?>
                                <span style="color:#2e7d32;font-weight:600">Approuvé</span>
                            <?php else: ?>
                                <span style="color:#d84315;font-weight:600">En attente</span>
                            <?php endif; ?>
                        </td>
                        <td><?= !empty($user->getRefEntreprise()) ? htmlspecialchars($user->getRefEntreprise()) : '<span class="text-muted">-</span>' ?></td>
                        <td><?= !empty($user->getRefFormation()) ? htmlspecialchars($user->getRefFormation()) : '<span class="text-muted">-</span>' ?></td>
                        <td class="actions">
                            <a href="modifUser.php?id=<?= $user->getIdUser() ?>" class="btn btn-sm btn-primary" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="suppUser.php?id_user=<?= $user->getIdUser() ?>" 
                               class="btn btn-sm btn-danger" 
                               title="Supprimer"
                               onclick="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?')">
                                <i class="bi bi-trash"></i>
                            </a>
                            <?php if ((int)($user->getIdUser()) > 0): ?>
                                <?php if ($approved !== 1): ?>
                                    <a href="../src/traitement/approveUser.php?id=<?= $user->getIdUser() ?>&action=approve" class="btn btn-sm" style="background:#2e7d32;color:#fff" title="Approuver">✔</a>
                                <?php else: ?>
                                    <a href="../src/traitement/approveUser.php?id=<?= $user->getIdUser() ?>&action=deny" class="btn btn-sm" style="background:#d84315;color:#fff" title="Retirer l'approbation">✖</a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <section class="mt-5">
            <h2>Créer un nouvel utilisateur</h2>
            
            <form method="post" class="form-container">
                <input type="hidden" name="create_user" value="1">
                
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="nom">Nom :</label>
                            <input type="text" id="nom" name="nom" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="prenom">Prénom :</label>
                            <input type="text" id="prenom" name="prenom" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email :</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="col">
                        <div class="form-group">
                            <label for="mdp">Mot de passe :</label>
                            <input type="password" id="mdp" name="mdp" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="role">Rôle :</label>
                            <select id="role" name="role" class="form-control" required>
                                <option value="">Sélectionnez un rôle</option>
                                <option value="admin">Administrateur</option>
                                <option value="prof">Professeur</option>
                                <option value="etudiant">Étudiant</option>
                                <option value="entreprise">Entreprise</option>
                                <option value="alumni">Alumni</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="ref_entreprise">Référence Entreprise (optionnel) :</label>
                            <input type="text" id="ref_entreprise" name="ref_entreprise" class="form-control">
                        </div>
                    </div>
                    
                    <div class="col">
                        <div class="form-group">
                            <label for="ref_formation">Référence Formation (optionnel) :</label>
                            <input type="text" id="ref_formation" name="ref_formation" class="form-control">
                        </div>
                    </div>
                </div>
                
                <div class="form-actions mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-person-plus"></i> Créer l'utilisateur
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

</body>
<script src="../assets/js/site.js"></script>
</html>
