<?php

namespace repository;

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Formation.php';

use bdd\Bdd;
use modele\formation; // le modèle est déclaré en minuscules

class FormationRepo
{
    /**
     * Retourne toutes les formations
     * @return formation[]
     */
    public function all(): array {
        $bdd = new Bdd();
        $db  = $bdd->getBdd();
        $rows = $db->query('SELECT id_formation, nom_formation, description FROM formation ORDER BY id_formation DESC')
                   ->fetchAll(\PDO::FETCH_ASSOC);
        return array_map(function(array $r){
            return new formation([
                'idformation'  => $r['id_formation'] ?? null,
                'nomformation' => $r['nom_formation'] ?? '',
                'description'  => $r['description'] ?? null,
            ]);
        }, $rows);
    }

    /**
     * Récupère une formation par ID
     */
    public function find(int $id): ?formation {
        $bdd = new Bdd();
        $db  = $bdd->getBdd();
        $st = $db->prepare('SELECT id_formation, nom_formation, description FROM formation WHERE id_formation = :id');
        $st->execute(['id' => $id]);
        $r = $st->fetch(\PDO::FETCH_ASSOC);
        if (!$r) return null;
        return new formation([
            'idformation'  => $r['id_formation'] ?? null,
            'nomformation' => $r['nom_formation'] ?? '',
            'description'  => $r['description'] ?? null,
        ]);
    }

    /**
     * Ajoute une formation, retourne l'ID créé
     */
    public function create(string $nom, ?string $description = null): int {
        $bdd = new Bdd();
        $db  = $bdd->getBdd();
        try {
            $st = $db->prepare('INSERT INTO formation (nom_formation, description) VALUES (:nom, :description)');
            $st->execute(['nom' => $nom, 'description' => $description]);
        } catch (\PDOException $e) {
            // Fallback si la colonne description n'est pas présente
            $st = $db->prepare('INSERT INTO formation (nom_formation) VALUES (:nom)');
            $st->execute(['nom' => $nom]);
        }
        return (int)$db->lastInsertId();
    }

    /**
     * Met à jour le nom d'une formation
     */
    public function update(int $id, string $nom, ?string $description = null): bool {
        $bdd = new Bdd();
        $db  = $bdd->getBdd();
        try {
            $st = $db->prepare('UPDATE formation SET nom_formation = :nom, description = :description WHERE id_formation = :id');
            return $st->execute(['nom' => $nom, 'description' => $description, 'id' => $id]);
        } catch (\PDOException $e) {
            $st = $db->prepare('UPDATE formation SET nom_formation = :nom WHERE id_formation = :id');
            return $st->execute(['nom' => $nom, 'id' => $id]);
        }
    }

    /**
     * Supprime une formation
     */
    public function delete(int $id): bool {
        $bdd = new Bdd();
        $db  = $bdd->getBdd();
        $st = $db->prepare('DELETE FROM formation WHERE id_formation = :id');
        return $st->execute(['id' => $id]);
    }

    // ==== Méthodes orientées objet (getters/setters) ====

    /**
     * Ajoute une formation à partir d'un objet formation
     * Retourne l'ID créé
     */
    public function ajoutFormation(formation $f): int {
        $nom = method_exists($f, 'getNomformation') ? $f->getNomformation() : '';
        $description = method_exists($f, 'getDescription') ? $f->getDescription() : null;
        return $this->create($nom, $description);
    }

    /**
     * Met à jour une formation à partir d'un objet formation
     */
    public function modifFormation(formation $f): bool {
        $id  = method_exists($f, 'getIdformation') ? (int)$f->getIdformation() : 0;
        $nom = method_exists($f, 'getNomformation') ? $f->getNomformation() : '';
        $description = method_exists($f, 'getDescription') ? $f->getDescription() : null;
        if ($id <= 0 || $nom === '') return false;
        return $this->update($id, $nom, $description);
    }

    /**
     * Supprime une formation par ID
     */
    public function suppFormation(int $id): bool {
        return $this->delete($id);
    }

    /**
     * Liste des formations (alias all)
     * @return formation[]
     */
    public function listeFormation(): array {
        return $this->all();
    }
}