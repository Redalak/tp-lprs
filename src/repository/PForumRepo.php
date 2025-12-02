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
            'canal' => $this->first($names, ['canal', 'channel', 'categorie']),
        ];
    }

    private function first(array $names, array $candidates): string {
        foreach ($candidates as $c) {
            if (in_array($c, $names, true)) return $c;
        }
        // Dernier recours : première colonne existante pour éviter l'erreur SQL
        return $names[0] ?? '';
    }

    public function all(string $canal = null): array {
        $this->detect();
        $pdo = $this->pdo();
        $order = $this->cols['date'] ?: $this->cols['id'];
        
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if ($canal !== null) {
            $sql .= " WHERE `{$this->cols['canal']}` = :canal";
            $params[':canal'] = $canal;
        }
        
        $sql .= " ORDER BY `{$order}` DESC";
        
        $st = $pdo->prepare($sql);
        $st->execute($params);
        $rows = $st->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function ($r) {
            return new PForum([
                'idPost'       => $r[$this->cols['id']]   ?? null,
                'refUser'      => $r[$this->cols['user']] ?? null,
                'titre'        => $r[$this->cols['title']]?? '',
                'contenue'     => $r[$this->cols['body']] ?? '',
                'dateCreation' => $r[$this->cols['date']] ?? null,
                'canal'        => $r[$this->cols['canal']] ?? 'general',
            ]);
        }, $rows);
    }

    public function find(int $id): ?PForum {
        $this->detect();
        $pdo = $this->pdo();
        
        $st = $pdo->prepare("SELECT * FROM {$this->table} WHERE `{$this->cols['id']}` = ?");
        $st->execute([$id]);
        $r = $st->fetch(\PDO::FETCH_ASSOC);

        if (!$r) return null;

        return new PForum([
            'idPost'       => $r[$this->cols['id']]   ?? null,
            'refUser'      => $r[$this->cols['user']] ?? null,
            'titre'        => $r[$this->cols['title']]?? '',
            'contenue'     => $r[$this->cols['body']] ?? '',
            'dateCreation' => $r[$this->cols['date']] ?? null,
            'canal'        => $r[$this->cols['canal']] ?? 'general',
        ]);
    }

    public function create(int $refUser, string $titre, string $contenue, string $canal = 'general'): PForum {
        $this->detect();
        $pdo = $this->pdo();
        
        // Vérifier si le canal est valide
        $canauxValides = ['general', 'alumni_entreprises', 'etudiants_professeurs'];
        if (!in_array($canal, $canauxValides)) {
            $canal = 'general';
        }
        
        $st = $pdo->prepare(
            "INSERT INTO {$this->table} (`{$this->cols['user']}`, `{$this->cols['title']}`, `{$this->cols['body']}`, `{$this->cols['canal']}`)
             VALUES (?, ?, ?, ?)"
        );
        $st->execute([$refUser, $titre, $contenue, $canal]);
        $id = (int)$pdo->lastInsertId();
        return $this->find($id);
    }
    
    /**
     * Récupère les messages d'un canal spécifique
     */
    public function findByCanal(string $canal): array {
        return $this->all($canal);
    }
    
    /**
     * Vérifie si un utilisateur a la permission de poster dans un canal
     */
    public function canPostInCanal(string $role, string $canal): bool {
        // Règles de permission par canal
        $permissions = [
            'alumni_entreprises' => ['alumni', 'entreprise'],
            'etudiants_professeurs' => ['etudiant', 'prof'], // Les étudiants et les professeurs peuvent créer des posts
            'general' => ['etudiant', 'prof', 'alumni', 'entreprise', 'admin']
        ];
        
        // Si le canal n'existe pas, on ne permet pas la création
        if (!isset($permissions[$canal])) {
            return false;
        }
        
        // Vérifier si le rôle de l'utilisateur est autorisé dans ce canal
        return in_array($role, $permissions[$canal]);
    }
    
    /**
     * Vérifie si un utilisateur peut voir un canal spécifique
     */
    public function canViewCanal(string $role, string $canal): bool {
        // Définir quels rôles peuvent voir quels canaux
        $canauxParRole = [
            'general' => ['etudiant', 'prof', 'alumni', 'entreprise', 'admin'],
            'alumni_entreprises' => ['alumni', 'entreprise', 'admin'],
            'etudiants_professeurs' => ['etudiant', 'prof', 'admin']
        ];
        
        // Vérifier si le canal existe et si le rôle peut le voir
        return isset($canauxParRole[$canal]) && in_array($role, $canauxParRole[$canal]);
    }

    /**
     * Met à jour un post existant
     * @param int $postId ID du post à mettre à jour
     * @param int $userId ID de l'utilisateur qui effectue la mise à jour
     * @param string $titre Nouveau titre du post
     * @param string $contenu Nouveau contenu du post
     * @param string $role Rôle de l'utilisateur (optionnel, pour les admins)
     * @return bool True si la mise à jour a réussi, false sinon
     */
    public function update(int $postId, int $userId, string $titre, string $contenu, string $role = ''): bool {
        $this->detect();
        $pdo = $this->pdo();
        
        // Préparer la requête de base
        $sql = "UPDATE {$this->table} 
                SET `{$this->cols['title']}` = ?, 
                    `{$this->cols['body']}` = ?
                WHERE `{$this->cols['id']}` = ?";
        
        $params = [$titre, $contenu, $postId];
        
        // Si l'utilisateur n'est pas admin, on vérifie qu'il est l'auteur du post
        if ($role !== 'admin') {
            $sql .= " AND `{$this->cols['user']}` = ?";
            $params[] = $userId;
        }
        
        $st = $pdo->prepare($sql);
        return $st->execute($params);
    }

    /**
     * Supprime un post et toutes ses réponses
     * @param int $postId ID du post à supprimer
     * @param int $userId ID de l'utilisateur qui effectue la suppression
     * @param string $role Rôle de l'utilisateur (optionnel, pour les admins)
     * @return bool True si la suppression a réussi, false sinon
     */
    public function delete(int $postId, int $userId, string $role = ''): bool {
        $this->detect();
        $pdo = $this->pdo();
        
        // Si l'utilisateur n'est pas admin, on vérifie qu'il est l'auteur du post
        if ($role !== 'admin') {
            $st = $pdo->prepare("SELECT COUNT(*) FROM {$this->table} WHERE `{$this->cols['id']}` = ? AND `{$this->cols['user']}` = ?");
            $st->execute([$postId, $userId]);
            
            if ($st->fetchColumn() === 0) {
                return false; // L'utilisateur n'est pas autorisé à supprimer ce post
            }
        }
        
        // Supprimer les réponses associées
        $rRepo = new RForumRepo();
        $rRepo->deleteByPostId($postId);
        
        // Supprimer le post
        $st = $pdo->prepare("DELETE FROM {$this->table} WHERE `{$this->cols['id']}` = ?");
        return $st->execute([$postId]);
    }
    
    /**
     * Récupère les posts d'un utilisateur spécifique
     * @param int $userId ID de l'utilisateur
     * @return array Tableau d'objets PForum
     */
    public function findByUser(int $userId): array {
        $this->detect();
        $pdo = $this->pdo();
        $order = $this->cols['date'] ?: $this->cols['id'];
        
        $sql = "SELECT * FROM {$this->table} WHERE {$this->cols['user']} = :userId ORDER BY {$order} DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':userId', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        
        $posts = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $posts[] = new \modele\PForum($row);
        }
        
        return $posts;
    }
}