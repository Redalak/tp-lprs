<?php $msg = $_GET['msg'] ?? null; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Inscription - École Sup.</title>

    <!-- Icônes Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body{margin:0;font-family:system-ui,Segoe UI,Arial;background:#f4f4f4}
        .container{
            position:relative;
            max-width:400px;margin:80px auto;background:#fff;
            padding:30px;border-radius:8px;box-shadow:0 0 10px rgba(0,0,0,.1);
        }
        /* icône en haut-droite */
        .corner-link{
            position:absolute;top:12px;right:12px;
            font-size:24px;line-height:1;
            color:#005baa;text-decoration:none;
            transition:transform .15s ease,opacity .15s ease;
        }
        .corner-link:hover{transform:translateY(-1px);opacity:.9;}

        h2{text-align:center;margin-bottom:30px;color:#005baa}
        label{display:block;margin:15px 0 5px;font-weight:600}
        input{width:100%;padding:10px;border:1px solid #ccc;border-radius:4px}
        button{width:100%;padding:12px;background:#005baa;color:#fff;font-size:16px;border:0;border-radius:4px;margin-top:25px;cursor:pointer}
        button:hover{background:#004080}
        .alert{margin:10px 0;padding:10px;border-radius:6px;background:#fee;border:1px solid #f99;color:#900}
        .ok{background:#e9ffe9;border-color:#9f9;color:#060}
        .register-link{text-align:center;margin-top:15px}
        .register-link a{color:#005baa;text-decoration:none}
        .register-link a:hover{text-decoration:underline}
        /* Shared theme */
        @import url('../assets/css/site.css');
    </style>
</head>
<body>
<div class="container">
    <a href="../index.php" class="corner-link" title="Retour à l’accueil">
        <i class="bi bi-arrow-return-left"></i>
    </a>

    <h2>Inscription</h2>

    <?php if ($msg === 'mdp'): ?>
        <div class="alert">Les mots de passe ne correspondent pas.</div>
    <?php elseif ($msg === 'doublon'): ?>
        <div class="alert">Cet email existe déjà.</div>
    <?php elseif ($msg === 'ok'): ?>
        <div class="alert ok">Inscription réussie !</div>
    <?php endif; ?>

    <form action="../src/traitement/gestionInscription.php" method="POST">
        <label for="prenom">Prénom</label>
        <input id="prenom" name="prenom" type="text" required autocomplete="given-name"/>

        <label for="nom">Nom</label>
        <input id="nom" name="nom" type="text" required autocomplete="family-name"/>

        <label for="email">Email</label>
        <input id="email" name="email" type="email" required autocomplete="email"/>

        <label for="mdp">Mot de passe</label>
        <input id="mdp" name="mdp" type="password" required minlength="6" autocomplete="new-password"/>

        <label for="CMdp">Confirmer le mot de passe</label>
        <input id="CMdp" name="CMdp" type="password" required minlength="6" autocomplete="new-password"/>

        <button type="submit">Créer un compte</button>

    <div class="register-link">
        <p>Déjà inscrit ? <a href="connexion.php">Se connecter</a></p>
    </div>
</div>
<script src="../assets/js/site.js"></script>
</body>
</html>