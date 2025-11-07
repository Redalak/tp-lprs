<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <title>Mot de passe oublié - École Sup.</title>

    <style>
        body{margin:0;font-family:'Segoe UI',sans-serif;background:#f4f4f4;}

        .container{
            position: relative;
            max-width:400px;margin:80px auto;background:#fff;
            padding:30px;border-radius:8px;
            box-shadow:0 0 10px rgba(0,0,0,.1);
        }

        .corner-link{
            position:absolute;top:12px;right:12px;
            font-size:24px;line-height:1;
            color:#005baa;text-decoration:none;
            transition:transform .15s ease, opacity .15s ease;
        }
        .corner-link:hover{transform:translateY(-1px);opacity:.9;}

        h2{text-align:center;margin:0 0 30px;color:#005baa;}

        label{display:block;margin:15px 0 5px;font-weight:700;}

        input{
            width:100%;padding:10px;border:1px solid #ccc;
            border-radius:4px;
        }

        button{
            width:100%;padding:12px;background:#005baa;
            color:#fff;font-size:16px;
            border:0;border-radius:4px;
            margin-top:25px;cursor:pointer;
            transition:background .3s;
        }
        button:hover{background:#004080;}

        .register-link{text-align:center;margin-top:15px;}
        .register-link a{color:#005baa;text-decoration:none;}
        .register-link a:hover{text-decoration:underline;}
        /* Shared theme */
        @import url('../assets/css/site.css');
    </style>
</head>
<body>

<div class="container">

    <a href="../index.php" class="corner-link" title="Retour à l’accueil">
        <i class="bi bi-arrow-return-left"></i>
    </a>

    <h2>Mot de passe oublié</h2>

    <p style="color:#333; text-align:center;">
        Entrez votre adresse email pour recevoir un lien
        de réinitialisation.
    </p>

    <form action="../src/api/requete_mdp.php" method="POST">
        <label for="email">Adresse email</label>
        <input type="email" id="email" name="email" required>

        <button type="submit">Envoyer le lien</button>
    </form>

    <div class="register-link">
        <p>Retour à la <a href="connexion.php">connexion</a></p>
    </div>

</div>

<script src="../assets/js/site.js"></script>
</body>
</html>
