<?php
// Définir le titre de la page
$pageTitle = 'Connexion';

// Inclure l'en-tête qui gère la session et l'authentification
require_once __DIR__ . '/../includes/header.php';

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['connexion']) && $_SESSION['connexion'] === true) {
    header('Location: /lprs/tp-lprs/vue/evenement.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <title><?= $pageTitle ?> - LPRS</title>
    <style>
        body{margin:0;font-family:'Segoe UI',sans-serif;background:#f4f4f4;}

        .container{
            position: relative;              /* pour positionner l’icône dans la carte */
            max-width:400px;margin:80px auto;background:#fff;
            padding:30px;border-radius:8px;box-shadow:0 0 10px rgba(0,0,0,.1);
        }

        /* Icône bleu en haut-droite */
        .corner-link{
            position:absolute;top:12px;right:12px;
            font-size:24px;line-height:1;
            color:#005baa;text-decoration:none;
            transition:transform .15s ease, opacity .15s ease;
        }
        .corner-link:hover{transform:translateY(-1px);opacity:.9;}

        h2{text-align:center;margin:0 0 30px;color:#005baa;}
        label{display:block;margin:15px 0 5px;font-weight:700;}
        input{width:100%;padding:10px;border:1px solid #ccc;border-radius:4px;}
        button{
            width:100%;padding:12px;background:#005baa;color:#fff;font-size:16px;
            border:0;border-radius:4px;margin-top:25px;cursor:pointer;transition:background .3s;
        }
        button:hover{background:#004080;}
        .register-link{text-align:center;margin-top:15px;}
        .register-link a{color:#005baa;text-decoration:none;}
        .register-link a:hover{text-decoration:underline;}
        .alert{margin:10px 0;padding:10px;border-radius:6px;background:#fff4e5;border:1px solid #f7c77d;color:#8a5a00}
        .alert.error{background:#fee;border-color:#f99;color:#900}
        /* Shared theme */
        @import url('../assets/css/site.css');
    </style>
</head>
<body>

<div class="container">
    <a href="../index.php" class="corner-link" title="Retour à l’accueil">
       <i class="bi bi-arrow-return-left"></i>
    </a>

    <h2>Connexion</h2>
    <?php if (!empty($_GET['parametre']) && $_GET['parametre']==='nonApprouve'): ?>
        <div class="alert">
            Votre compte est en attente de validation par un administrateur. Vous recevrez un accès dès approbation.
        </div>
    <?php elseif (!empty($_GET['parametre']) && $_GET['parametre']==='emailmdpInvalide'): ?>
        <div class="alert error">Email ou mot de passe invalide.</div>
    <?php endif; ?>
    <form action="../src/traitement/gestionConnexion.php" method="POST">
        <label for="email">Adresse email</label>
        <input type="email" id="email" name="email" required />

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required />

        <button type="submit">Se connecter</button>
    </form>

        <p>Pas encore de compte ? <a href="inscription.php">S'inscrire</a></p>
        <p>Mot de passe oublié ? <a href="oublie_mdp.php">Récupération par mail</a></p>
    </div>
</div>

<script src="../assets/js/site.js"></script>
</body>
</html>