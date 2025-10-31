<?php
namespace bdd;

use PDO;
use PDOException;
class Bdd {
    private $pdo = null;
    const DB_NAME = 'tplprs';
    public function getBdd(): PDO {
        if ($this->pdo instanceof PDO) {
            return $this->pdo;
        }

        try {
            $this->pdo = new PDO(
                'mysql:host=127.0.0.1;port=8889;dbname=' . self::DB_NAME . ';charset=utf8mb4',// mamp pour moi
                'root',
                'root',
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            // Fallback WAMP (port 3306, root/'' )
            $this->pdo = new PDO(
                'mysql:host=127.0.0.1;port=3306;dbname=' . self::DB_NAME . ';charset=utf8mb4', // wamp pour vous
                'root',
                '',
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // quand ya erreur ca lance une exeption et on peu try catch et voir pu sa plante
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        }

        return $this->pdo;
    }
}