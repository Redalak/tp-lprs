<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Inscription - École Exemple</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f4f4f4;
        }

        .container {
            max-width: 500px;
            margin: 80px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #005baa;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            margin-top: 15px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 3px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #005baa;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            margin-top: 25px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #004080;
        }

        .login-link {
            text-align: center;
            margin-top: 15px;
        }

        .login-link a {
            color: #005baa;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Inscription</h2>
    <form action="#" method="POST">
        <label for="firstname">Prénom</label>
        <input type="text" id="firstname" name="firstname" required />

        <label for="lastname">Nom</label>
        <input type="text" id="lastname" name="lastname" required />

        <label for="email">Adresse email</label>
        <input type="email" id="email" name="email" required />

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required minlength="6" />

        <label for="confirm">Confirmer le mot de passe</label>
        <input type="password" id="confirm" name="confirm" required minlength="6" />

        <button type="submit">Créer un compte</button>
    </form>
    <div class="login-link">
        <p>Déjà inscrit ? <a href="#">Se connecter</a></p>
    </div>
</div>

</body>
</html>
