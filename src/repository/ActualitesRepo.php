<?php

namespace repository;

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Actualites.php';

use modele\Actualites;
use bdd\Bdd as Bdd;

class ActualitesRepo
{
    /**
     * Récupère les 3 dernières actualités
     * @return array Tableau d'objets Actualites
     */
    public function getDernieresActualites()
    {
        try {
            $bdd = new Bdd();
            $database = $bdd->getBdd();
            
            $req = $database->query('SELECT * FROM actualites ORDER BY id_actu DESC LIMIT 3');
            
            $actualites = [];
            while ($row = $req->fetch(\PDO::FETCH_ASSOC)) {
                $actualites[] = new Actualites([
                    'id_actu' => $row['id_actu'],
                    'contexte' => $row['contexte']
                ]);
            }
            
            return $actualites;
        } catch (\PDOException $e) {
            error_log('Erreur lors de la récupération des actualités : ' . $e->getMessage());
            return [];
        }
        
        return $actualites;
    }
    
    /**
     * Récupère une actualité par son ID
     * @param int $id ID de l'actualité
     * @return Actualites|null L'actualité ou null si non trouvée
     */
    public function getActualiteById($id)
    {
        $bdd = new Bdd();
        $database = $bdd->getBdd();
        
        $req = $database->prepare('SELECT * FROM actualites WHERE id_actu = :id');
        $req->execute(['id' => $id]);
        
        $row = $req->fetch(\PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        return new Actualites([
            'id_actu' => $row['id_actu'],
            'contexte' => $row['contexte']
        ]);
    }
    
    /**
     * Ajoute une nouvelle actualité
     * @param Actualites $actualite L'actualité à ajouter
     * @return Actualites L'actualité avec son ID
     */
    public function ajoutActualite(Actualites $actualite)
    {
        $bdd = new Bdd();
        $database = $bdd->getBdd();
        
        $req = $database->prepare('INSERT INTO actualites (contexte) VALUES (:contexte)');
        
        $req->execute([
            'contexte' => $actualite->getContexte()
        ]);
        
        // Récupérer l'ID inséré
        $actualite->setIdActu($database->lastInsertId());
        
        return $actualite;
    }
    
    /**
     * Modifie une actualité existante
     * @param Actualites $actualite L'actualité à modifier
     * @return bool Succès de l'opération
     */
    public function modifActualite(Actualites $actualite)
    {
        $bdd = new Bdd();
        $database = $bdd->getBdd();
        
        $req = $database->prepare('UPDATE actualites SET contexte = :contexte WHERE id_actu = :id_actu');
        
        return $req->execute([
            'contexte' => $actualite->getContexte(),
            'id_actu' => $actualite->getIdActu()
        ]);
    }
    
    /**
     * Supprime une actualité
     * @param int $id ID de l'actualité à supprimer
     * @return bool Succès de l'opération
     */
    public function suppActualite($id)
    {
        $bdd = new Bdd();
        $database = $bdd->getBdd();
        
        $req = $database->prepare('DELETE FROM actualites WHERE id_actu = :id');
        return $req->execute(['id' => $id]);
    }
    
    /**
     * Récupère toutes les actualités
     * @return array Tableau d'objets Actualites
     */
    public function listeActualites()
    {
        $bdd = new Bdd();
        $database = $bdd->getBdd();
        
        $req = $database->query('SELECT * FROM actualites ORDER BY id_actu DESC');
        
        $actualites = [];
        while ($row = $req->fetch(\PDO::FETCH_ASSOC)) {
            $actualites[] = new Actualites([
                'id_actu' => $row['id_actu'],
                'contexte' => $row['contexte']
            ]);
        }
        
        return $actualites;
    }
}
