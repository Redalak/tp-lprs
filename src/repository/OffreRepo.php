<?php

namespace repository;

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/offre.php';

use bdd\Bdd;
use modele\offre;

class OffreRepo
{
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
            'type_offre'   => $offre->getTypeOffre(), // <-- DOIT être là
            'etat'         => $offre->getEtat(),      // <-- DOIT être là
        ]);

        if (method_exists($offre, 'setIdOffre')) {
            $offre->setIdOffre((int)$database->lastInsertId());
        }

        return $offre;
    }

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
            // on recrée un objet offre à partir de la ligne SQL
            $o = new offre($row);
            $offres[] = $o;
        }

        return $offres;
    }

    public function suppOffre(int $idOffre) {
        $bdd = new Bdd();
        $database = $bdd->getBdd();

        $req = $database->prepare('DELETE FROM offre WHERE id_offre = :id_offre');
        $req->execute(['id_offre' => $idOffre]);
    }

    public function modifOffre(offre $offre) {
        $bdd = new Bdd();
        $database = $bdd->getBdd();

        $req = $database->prepare('
            UPDATE offre
            SET
                titre        = :titre,
                rue          = :rue,
                cp           = :cp,
                ville        = :ville,
                description  = :description,
                salaire      = :salaire,
                type_offre   = :type_offre,
                etat         = :etat
            WHERE id_offre = :id_offre
        ');

        $req->execute([
            'id_offre'     => $offre->getIdOffre(),
            'titre'        => $offre->getTitre(),
            'rue'          => $offre->getRue(),
            'cp'           => $offre->getCp(),
            'ville'        => $offre->getVille(),
            'description'  => $offre->getDescription(),
            'salaire'      => ($offre->getSalaire() === '' ? null : $offre->getSalaire()),
            'type_offre'   => $offre->getTypeOffre(),
            'etat'         => $offre->getEtat(),
        ]);

        return $offre;
    }
}
