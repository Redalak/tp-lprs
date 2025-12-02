<?php

function bdd(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    $bdd = new \bdd\Bdd();
    $pdo = $bdd->getBdd();
    return $pdo;
}