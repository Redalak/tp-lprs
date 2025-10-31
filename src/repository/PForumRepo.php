<?php
namespace repository;

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/PForum.php';

use modele\PForum;

class PForumRepo {
    private string $table = 'p_forum';

    private function pdo(): \PDO {
        $bdd = new \bdd\Bdd();
        return $bdd->getBdd();
    }

    private const PK      = 'id_p_forum';
    private const USER    = 'ref_user';
    private const TITLE   = 'titre';
    private const BODY    = 'contenue';
    private const CREATED = 'date_creation';

    public function all(): array {
        $pdo = $this->pdo();
        $sql = "SELECT `".self::PK."` AS idPost,
                       `".self::USER."` AS refUser,
                       `".self::TITLE."` AS titre,
                       `".self::BODY."` AS contenue,
                       `".self::CREATED."` AS dateCreation
                FROM {$this->table}
                ORDER BY `".self::CREATED."` DESC";
        $rows = $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        return array_map(fn($r) => new PForum($r), $rows);
    }

    public function find(int $id): ?PForum {
        $pdo = $this->pdo();
        $st = $pdo->prepare(
            "SELECT `".self::PK."` AS idPost,
                    `".self::USER."` AS refUser,
                    `".self::TITLE."` AS titre,
                    `".self::BODY."` AS contenue,
                    `".self::CREATED."` AS dateCreation
             FROM {$this->table}
             WHERE `".self::PK."` = ?"
        );
        $st->execute([$id]);
        $r = $st->fetch(\PDO::FETCH_ASSOC);
        return $r ? new PForum($r) : null;
    }

    public function create(int $refUser, string $titre, string $contenue): PForum {
        $pdo = $this->pdo();
        $st  = $pdo->prepare(
            "INSERT INTO {$this->table} (`".self::USER."`,`".self::TITLE."`,`".self::BODY."`)
             VALUES (?,?,?)"
        );
        $st->execute([$refUser, $titre, $contenue]);
        return $this->find((int)$pdo->lastInsertId());
    }
}