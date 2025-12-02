<?php
namespace bdd;

use PDO;
use PDOException;
class Bdd {
    private ?PDO $pdo = null;

    private const DB_NAME = 'tplprs';

    public function getBdd(): PDO {
        if ($this->pdo !== null) {
            return $this->pdo;
        }

        // Paramètres communs
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        // 🔥 Tentative 1 : MAMP (Mac)
        try {
            $this->pdo = new PDO(
                'mysql:host=127.0.0.1;port=8889;dbname=' . self::DB_NAME . ';charset=utf8mb4',
                'root',
                'root',
                $options
            );
            return $this->pdo;
        }
        catch (PDOException $e) {
            // Si MAMP n'est pas accessible → on essaie WAMP
        }

        // 🔥 Tentative 2 : WAMP (Windows)
        try {
            $this->pdo = new PDO(
                'mysql:host=127.0.0.1;port=3306;dbname=' . self::DB_NAME . ';charset=utf8mb4',
                'root',
                '',
                $options
            );
            return $this->pdo;
        }
        catch (PDOException $e) {
            die("Impossible de se connecter à la base de données : " . $e->getMessage());
        }
    }
}