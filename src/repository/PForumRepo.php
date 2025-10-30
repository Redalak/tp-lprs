<?php
namespace repository;
use modele\PForum;
use bdd\Bdd;
class PForumRepo {
    private  $table = 'posts_general';

    public function all(): array {
        $db = new Bdd();
        $db = $db->getBdd();
        $q  = $db->query("SELECT id AS idPost, author, title AS titre, content AS contenue, created_at AS dateCreation FROM {$this->table} ORDER BY created_at DESC");
        $rows = $q->fetchAll(\PDO::FETCH_ASSOC);
        return array_map(fn($r)=> new PForum($r), $rows);
    }

    public function find(int $id): ?PForum {
        $db = new Bdd();
        $st = $db->prepare("SELECT id AS idPost, author, title AS titre, content AS contenue, created_at AS dateCreation FROM {$this->table} WHERE id = ?");
        $st->execute([$id]);
        $r = $st->fetch(\PDO::FETCH_ASSOC);
        return $r ? new PForum($r) : null;
    }

    public function create(string $author, string $titre, string $contenue): PForum {
        $db = new Bdd();
        $st = $db->prepare("INSERT INTO {$this->table}(author,title,content) VALUES(?,?,?)");
        $st->execute([$author,$titre,$contenue]);
        $id = (int)$db->lastInsertId();
        return $this->find($id);
    }
}
