<?php

namespace repository;

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Entreprise.php';
require_once __DIR__ . '/../modele/offre.php';

use modele\Entreprise;
use modele\offre;
use bdd\Bdd;

class EntrepriseRepo
{
    /**
     * Ajout d'une entreprise
     */
    public function ajoutEntreprise(Entreprise $entreprise)
    {
        $bdd = new Bdd();
        $database = $bdd->getBdd();
        $req = $database->prepare('
            INSERT INTO entreprise (nom, adresse, site_web, motif_partenariat, date_inscription, ref_offre) 
            VALUES (:nom, :adresse, :site_web, :motif_partenariat, :date_inscription, :ref_offre)
        ');

        $req->execute([
            'nom' => $entreprise->getNom(),
            'adresse' => $entreprise->getAdresse(),
            'site_web' => $entreprise->getSiteWeb(),
            'motif_partenariat' => $entreprise->getMotifPartenariat(),
            'date_inscription' => $entreprise->getDateInscription(),
            'ref_offre' => $entreprise->getRefOffre()
        ]);

        // Récupérer l'ID inséré
        $entreprise->setIdEntreprise($database->lastInsertId());

        return $entreprise;
    }

    /**
     * Modification d'une entreprise
     */
    public function modifEntreprise(Entreprise $entreprise)
    {
        $bdd = new Bdd();
        $database = $bdd->getBdd();
        $req = $database->prepare('
            UPDATE entreprise 
            SET nom = :nom, 
                adresse = :adresse,
                site_web = :site_web,
                motif_partenariat = :motif_partenariat,
                date_inscription = :date_inscription,
                ref_offre = :ref_offre
            WHERE id_entreprise = :id_entreprise
        ');

        $req->execute([
            'id_entreprise' => $entreprise->getIdEntreprise(),
            'nom' => $entreprise->getNom(),
            'adresse' => $entreprise->getAdresse(),
            'site_web' => $entreprise->getSiteWeb(),
            'motif_partenariat' => $entreprise->getMotifPartenariat(),
            'date_inscription' => $entreprise->getDateInscription(),
            'ref_offre' => $entreprise->getRefOffre()
        ]);

        return $entreprise;
    }

    /**
     * Suppression d'une entreprise par son ID
     */
    public function suppEntreprise(int $idEntreprise)
    {
        $bdd = new Bdd();
        $database = $bdd->getBdd();
        $req = $database->prepare('DELETE FROM entreprise WHERE id_entreprise = :id_entreprise');
        return $req->execute(['id_entreprise' => $idEntreprise]);
    }

    /**
     * Récupère la liste de toutes les entreprises avec le nombre d'offres
     */
    public function listeEntreprise()
    {
        $bdd = new Bdd();
        $database = $bdd->getBdd();

        $req = $database->query('
            SELECT e.*, 
                   (SELECT COUNT(*) FROM offre WHERE ref_entreprise = e.id_entreprise) as nombre_offres
            FROM entreprise e 
            ORDER BY e.nom
        ');

        $entreprises = [];
        while ($row = $req->fetch(\PDO::FETCH_ASSOC)) {
            $entreprise = new Entreprise([
                'idEntreprise' => $row['id_entreprise'],
                'nom' => $row['nom'],
                'adresse' => $row['adresse'],
                'siteWeb' => $row['site_web'],
                'motifPartenariat' => $row['motif_partenariat'],
                'dateInscription' => $row['date_inscription'],
                'refOffre' => $row['ref_offre']
            ]);
            $entreprise->nombre_offres = (int)$row['nombre_offres'];
            $entreprises[] = $entreprise;
        }

        return $entreprises;
    }

    /**
     * Récupère une entreprise par son ID
     */
    public function getEntrepriseById(int $id)
    {
        $bdd = new Bdd();
        $database = $bdd->getBdd();

        $req = $database->prepare('SELECT * FROM entreprise WHERE id_entreprise = :id');
        $req->execute(['id' => $id]);

        $row = $req->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Entreprise([
            'idEntreprise' => $row['id_entreprise'],
            'nom' => $row['nom'],
            'adresse' => $row['adresse'],
            'siteWeb' => $row['site_web'],
            'motifPartenariat' => $row['motif_partenariat'],
            'dateInscription' => $row['date_inscription'],
            'refOffre' => $row['ref_offre']
        ]);
    }

    /**
     * Récupère toutes les offres d'une entreprise
     */
    public function getOffresParEntreprise($id_entreprise)
    {
        $bdd = new Bdd();
        $database = $bdd->getBdd();

        $req = $database->prepare('SELECT * FROM offre WHERE ref_entreprise = :id_entreprise ORDER BY date_creation DESC');
        $req->execute(['id_entreprise' => $id_entreprise]);

        $offres = [];
        while ($row = $req->fetch(\PDO::FETCH_ASSOC)) {
            $offres[] = new offre($row);
        }

        return $offres;
    }
}
