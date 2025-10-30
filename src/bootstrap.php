<?php
use PDO;
use PDOException;

function bdd(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    $db   = getenv('DB_NAME') ?: 'tp_lprs';
    $host = getenv('DB_HOST') ?: '127.0.0.1';

    // Si tu as défini des variables d'env, on les prend en priorité
    if (getenv('DB_PORT') || getenv('DB_USER') || getenv('DB_PASS')) {
        $candidates[] = [
            'port' => (int)(getenv('DB_PORT') ?: 3306),
            'user' => getenv('DB_USER') ?: 'root',
            'pass' => getenv('DB_PASS') ?: '',
        ];
    } else {
        $candidates = [];
        if (PHP_OS_FAMILY === 'Darwin') {               // macOS → MAMP d'abord
            $candidates[] = ['port'=>8889, 'user'=>'root', 'pass'=>'root']; // MAMP
            $candidates[] = ['port'=>3306, 'user'=>'root', 'pass'=>'root']; // MySQL standard
        } else {                                        // Windows → WAMP d'abord
            $candidates[] = ['port'=>3306, 'user'=>'root', 'pass'=>''];     // WAMP (par défaut)
            $candidates[] = ['port'=>3308, 'user'=>'root', 'pass'=>''];     // WAMP alternatif
            $candidates[] = ['port'=>3306, 'user'=>'root', 'pass'=>'root']; // autre config
        }
    }

    $errors = [];
    foreach ($candidates as $c) {
        try {
            $dsn = "mysql:host=$host;port={$c['port']};dbname=$db;charset=utf8mb4";
            $pdo = new PDO($dsn, $c['user'], $c['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
            return $pdo;
        } catch (PDOException $e) {
            $errors[] = "port {$c['port']}: ".$e->getMessage();
        }
    }

    throw new RuntimeException("Connexion DB impossible ($host/$db) → ".implode(' | ', $errors));
}