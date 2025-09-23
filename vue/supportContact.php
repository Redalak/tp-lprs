<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Contact - École Exemple</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        header {
            background: white;
            border-bottom: 1px solid #ddd;
            padding: 15px 20px;
        }

        .logo {
            font-size: 1.5em;
            color: #005baa;
            font-weight: bold;
        }

        main {
            max-width: 700px;
            margin: 60px auto;
            padding: 0 20px;
        }

        h1 {
            text-align: center;
            color: #003a70;
            margin-bottom: 40px;
        }

        form {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        input[type="text"],
        input[type="email"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1em;
            font-family: inherit;
            resize: vertical;
        }

        textarea {
            min-height: 150px;
        }

        button {
            background-color: #005baa;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #004070;
        }

        footer {
            background: #003a70;
            color: white;
            text-align: center;
            padding: 30px 20px;
            margin-top: 60px;
        }

        @media (max-width: 500px) {
            form {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="logo">École Exemple</div>
</header>

<main>
    <h1>Contactez-nous</h1>

    <form action="https://formsubmit.co/974540d9055dcfa92fbb57af58813f4b" method="POST">
        <!-- Champs cachés de configuration -->
        <input type="hidden" name="_captcha" value="false">
        <input type="hidden" name="_next" value="https://votresite.fr/merci.html">

        <!-- Champs du formulaire -->
        <div class="form-group">
            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" required />
        </div>

        <div class="form-group">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" required />
        </div>

        <div class="form-group">
            <label for="email">Adresse email</label>
            <input type="email" id="email" name="email" required />
        </div>

        <div class="form-group">
            <label for="sujet">Sujet</label>
            <select id="sujet" name="sujet" required>
                <option value="">-- Choisissez un sujet --</option>
                <option value="admission">Demande d’admission</option>
                <option value="formation">Informations sur les formations</option>
                <option value="entreprise">Partenariat entreprise</option>
                <option value="autre">Autre</option>
            </select>
        </div>

        <div class="form-group">
            <label for="message">Votre message</label>
            <textarea id="message" name="message" required></textarea>
        </div>

        <button type="submit">Envoyer</button>
    </form>
</main>

<footer>
    &copy; 2025 École Exemple - Tous droits réservés
</footer>

</body>
</html>
