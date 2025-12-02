<?php

namespace helpers;

class AuthHelper
{
    /**
     * Vérifie si l'utilisateur est connecté
     */
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Vérifie si l'utilisateur a un rôle spécifique
     */
    public static function hasRole(string $role): bool
    {
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }

    /**
     * Exige un rôle spécifique, sinon redirige
     */
    public static function requireRole(string $role, string $redirect = 'index.php'): void
    {
        if (!self::isLoggedIn() || !self::hasRole($role)) {
            $_SESSION['error'] = 'Accès non autorisé. Cette section est réservée aux ' . $role . 's.';
            header('Location: ' . $redirect);
            exit();
        }
    }

    /**
     * Exige que l'utilisateur soit connecté, sinon redirige
     */
    public static function requireLogin(string $redirect = 'connexion.php'): void
    {
        if (!self::isLoggedIn()) {
            $_SESSION['error'] = 'Veuillez vous connecter pour accéder à cette page.';
            header('Location: ' . $redirect);
            exit();
        }
    }

    /**
     * Vérifie si l'utilisateur est propriétaire de l'offre
     */
    public static function isOwnerOfOffer(int $offerId, \PDO $pdo): bool
    {
        if (!self::hasRole('entreprise')) {
            return false;
        }

        $stmt = $pdo->prepare('
            SELECT COUNT(*) 
            FROM offre o
            JOIN user u ON o.ref_entreprise = u.ref_entreprise
            WHERE o.id_offre = :offerId AND u.id_user = :userId
        ');
        
        $stmt->execute([
            'offerId' => $offerId,
            'userId' => $_SESSION['user_id']
        ]);
        
        return $stmt->fetchColumn() > 0;
    }
}
