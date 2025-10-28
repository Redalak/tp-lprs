<?php

namespace repository;

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/offre.php'; // inclusion du modèle Offre

use bdd\Bdd;
use modele\offre;

class OffreRepo
{
    // Ajout d'une offre
    public function ajoutOffre(offre $offre) {
        $bdd = new Bdd();
        $database = $bdd->getBdd();

        $req = $database->prepare('
            INSERT INTO offre (
                titre,
                rue,
                cp,
                ville,
                description,
                salaire,
                type_offre,
                etat
            ) VALUES (
                :titre,
                :rue,
                :cp,
                :ville,
                :description,
                :salaire,
                :type_offre,
                :etat
            )
        ');

        $req->execute([
            'titre'        => $offre->getTitre(),
            'rue'          => $offre->getRue(),
            'cp'           => $offre->getCp(),
            'ville'        => $offre->getVille(),
            'description'  => $offre->getDescription(),
            'salaire'      => ($offre->getSalaire() === '' ? null : $offre->getSalaire()),
            'type_offre'   => $offre->getTypeOffre(),
            'etat'         => $offre->getEtat(),
        ]);

        // si tu veux garder l'id dans l'objet
        if (method_exists($offre, 'setIdOffre')) {
            $offre->setIdOffre((int)$database->lastInsertId());
        }

        return $offre;
    }

    // Modification d'une offre
    public function modifOffre(offre $offre) {
        $bdd = new Bdd();
        $database = $bdd->getBdd();

        $req = $database->prepare('
            UPDATE offre
            SET
                titre = :titre,
                rue = :rue,
                cp = :cp,
                ville = :ville,
                description = :description,
                salaire = :salaire,
                type_offre = :type_offre,
                etat = :etat,
                ref_entreprise = :ref_entreprise
            WHERE id_offre = :id_offre
        ');

        $req->execute([
            'id_offre'       => $offre->getIdOffre(),
            'titre'          => $offre->getTitre(),
            'rue'            => $offre->getRue(),
            'cp'             => $offre->getCp(),
            'ville'          => $offre->getVille(),
            'description'    => $offre->getDescription(),
            'salaire'        => ($offre->getSalaire() === '' ? null : $offre->getSalaire()),
            'type_offre'     => $offre->getTypeOffre(),
            'etat'           => $offre->getEtat(),
            'ref_entreprise' => $offre->getRefEntreprise()
        ]);

        return $offre;
    }

    // Suppression d'une offre
    public function suppOffre(int $idOffre) {
        $bdd = new Bdd();
        $database = $bdd->getBdd();

        $req = $database->prepare('
            DELETE FROM offre
            WHERE id_offre = :id_offre
        ');

        $req->execute([
            'id_offre' => $idOffre
        ]);
    }

    // Liste des offres
    public function listeOffre() {
        $bdd = new Bdd();
        $database = $bdd->getBdd();

        $req = $database->query('
        SELECT *
        FROM offre
        ORDER BY date_creation DESC
    ');
        $rows = $req->fetchAll(\PDO::FETCH_ASSOC);

        $offres = [];
        foreach ($rows as $row) {

            // 1. On construit l'objet avec tout ce qu'on peut
            $o = new offre([
                'id_offre'       => $row['id_offre'],
                'titre'          => $row['titre'],
                'rue'            => $row['rue'],
                'cp'             => $row['cp'],
                'ville'          => $row['ville'],
                'description'    => $row['description'],
                'salaire'        => $row['salaire'],
                'type_offre'     => $row['type_offre'],
                'etat'           => $row['etat'],
                'date_creation'  => $row['date_creation'],
            ]);

            // 2. Ceinture + bretelles : on force les setters
            if (method_exists($o, 'setIdOffre')) {
                $o->setIdOffre($row['id_offre']);
            }
            if (method_exists($o, 'setSalaire')) {
                $o->setSalaire($row['salaire']);
            }
            if (method_exists($o, 'setTypeOffre')) {
                $o->setTypeOffre($row['type_offre']);
            }
            if (method_exists($o, 'setEtat')) {
                $o->setEtat($row['etat']);
            }
            if (method_exists($o, 'setDateCreation')) {
                $o->setDateCreation($row['date_creation']);
            }
            if (method_exists($o, 'setRue')) {
                $o->setRue($row['rue']);
            }
            if (method_exists($o, 'setCp')) {
                $o->setCp($row['cp']);
            }
            if (method_exists($o, 'setVille')) {
                $o->setVille($row['ville']);
            }
            if (method_exists($o, 'setDescription')) {
                $o->setDescription($row['description']);
            }
            if (method_exists($o, 'setTitre')) {
                $o->setTitre($row['titre']);
            }

            $offres[] = $o;
        }

        return $offres;
    }


    // Récupérer les dernières offres
    public function getDernieresOffres(int $limit = 5) {
        $bdd = new Bdd();
        $database = $bdd->getBdd();

        $stmt = $database->prepare('
            SELECT *
            FROM offre
            ORDER BY date_creation DESC
            LIMIT :limit
        ');
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $offres = [];
        foreach ($rows as $row) {
            $offres[] = new offre([
                'idOffre'        => $row['id_offre'],
                'titre'          => $row['titre'],
                'rue'            => $row['rue'],
                'cp'             => $row['cp'],
                'ville'          => $row['ville'],
                'description'    => $row['description'],
                'salaire'        => $row['salaire'],
                'typeOffre'      => $row['type_offre'],
                'dateCreation'   => $row['date_creation'],
                'etat'           => $row['etat'],
                'refEntreprise'  => $row['ref_entreprise']
            ]);
        }

        return $offres;
    }
}
