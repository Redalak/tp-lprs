<?php
namespace repository;

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/PForum.php';

use modele\PForum;

class PForumRepo {
    private string $table = 'p_forum';
    private ?array $cols = null;

    private function pdo(): \PDO {
        $bdd = new \bdd\Bdd();
        return $bdd->getBdd();
    }

    /** Detecte les vrais noms de colonnes (tolérant : id / id_p_forum / ...). */
    private function detect(): void {
        if ($this->cols !== null) return;

        $pdo    = $this->pdo();
        $fields = $pdo->query("SHOW COLUMNS FROM {$this->table}")->fetchAll(\PDO::FETCH_ASSOC);
        $names  = array_map(fn($f) => $f['Field'], $fields);

        $this->cols = [
            'id'    => $this->first($names, ['id_p_forum', 'id', 'id_post', 'idp_forum']),
            'user'  => $this->first($names, ['ref_user', 'user_id', 'author', 'id_user']),
            'title' => $this->first($names, ['titre', 'title']),
            'body'  => $this->first($names, ['contenue', 'contenu', 'content', 'message', 'body']),
            'date'  => $this->first($names, ['date_creation', 'created_at', 'createdAt', 'date_create']),
        ];
    }

    private function first(array $names, array $candidates): string {
        foreach ($candidates as $c) {
            if (in_array($c, $names, true)) return $c;
        }
        // Dernier recours : première colonne existante pour éviter l'erreur SQL
        return $names[0] ?? '';
    }

    public function all(): array {
        $this->detect();
        $pdo   = $this->pdo();
        $order = $this->cols['date'] ?: $this->cols['id'];
        $q = $pdo->query("SELECT * FROM {$this->table} ORDER BY `{$order}` DESC");
        $rows = $q->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function ($r) {
            return new PForum([
                'idPost'       => $r[$this->cols['id']]   ?? null,
                'refUser'      => $r[$this->cols['user']] ?? null,
                'titre'        => $r[$this->cols['title']]?? '',
                'contenue'     => $r[$this->cols['body']] ?? '',
                'dateCreation' => $r[$this->cols['date']] ?? null,
            ]);
        }, $rows);
    }

    public function find(int $id): ?PForum {
        $this->detect();
        $pdo = $this->pdo();
        $st  = $pdo->prepare("SELECT * FROM {$this->table} WHERE `{$this->cols['id']}` = ?");
        $st->execute([$id]);
        $r = $st->fetch(\PDO::FETCH_ASSOC);

        if (!$r) return null;

        return new PForum([
            'idPost'       => $r[$this->cols['id']]   ?? null,
            'refUser'      => $r[$this->cols['user']] ?? null,
            'titre'        => $r[$this->cols['title']]?? '',
            'contenue'     => $r[$this->cols['body']] ?? '',
            'dateCreation' => $r[$this->cols['date']] ?? null,
        ]);
    }

    public function create(int $refUser, string $titre, string $contenue): PForum {
        $this->detect();
        $pdo = $this->pdo();
        $st  = $pdo->prepare(
            "INSERT INTO {$this->table} (`{$this->cols['user']}`, `{$this->cols['title']}`, `{$this->cols['body']}`)
             VALUES (?,?,?)"
        );
        $st->execute([$refUser, $titre, $contenue]);
        $id = (int)$pdo->lastInsertId();
        return $this->find($id);
    }
}