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
        .champs-specifiques[style*="display: block"] {
            display: block !important;
        }
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

    <form action="../src/traitement/gestionInscription.php" method="POST" id="inscriptionForm">
        <label for="role">Type de compte</label>
        <select id="role" name="role" required onchange="afficherChampsSpecifiques()">
            <option value="">Sélectionnez un type de compte</option>
            <option value="etudiant">Étudiant</option>
            <option value="alumni">Alumni</option>
            <option value="prof">Professeur</option>
        </select>

        <div id="champsCommuns">
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
        </div>

        <!-- Champs spécifiques aux étudiants -->
        <div id="champsEtudiant" class="champs-specifiques" style="display: none;">
            <label for="annee_promo">Année de promotion</label>
            <input id="annee_promo" name="annee_promo" type="text" placeholder="Ex: 2023-2024"/>
        </div>

        <!-- Champs spécifiques aux alumni -->
        <div id="champsAlumni" class="champs-specifiques" style="display: none;">
            <label for="emploi_actuel">Emploi actuel</label>
            <input id="emploi_actuel" name="emploi_actuel" type="text" placeholder="Votre poste actuel"/>
            
            <label for="nom_entreprise">Nom de l'entreprise</label>
            <input id="nom_entreprise" name="nom_entreprise" type="text" placeholder="Nom de votre entreprise actuelle"/>
        </div>

        <!-- Champs spécifiques aux professeurs -->
        <div id="champsProf" class="champs-specifiques" style="display: none;">
            <label for="matiere">Matière enseignée</label>
            <input id="matiere" name="matiere" type="text" placeholder="Votre matière principale"/>
        </div>

        <button type="submit">Créer un compte</button>
    </form>
    
    <div class="register-link">
        <p>Déjà inscrit ? <a href="connexion.php">Se connecter</a></p>
    </div>
</div>
    <script>
        // Fonction pour afficher les champs spécifiques
        function afficherChampsSpecifiques() {
            // Masquer tous les champs spécifiques
            document.querySelectorAll('.champs-specifiques').forEach(div => {
                div.style.display = 'none';
            });

            // Afficher les champs en fonction du rôle
            const role = document.getElementById('role').value;
            
            if (role === 'etudiant') {
                document.getElementById('champsEtudiant').style.display = 'block';
            } else if (role === 'alumni') {
                document.getElementById('champsAlumni').style.display = 'block';
            } else if (role === 'prof') {
                document.getElementById('champsProf').style.display = 'block';
            }
            
            console.log('Affichage des champs pour le rôle:', role);
        }
        
        // Initialisation au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page chargée, initialisation du formulaire');
            
            // Ajouter l'événement onchange au select
            const roleSelect = document.getElementById('role');
            if (roleSelect) {
                roleSelect.addEventListener('change', afficherChampsSpecifiques);
                
                // Afficher les champs si un rôle est déjà sélectionné (en cas de rechargement de page)
                if (roleSelect.value) {
                    afficherChampsSpecifiques();
                }
            }
        });
    </script>
    <script src="../assets/js/site.js"></script>
</body>
</html>