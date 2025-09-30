<?php
namespace repository;
use modele\RForum;

class RForumRepo {
    private string $table = 'replies_general';

    public function forPost(int $post_id): array {
        $db = \bdd();
        $st = $db->prepare("SELECT id AS idRForum, post_id, author, content AS contenue, created_at AS dateCreation FROM {$this->table} WHERE post_id = ? ORDER BY created_at ASC");
        $st->execute([$post_id]);
        $rows = $st->fetchAll(\PDO::FETCH_ASSOC);
        return array_map(fn($r)=> new RForum($r), $rows);
    }

    public function create(int $post_id, string $author, string $contenue): RForum {
        $db = \bdd();
        $st = $db->prepare("INSERT INTO {$this->table}(post_id,author,content) VALUES(?,?,?)");
        $st->execute([$post_id,$author,$contenue]);
        $id = (int)$db->lastInsertId();
        $st = $db->prepare("SELECT id AS idRForum, post_id, author, content AS contenue, created_at AS dateCreation FROM {$this->table} WHERE id = ?");
        $st->execute([$id]);
        $r = $st->fetch(\PDO::FETCH_ASSOC);
        return new RForum($r);
    }
}
