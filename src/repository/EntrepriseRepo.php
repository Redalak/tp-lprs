<?php

namespace repository;

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Entreprise.php'; // inclusion du modèle Entreprise

use modele\Entreprise;
use bdd\Bdd;

class EntrepriseRepo
{
    /**
     * Ajout d'une entreprise
     */
    public function ajoutEntreprise(Entreprise $entreprise) {
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
            'date_inscription' => $entreprise->getDateInscription(), // S'assurer que c'est une date valide (ex: Y-m-d H:i:s)
            'ref_offre' => $entreprise->getRefOffre()
        ]);

        // Optionnel : récupérer l'ID inséré et le setter sur l'objet
        // $entreprise->setIdEntreprise($database->lastInsertId());

        return $entreprise;
    }

    /**
     * Modification d'une entreprise
     */
    public function modifEntreprise(Entreprise $entreprise) {
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
    public function suppEntreprise(int $idEntreprise) {
        $bdd = new Bdd();
        $database = $bdd->getBdd();
        $req = $database->prepare('DELETE FROM entreprise WHERE id_entreprise = :id_entreprise');
        $req->execute(['id_entreprise' => $idEntreprise]);
    }

    /**
     * Liste de toutes les entreprises
     */
    public function listeEntreprise() {
        $bdd = new Bdd();
        $database = $bdd->getBdd();
        $req = $database->query('SELECT * FROM entreprise ORDER BY date_inscription DESC');
        $rows = $req->fetchAll(\PDO::FETCH_ASSOC);

        $entreprises = [];
        foreach ($rows as $row) {
            // Correspondance entre les colonnes BDD (snake_case)
            // et les clés attendues par le constructeur du modèle (camelCase, comme dans l'exemple EventRepo)
            $entreprises[] = new Entreprise([
                'idEntreprise'      => $row['id_entreprise'],
                'nom'               => $row['nom'],
                'adresse'           => $row['adresse'],
                'siteWeb'           => $row['site_web'],
                'motifPartenariat'  => $row['motif_partenariat'],
                'dateInscription'   => $row['date_inscription'],
                'refOffre'          => $row['ref_offre']
            ]);
        }
        return $entreprises;
    }

    /**
     * Récupérer une entreprise par son ID
     */
    public function getEntrepriseById(int $idEntreprise) {
        $bdd = new Bdd();
        $database = $bdd->getBdd();
        $req = $database->prepare('SELECT * FROM entreprise WHERE id_entreprise = :id_entreprise LIMIT 1');
        $req->execute(['id_entreprise' => $idEntreprise]);
        $row = $req->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null; // Si non trouvée
        }

        return new Entreprise([
            'idEntreprise'      => $row['id_entreprise'],
            'nom'               => $row['nom'],
            'adresse'           => $row['adresse'],
            'siteWeb'           => $row['site_web'],
            'motifPartenariat'  => $row['motif_partenariat'],
            'dateInscription'   => $row['date_inscription'],
            'refOffre'          => $row['ref_offre']
        ]);
    }
}