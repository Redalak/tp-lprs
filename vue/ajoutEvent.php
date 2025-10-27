<?php
require_once __DIR__ . '/../src/repository/EventRepo.php';
use repository\eventRepo;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Événement</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: #f5f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            width: 400px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-top: 10px;
        }

        input, textarea, select {
            margin-top: 5px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        button {
            margin-top: 20px;
            padding: 10px;
            background-color: #0078D7;
            color: white;
            font-size: 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #005a9e;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Ajouter un Événement</h1>

    <!-- Affichage du message d’erreur si champs vides -->
    <?php if (isset($_GET['error']) && $_GET['error'] === 'champs_vides'): ?>
        <p class="error">⚠️ Tous les champs doivent être remplis.</p>
    <?php endif; ?>

    <form action="../src/traitement/ajoutEvent.php" method="POST">
        <label for="type">Type d'événement :</label>
        <select name="type" id="type" required>
            <option value="">-- Sélectionnez un type --</option>
            <option value="Concert">Concert</option>
            <option value="Conférence">Conférence</option>
            <option value="Sport">Sport</option>
            <option value="Autre">Autre</option>
        </select>

        <label for="titre">Titre :</label>
        <input type="text" id="titre" name="titre" placeholder="Nom de l'événement" required>

        <label for="description">Description :</label>
        <textarea id="description" name="description" placeholder="Décrivez brièvement l'événement" required></textarea>

        <label for="lieu">Lieu :</label>
        <input type="text" id="lieu" name="lieu" placeholder="Ex : Paris, Salle des fêtes" required>

        <label for="nombre_place">Nombre de places :</label>
        <input type="number" id="nombre_place" name="nombre_place" min="1" placeholder="Ex : 100" required>

        <label for="date_event">Date et heure de l'événement :</label>
        <input type="datetime-local" id="date_event" name="date_event" required>


        <label for="etat">État :</label>
        <select name="etat" id="etat" required>
            <option value="">-- Sélectionnez un état --</option>
            <option value="ouvert">Ouvert</option>
            <option value="fermé">Fermé</option>
            <option value="reporté">Reporté</option>
        </select>

        <!-- Si ton script utilise l'ID utilisateur -->
        <input type="hidden" name="ref_user" value="1"> <!-- À adapter selon la session -->

        <button type="submit">Ajouter l'événement</button>
    </form>
</div>

</body>
</html>
