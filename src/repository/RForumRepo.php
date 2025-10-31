<?php
namespace repository;

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/RForum.php';

use modele\RForum;

class RForumRepo {
    private string $table = 'r_forum';

    private function pdo(): \PDO {
        $bdd = new \bdd\Bdd();
        return $bdd->getBdd();
    }

    private const PK      = 'id_r_forum';
    private const POST    = 'ref_post';
    private const USER    = 'ref_user';
    private const BODY    = 'contenue';
    private const CREATED = 'date_creation';

    public function forPost(int $postId): array {
        $pdo = $this->pdo();
        $st = $pdo->prepare(
            "SELECT `".self::PK."` AS idReponse,
                    `".self::POST."` AS refPost,
                    `".self::USER."` AS refUser,
                    `".self::BODY."` AS contenue,
                    `".self::CREATED."` AS dateCreation
             FROM {$this->table}
             WHERE `".self::POST."` = ?
             ORDER BY `".self::CREATED."` ASC"
        );
        $st->execute([$postId]);
        $rows = $st->fetchAll(\PDO::FETCH_ASSOC);
        return array_map(fn($r) => new RForum($r), $rows);
    }

    public function create(int $postId, int $userId, string $contenu): int {
        $pdo = $this->pdo();
        $st  = $pdo->prepare(
            "INSERT INTO {$this->table} (`".self::POST."`,`".self::USER."`,`".self::BODY."`)
             VALUES (?,?,?)"
        );
        $st->execute([$postId, $userId, $contenu]);
        return (int)$pdo->lastInsertId();
    }

    public function find(int $id): ?RForum {
        $pdo = $this->pdo();
        $st = $pdo->prepare(
            "SELECT `".self::PK."` AS idReponse,
                    `".self::USER."` AS refUser,
                    `".self::BODY."` AS contenue,
                    `".self::CREATED."` AS dateCreation
             FROM {$this->table}
             WHERE `".self::PK."` = ?"
        );
        $st->execute([$id]);
        $r = $st->fetch(\PDO::FETCH_ASSOC);
        return $r ? new RForum($r) : null;
    }
}