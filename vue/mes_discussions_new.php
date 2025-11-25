<?php
declare(strict_types=1);

// Définir le titre de la page
$pageTitle = 'Mes discussions';

// Inclure l'en-tête qui gère la session et l'authentification
require_once __DIR__ . '/../includes/header.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header('Location: ../index.php?error=not_logged_in');
    exit;
}

require_once __DIR__ . '/../src/modele/PForum.php';
require_once __DIR__ . '/../src/modele/RForum.php';
require_once __DIR__ . '/../src/repository/PForumRepo.php';
require_once __DIR__ . '/../src/repository/RForumRepo.php';
require_once __DIR__ . '/../src/repository/UserRepo.php';

use repository\PForumRepo;
use repository\RForumRepo;
use repository\UserRepo;

$pRepo = new PForumRepo();
$rRepo = new RForumRepo();
$userRepo = new UserRepo();

$userId = (int)$_SESSION['id_user'];
$prenom = $_SESSION['prenom'] ?? '';
$nom = $_SESSION['nom'] ?? '';
$role = $_SESSION['role'] ?? '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mes discussions - Forum</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        
        .wrap {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 15px;
        }
        
        .page-header {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        @media (min-width: 768px) {
            .page-header {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
        }
        
        .header-content h1 {
            margin: 0 0 5px 0;
            color: #2c3e50;
            font-size: 24px;
        }
        
        .page-description {
            margin: 0;
            color: #666;
            font-size: 14px;
        }
        
        .header-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            border: 1px solid transparent;
        }
        
        .btn-primary {
            background-color: #4a90e2;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #3a7bc8;
        }
        
        .btn-outline {
            background-color: transparent;
            border-color: #4a90e2;
            color: #4a90e2;
        }
        
        .btn-outline:hover {
            background-color: rgba(74, 144, 226, 0.1);
        }
        
        .btn i {
            margin-right: 6px;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    
    <div class="wrap">
        <div class="page-header">
            <div class="header-content">
                <h1><i class="fas fa-comments"></i> Mes discussions</h1>
                <p class="page-description">Gérez vos publications et suivez vos interactions sur le forum</p>
            </div>
            <div class="header-actions">
                <a href="forum.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nouvelle discussion
                </a>
                <a href="mes_reponses.php" class="btn btn-outline">
                    <i class="fas fa-reply"></i> Mes réponses
                </a>
            </div>
        </div>
    </div>
</body>
</html>
