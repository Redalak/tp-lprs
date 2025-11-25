<?php
$role = $_GET['role'] ?? 'etudiant';
$messages = [
    'admin' => 'Votre compte administrateur a été créé avec succès !',
    'etudiant' => 'Votre inscription a été enregistrée avec succès. Un administrateur validera votre compte sous peu.',
    'alumni' => 'Votre inscription a été enregistrée avec succès. Un administrateur validera votre compte sous peu.',
    'prof' => 'Votre inscription a été enregistrée avec succès. Un administrateur validera votre compte sous peu.'
];

$message = $messages[$role] ?? $messages['etudiant'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Inscription réussie - École Sup.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { margin: 0; font-family: system-ui, Segoe UI, Arial; background: #f4f4f4; }
        .container {
            position: relative;
            max-width: 500px; margin: 100px auto; background: #fff;
            padding: 40px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,.1);
            text-align: center;
        }
        .success-icon {
            font-size: 60px; color: #28a745; margin-bottom: 20px;
        }
        h2 { color: #005baa; margin-bottom: 20px; }
        p { margin-bottom: 30px; line-height: 1.6; }
        .btn {
            display: inline-block; padding: 10px 20px; background: #005baa; 
            color: white; text-decoration: none; border-radius: 4px;
            transition: background-color 0.3s;
        }
        .btn:hover { background: #004080; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">
            <i class="bi bi-check-circle"></i>
        </div>
        <h2>Inscription réussie !</h2>
        <p><?php echo htmlspecialchars($message); ?></p>
        <?php if ($role === 'admin'): ?>
            <a href="espace_admin.php" class="btn">Accéder au tableau de bord</a>
        <?php else: ?>
            <a href="connexion.php" class="btn">Se connecter</a>
        <?php endif; ?>
    </div>
</body>
</html>
