<?php
session_start();

require_once __DIR__ . '/../src/repository/UserRepo.php';
require_once __DIR__ . '/../src/modele/User.php';

use repository\UserRepo;
use modele\User;

// Vérifie si l'utilisateur est connecté et si l'ID correspond à la session
if (!isset($_SESSION['id_user'])) {
    header('Location: connexion.php');  // Redirection vers la page de connexion si non connecté
    exit;
}

// Récupère les informations de l'utilisateur à partir de l'ID de la session
$userRepo = new UserRepo();
$user = $userRepo->getUserById($_SESSION['id_user']);

// Récupère les réservations de l'utilisateur
$reservations = $userRepo->getReservationsByUserId($_SESSION['id_user']);

// Traitement de la modification du profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $email = $_POST['email'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $password = $_POST['password'] ?? '';

    // Mise à jour des données de l'utilisateur
    $user->setEmail($email);
    $user->setNom($nom);
    $user->setPrenom($prenom);

    // Si un mot de passe est fourni, on le met à jour
    if ($password) {
        $user->setMdp(password_hash($password, PASSWORD_DEFAULT)); // Sécurisation du mot de passe
    }

    // Sauvegarde dans la base de données
    $userRepo->modifUser($user);

    // Message de succès
    $successMessage = "Votre profil a été mis à jour avec succès.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Styles déjà existants */
    </style>
</head>
<body>

<div class="container">

    <h1>Mon Profil</h1>

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
        <input type="password" name="password">

        <div class="role">
            Rôle : <?= htmlspecialchars($user->getRole()) ?> (non modifiable)
        </div>

        <button type="submit">Mettre à jour ✅</button>
    </form>

    <!-- Affichage des réservations -->
    <h2>Mes Réservations</h2>
    <?php if (count($reservations) > 0): ?>
        <ul>
            <?php foreach ($reservations as $reservation): ?>
                <li>
                    <strong><?= htmlspecialchars($reservation['titre']) ?></strong><br>
                    Date : <?= htmlspecialchars($reservation['date_event']) ?><br>
                    Lieu : <?= htmlspecialchars($reservation['lieu']) ?><br><br>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucune réservation trouvée.</p>
    <?php endif; ?>

    <div style="text-align:center;margin-top:15px;">
        <a href="../index.php" style="color: var(--primary-color); text-decoration:none;">⬅ Retour Accueil</a>
    </div>

</div>

</body>
</html>
