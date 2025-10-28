<?php
require_once __DIR__ . '/../src/repository/UserRepo.php';
use repository\UserRepo;
use modele\User; // Assurez-vous que votre classe User est incluse

$userRepo = new UserRepo();
$users = $userRepo->listeUser();

// Traitement du formulaire de création
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $mdp = $_POST['mdp'];
    $role = $_POST['role'];

    // Références optionnelles
    $ref_entreprise = !empty($_POST['ref_entreprise']) ? $_POST['ref_entreprise'] : null;
    $ref_formation = !empty($_POST['ref_formation']) ? $_POST['ref_formation'] : null;

    // Hachage du mot de passe
    $hashedPassword = password_hash($mdp, PASSWORD_BCRYPT);

    // Création de l'utilisateur
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

    // Ajout de l'utilisateur à la base de données
    $userRepo->ajoutUser($newUser);

    // Rafraîchir la liste des utilisateurs
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>

<h2>Liste des utilisateurs</h2>

<table border="1" cellpadding="5" cellspacing="0">
    <thead>
    <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Email</th>
        <th>Rôle</th>
        <th>Entreprise</th>
        <th>Formation</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($users as $user): ?>
        <tr>
            <td><?= $user->getIdUser() ?></td>
            <td><?= htmlspecialchars($user->getNom()) ?></td>
            <td><?= htmlspecialchars($user->getPrenom()) ?></td>
            <td><?= htmlspecialchars($user->getEmail()) ?></td>
            <td><?= htmlspecialchars($user->getRole()) ?></td>
            <td><?= htmlspecialchars($user->getRefEntreprise() ?? 'N/A') ?></td>
            <td><?= htmlspecialchars($user->getRefFormation() ?? 'N/A') ?></td>
            <td>
                <a href="modifUser.php?id=<?= $user->getIdUser() ?>">Modifier</a> |
                <a href="deleteUser.php?id_user=<?= $user->getIdUser() ?>" onclick="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?')">Supprimer</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h2>Créer un nouvel utilisateur</h2>

<!-- Le formulaire reste inchangé -->

