<?php
declare(strict_types=1);

// Définir le titre de la page
$pageTitle = 'Mes réponses';

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

// Récupérer les réponses de l'utilisateur
$userReplies = $rRepo->findByUser($userId);

// Fonction pour obtenir le nom complet d'un utilisateur
function getUserFullName($userId, $userRepo) {
    try {
        $user = $userRepo->getUserById($userId);
        if ($user && method_exists($user, 'getPrenom') && method_exists($user, 'getNom')) {
            return trim($user->getPrenom() . ' ' . $user->getNom());
        }
    } catch (\Throwable $e) {}
    return 'Utilisateur #' . $userId;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mes réponses - Forum</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/site.css">
    <style>
        .wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            font-family: 'Inter', sans-serif;
        }
        
        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 2rem;
            font-weight: 600;
        }
        
        .panel {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            padding: 20px;
            border: 1px solid #e1e4e8;
        }
        
        .meta {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 10px;
            border-bottom: 1px solid #eaecef;
            padding-bottom: 10px;
        }
        
        .discussion-title {
            font-size: 1.25rem;
            color: #24292e;
            margin: 15px 0;
            font-weight: 600;
        }
        
        .discussion-content {
            line-height: 1.6;
            color: #24292e;
            margin-bottom: 15px;
        }
        
        .discussion-actions {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eaecef;
        }
        
        .button {
            display: inline-block;
            padding: 8px 16px;
            background-color: #2b6cb0;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: background-color 0.2s;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
        }
        
        .button:hover {
            background-color: #2c5282;
            color: white;
            text-decoration: none;
        }
        
        .no-discussions {
            text-align: center;
            padding: 40px 20px;
            background: #f8f9fa;
            border-radius: 8px;
            color: #6c757d;
        }
        
        .no-discussions p {
            margin-bottom: 20px;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #2b6cb0;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .reply-count {
            display: inline-block;
            background: #e9ecef;
            color: #495057;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.85rem;
            margin-left: 10px;
        }
        
        .replies-list {
            margin-top: 15px;
            padding-left: 20px;
            border-left: 2px solid #eaecef;
        }
        
        .reply-item {
            margin-bottom: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 6px;
        }
        
        .reply-meta {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 5px;
        }
        
        .reply-content {
            color: #24292e;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    
    <div class="wrap">
        <a href="mes_discussions.php" class="back-link">← Retour à mes discussions</a>
        <h1>Mes réponses</h1>
        
        <?php if (empty($userReplies)): ?>
            <div class="no-discussions">
                <p>Vous n'avez pas encore répondu à des discussions.</p>
                <a href="forum.php" class="button">Voir le forum</a>
            </div>
        <?php else: ?>
            <?php 
            // Debug: Afficher le nombre de réponses
            error_log("Nombre de réponses à traiter: " . count($userReplies));
            
            // Grouper les réponses par post pour éviter les doublons
            $repliesByPost = [];
            foreach ($userReplies as $index => $reply) {
                // Debug: Afficher les informations de la réponse
                error_log("Traitement de la réponse #" . ($index + 1) . ":");
                error_log("- ID: " . $reply->getIdReply());
                error_log("- Post ID: " . $reply->getPostId());
                error_log("- Contenu: " . $reply->getContenue());
                
                // Vérifier que la méthode getPostId() existe et retourne une valeur valide
                if (!method_exists($reply, 'getPostId')) {
                    error_log("- Erreur: La méthode getPostId() n'existe pas");
                    continue;
                }
                
                $postId = $reply->getPostId();
                
                // Vérifier que l'ID du post est valide
                if ($postId === null || !is_numeric($postId)) {
                    error_log("- Erreur: ID de post invalide: " . var_export($postId, true));
                    continue; // Passer à la réponse suivante si l'ID du post est invalide
                }
                
                $postId = (int)$postId; // S'assurer que c'est un entier
                
                if (!isset($repliesByPost[$postId])) {
                    error_log("- Recherche du post avec l'ID: " . $postId);
                    $post = $pRepo->find($postId);
                    
                    // Debug: Afficher si le post a été trouvé
                    error_log("- Post trouvé: " . ($post ? 'Oui' : 'Non'));
                    
                    // Ne pas ajouter si le post n'existe plus
                    if ($post) {
                        $repliesByPost[$postId] = [
                            'post' => $post,
                            'replies' => []
                        ];
                        error_log("- Post ajouté au tableau repliesByPost");
                    } else {
                        error_log("- Le post avec l'ID $postId n'existe pas, réponse ignorée");
                    }
                }
                
                // Ne pas ajouter la réponse si le post n'existe pas
                if (isset($repliesByPost[$postId])) {
                    $repliesByPost[$postId]['replies'][] = $reply;
                    error_log("- Réponse ajoutée au post ID $postId");
                } else {
                    error_log("- Le post ID $postId n'existe pas, réponse non ajoutée");
                }
            }
            
            // Trier les discussions par date de la dernière réponse (plus récente en premier)
            uasort($repliesByPost, function($a, $b) {
                $aDate = $a['replies'][0]->getDateCreation();
                $bDate = $b['replies'][0]->getDateCreation();
                return strtotime($bDate) - strtotime($aDate);
            });
            ?>
            
            <?php foreach ($repliesByPost as $postId => $data): 
                $post = $data['post'];
                $replies = $data['replies'];
                if (!$post) continue; // Si le post n'existe plus, on passe
                
                // Trier les réponses par date décroissante
                usort($replies, function($a, $b) {
                    return strtotime($b->getDateCreation()) - strtotime($a->getDateCreation());
                });
                
                $lastReply = $replies[0]; // La plus récente
            ?>
                <div class="panel">
                    <div class="meta">
                        <span class="reply-count"><?= count($replies) > 1 ? count($replies) . ' réponses' : '1 réponse' ?></span>
                        Dernière activité : 
                        <?php $lastReplyDate = $lastReply->getDateCreation(); ?>
                        <?php if ($lastReplyDate): ?>
                            <?= date('d/m/Y à H:i', strtotime($lastReplyDate)) ?>
                        <?php else: ?>
                            Date inconnue
                        <?php endif; ?>
                    </div>
                    
                    <h3 class="discussion-title">
                        <a href="forum.php?canal=<?= urlencode($post->getCanal()) ?>#post-<?= $post->getIdPost() ?>">
                            <?= htmlspecialchars($post->getTitre()) ?>
                        </a>
                    </h3>
                    
                    <div class="replies-list">
                        <?php foreach (array_slice($replies, 0, 3) as $reply): ?>
                            <div class="reply-item">
                                <div class="reply-meta">
                                    Réponse du <?= date('d/m/Y à H:i', strtotime($reply->getDateCreation())) ?>
                                </div>
                                <div class="reply-content">
                                    <?= nl2br(htmlspecialchars(mb_substr($reply->getContenue(), 0, 200) . (mb_strlen($reply->getContenue()) > 200 ? '...' : ''))) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if (count($replies) > 3): ?>
                            <div style="margin-top: 10px; font-style: italic; color: #6c757d;">
                                + <?= count($replies) - 3 ?> autres réponses...
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="discussion-actions">
                        <a href="forum.php?canal=<?= urlencode($post->getCanal()) ?>#post-<?= $post->getIdPost() ?>" class="button">
                            Voir la discussion complète
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
