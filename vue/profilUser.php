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
        :root {
            --primary-color: #0A4D68;
            --secondary-color: #088395;
            --background-color: #f4f7fa;
            --surface-color: #ffffff;
            --accent-color: #F39C12;
            --text-color: #343a40;
            --border-radius: 10px;
            --shadow: 0px 4px 18px rgba(0,0,0,0.08);
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: var(--background-color);
        }

        .container {
            max-width: 650px;
            margin: 80px auto;
            background: var(--surface-color);
            padding: 40px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        h1 {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 30px;
        }

        label {
            font-weight: 500;
            margin-bottom: 5px;
            display: block;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border-radius: var(--border-radius);
            border: 1px solid #ddd;
            font-size: 1rem;
        }
        input:focus {
            border-color: var(--secondary-color);
            outline: none;
        }

        .role {
            background: #eaf8fa;
            padding: 12px;
            border-radius: var(--border-radius);
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        button {
            width: 100%;
            background: var(--secondary-color);
            border: none;
            padding: 14px;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            cursor: pointer;
            transition: 0.3s ease-in-out;
        }

        button:hover {
            background: var(--primary-color);
            transform: translateY(-2px);
        }

        .success {
            color: green;
            background: #e9ffe9;
            border-left: 4px solid green;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: var(--border-radius);
            text-align: center;
            font-weight: 600;
        }
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
    <div style="text-align:center;margin-top:15px;">
        <a href="../index.php" style="color: var(--primary-color); text-decoration:none;">⬅ Retour Accueil</a>
    </div>

</div>

</body>
</html>
