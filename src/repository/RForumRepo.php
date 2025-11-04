<?php
namespace repository;

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/RForum.php';

use modele\RForum;

class RForumRepo
{
    private string $table = 'r_forum';
    private ?array $cols = null;

    private function pdo(): \PDO
    {
        $bdd = new \bdd\Bdd();
        return $bdd->getBdd();
    }

    /** Détecte les vrais noms de colonnes de r_forum. */
    private function detect(): void
    {
        if ($this->cols !== null) return;

        $pdo = $this->pdo();
        $fields = $pdo->query("SHOW COLUMNS FROM {$this->table}")->fetchAll(\PDO::FETCH_ASSOC);
        $names = array_map(fn($f) => $f['Field'], $fields);

        // Détection sûre de la colonne parent (optionnelle)
        $parentCandidates = ['ref_parent', 'parent_id', 'id_parent', 'reply_to', 'ref_reply', 'ref_r_forum'];
        $parentCol = '';
        foreach ($parentCandidates as $c) {
            if (in_array($c, $names, true)) {
                $parentCol = $c;
                break;
            }
        }

        $this->cols = [
            'id' => $this->first($names, ['id_reponse_forum', 'id_r_forum', 'id', 'id_reply']),
            'post' => $this->first($names, ['ref_post_forum', 'ref_post', 'post_id', 'id_post']),
            'parent' => $parentCol, // vide si absent dans le schéma
            'user' => $this->first($names, ['ref_user', 'user_id', 'author', 'id_user']),
            'body' => $this->first($names, ['contenue', 'contenu', 'content', 'message', 'body']),
            'date' => $this->first($names, ['date_creation', 'created_at', 'createdAt', 'date_create']),
        ];
    }

    private function first(array $names, array $cands): string
    {
        foreach ($cands as $c) if (in_array($c, $names, true)) return $c;
        return $names[0] ?? '';
    }

    public function forPost(int $postId): array
    {
        $this->detect();
        $pdo = $this->pdo();
        $order = $this->cols['date'] ?: $this->cols['id'];
        $st = $pdo->prepare(
            "SELECT * FROM {$this->table}
             WHERE `{$this->cols['post']}` = ?
             ORDER BY `{$order}` ASC"
        );
        $st->execute([$postId]);
        $rows = $st->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function ($r) {
            return new RForum([
                'id_reply' => $r[$this->cols['id']] ?? null,
                'post_id' => $r[$this->cols['post']] ?? null,
                'parent_id' => ($this->cols['parent'] ?? '') !== '' ? ($r[$this->cols['parent']] ?? null) : null,
                'ref_user' => $r[$this->cols['user']] ?? null,
                'contenue' => $r[$this->cols['body']] ?? '',
                'date_creation' => $r[$this->cols['date']] ?? null,
            ]);
        }, $rows);
    }

    public function create(int $postId, int $userId, string $contenu, ?int $parentId = null): int
    {
        $this->detect();
        $pdo = $this->pdo();
        $columns = ["`{$this->cols['post']}`", "`{$this->cols['user']}`", "`{$this->cols['body']}`"];
        $params = [$postId, $userId, $contenu];
        if (!empty($this->cols['parent']) && $parentId !== null) {
            $columns[] = "`{$this->cols['parent']}`";
            $params[] = $parentId;
        }
        $placeholders = rtrim(str_repeat('?,', count($columns)), ',');
        $sql = "INSERT INTO {$this->table} (" . implode(',', $columns) . ") VALUES ($placeholders)";
        $st = $pdo->prepare($sql);
        $st->execute($params);
        return (int)$pdo->lastInsertId();
    }

    public function find(int $id): ?RForum
    {
        $this->detect();
        $pdo = $this->pdo();
        $st = $pdo->prepare(
            "SELECT * FROM {$this->table}
             WHERE `{$this->cols['id']}` = ?"
        );
        $st->execute([$id]);
        $r = $st->fetch(\PDO::FETCH_ASSOC);

        return $r ? new RForum([
            'id_reply' => $r[$this->cols['id']] ?? null,
            'post_id' => $r[$this->cols['post']] ?? null,
            'parent_id' => ($this->cols['parent'] ?? '') !== '' ? ($r[$this->cols['parent']] ?? null) : null,
            'ref_user' => $r[$this->cols['user']] ?? null,
            'contenue' => $r[$this->cols['body']] ?? '',
            'date_creation' => $r[$this->cols['date']] ?? null,
        ]) : null;
    }

    /**
     * Met à jour une réponse existante
     */
    public function update(int $replyId, int $userId, string $contenu): bool
    {
        $this->detect();
        $pdo = $this->pdo();

        $st = $pdo->prepare(
            "UPDATE {$this->table} 
             SET `{$this->cols['body']}` = ?
             WHERE `{$this->cols['id']}` = ? AND `{$this->cols['user']}` = ?"
        );

        return $st->execute([$contenu, $replyId, $userId]);
    }

    /**
     * Supprime une réponse
     */
    public function delete(int $replyId, int $userId): bool
    {
        $this->detect();
        $pdo = $this->pdo();

        // Vérifier que l'utilisateur est bien l'auteur de la réponse
        $st = $pdo->prepare("SELECT COUNT(*) FROM {$this->table} WHERE `{$this->cols['id']}` = ? AND `{$this->cols['user']}` = ?");
        $st->execute([$replyId, $userId]);

        if ($st->fetchColumn() === 0) {
            return false;
        }

        // Supprimer les réponses enfants (si elles existent)
        $this->deleteReplies($replyId);

        // Supprimer la réponse
        $st = $pdo->prepare("DELETE FROM {$this->table} WHERE `{$this->cols['id']}` = ?");
        return $st->execute([$replyId]);
    }

    /**
     * Supprime toutes les réponses d'un post
     */
    public function deleteByPostId(int $postId): void
    {
        $this->detect();
        $pdo = $this->pdo();
        $st = $pdo->prepare("DELETE FROM {$this->table} WHERE `{$this->cols['post']}` = ?");
        $st->execute([$postId]);
    }

    /**
     * Supprime les réponses enfants d'une réponse
     */
    private function deleteReplies(int $parentId): void
    {
        if (empty($this->cols['parent'])) return; // Pas de colonne parent, pas de hiérarchie

        $this->detect();
        $pdo = $this->pdo();

        // Récupérer les IDs des réponses enfants
        $st = $pdo->prepare("SELECT `{$this->cols['id']}` FROM {$this->table} WHERE `{$this->cols['parent']}` = ?");
        $st->execute([$parentId]);
        $children = $st->fetchAll(\PDO::FETCH_COLUMN);

        // Supprimer les récursivement
        foreach ($children as $childId) {
            $this->deleteReplies($childId);
            $st = $pdo->prepare("DELETE FROM {$this->table} WHERE `{$this->cols['id']}` = ?");
            $st->execute([$childId]);
        }
    }
}