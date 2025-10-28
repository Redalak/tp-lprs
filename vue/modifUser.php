<?php
require_once __DIR__ . '/../src/repository/UserRepo.php';
use repository\UserRepo;
use modele\User; // Assurez-vous que votre classe User est incluse

// Récupération de l'ID de l'utilisateur à modifier
$userRepo = new UserRepo();

if (!isset($_GET['id'])) {
    die('ID de l’utilisateur manquant');
}

$idUser = (int)$_GET['id'];

// Vérification que l'utilisateur existe
$user = $userRepo->getUserById($idUser);

if (!$user) {
    die('Utilisateur introuvable');
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $ref_entreprise = $_POST['ref_entreprise'] ?? null; // Si vide, 'null'
    $ref_formation = $_POST['ref_formation'] ?? null; // Si vide, 'null'

    // Mise à jour de l'utilisateur
    $user->setNom($nom);
    $user->setPrenom($prenom);
    $user->setEmail($email);
    $user->setRole($role);
    $user->setRefEntreprise($ref_entreprise);
    $user->setRefFormation($ref_formation);

    // Sauvegarde des modifications
    $userRepo->modifUser($user);

    // Redirection vers la liste des utilisateurs après modification
    header('Location: adminUser.php');
    exit;
}
?>

<h2>Modifier l'utilisateur</h2>

<form method="post" style="margin-bottom: 30px; border: 1px solid #ccc; padding: 10px;">
    <label>Nom :</label><br>
    <input type="text" name="nom" value="<?= htmlspecialchars($user->getNom()) ?>" required><br><br>

    <label>Prénom :</label><br>
    <input type="text" name="prenom" value="<?= htmlspecialchars($user->getPrenom()) ?>" required><br><br>

    <label>Email :</label><br>
    <input type="email" name="email" value="<?= htmlspecialchars($user->getEmail()) ?>" required><br><br>

    <label>Rôle :</label><br>
    <select name="role" required>
        <option value="admin" <?= $user->getRole() === 'admin' ? 'selected' : '' ?>>Admin</option>
        <option value="prof" <?= $user->getRole() === 'prof' ? 'selected' : '' ?>>Prof</option>
        <option value="alumni" <?= $user->getRole() === 'alumni' ? 'selected' : '' ?>>Alumni</option>
        <option value="entreprise" <?= $user->getRole() === 'entreprise' ? 'selected' : '' ?>>Entreprise</option>
        <option value="etudiant" <?= $user->getRole() === 'etudiant' ? 'selected' : '' ?>>Etudiant</option>
    </select><br><br>

    <label>Référence Entreprise :</label><br>
    <input type="text" name="ref_entreprise" value="<?= htmlspecialchars($user->getRefEntreprise() ?? 'Non défini') ?>"><br><br>

    <label>Référence Formation :</label><br>
    <input type="text" name="ref_formation" value="<?= htmlspecialchars($user->getRefFormation() ?? 'Non défini') ?>"><br><br>

    <button type="submit">Modifier l'utilisateur</button>
</form>

<p><a href="adminUser.php">Retour à la liste des utilisateurs</a></p>
