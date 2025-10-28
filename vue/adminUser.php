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
    $ref_entreprise = $_POST['ref_entreprise'];
    $ref_formation = $_POST['ref_formation'];

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
            <td>
                <?= !empty($user->getRefEntreprise()) ? htmlspecialchars($user->getRefEntreprise()) : 'Non spécifié' ?>
            </td>
            <td>
                <?= !empty($user->getRefFormation()) ? htmlspecialchars($user->getRefFormation()) : 'Non spécifié' ?>
            </td>
            <td>
                <a href="modifUser.php?id=<?= $user->getIdUser() ?>">Modifier</a> |
                <a href="suppUser.php?id_user=<?= $user->getIdUser() ?>" onclick="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?')">Supprimer</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>


<h2>Créer un nouvel utilisateur</h2>

<form method="post" style="margin-bottom: 30px; border: 1px solid #ccc; padding: 10px;">
    <input type="hidden" name="create_user" value="1">

    <label>Nom :</label><br>
    <input type="text" name="nom" required><br><br>

    <label>Prénom :</label><br>
    <input type="text" name="prenom" required><br><br>

    <label>Email :</label><br>
    <input type="email" name="email" required><br><br>

    <label>Mot de passe :</label><br>
    <input type="password" name="mdp" required><br><br>

    <label>Rôle :</label><br>
    <select name="role" required>
        <option value="admin">Admin</option>
        <option value="prof">Prof</option>
        <option value="alumni">Alumni</option>
        <option value="entreprise">Entreprise</option>
        <option value="etudiant">Etudiant</option>
    </select><br><br>

    <label>Référence Entreprise (optionnel) :</label><br>
    <input type="text" name="ref_entreprise"><br><br>

    <label>Référence Formation (optionnel) :</label><br>
    <input type="text" name="ref_formation"><br><br>

    <button type="submit">Créer l'utilisateur</button>
</form>
