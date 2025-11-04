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
                etat,
                ref_entreprise
            ) VALUES (
                :titre,
                :rue,
                :cp,
                :ville,
                :description,
                :salaire,
                :type_offre,
                :etat,
                :ref_entreprise
            )
        ');

        $req->execute([
            'titre'        => $offre->getTitre(),
            'rue'          => $offre->getRue(),
            'cp'           => $offre->getCp(),
            'ville'        => $offre->getVille(),
            'description'  => $offre->getDescription(),
            'salaire'      => ($offre->getSalaire() === '' ? null : $offre->getSalaire()),
            'type_offre'     => $offre->getTypeOffre(),
            'etat'           => $offre->getEtat(),
            'ref_entreprise' => $offre->getRefEntreprise()
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
            SELECT o.*, e.nom as entreprise_nom 
            FROM offre o
            LEFT JOIN entreprise e ON o.ref_entreprise = e.id_entreprise
            WHERE o.etat NOT IN ("ferme", "brouillon")
            ORDER BY o.date_creation DESC
        ');
        $rows = $req->fetchAll(\PDO::FETCH_ASSOC);

        $offres = [];
        foreach ($rows as $row) {
            // Adapter la clé SQL vers la propriété du modèle
            if (isset($row['id_offre'])) {
                $row['idOffre'] = $row['id_offre'];
                unset($row['id_offre']);
            }
            
            // Ajouter le nom de l'entreprise comme propriété dynamique
            $offre = new offre($row);
            if (isset($row['entreprise_nom'])) {
                $offre->entreprise_nom = $row['entreprise_nom'];
            }
            
            $offres[] = $offre;
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
    public function getDernieresOffres(int $limit = 3) {
        $bdd = new \bdd\Bdd();
        $database = $bdd->getBdd();

        // On prend les dernières offres créées qui ne sont ni fermées ni en brouillon
        $stmt = $database->prepare('
            SELECT o.*, e.nom as entreprise_nom
            FROM offre o
            LEFT JOIN entreprise e ON o.ref_entreprise = e.id_entreprise
            WHERE o.etat NOT IN ("ferme", "brouillon")
            ORDER BY o.date_creation DESC
            LIMIT :limite
        ');

        // LIMIT doit être bindé en entier sinon MySQL râle
        $stmt->bindValue(':limite', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $offres = [];

        foreach ($rows as $row) {
            // On crée l'objet offre à partir de la ligne BDD
            $o = new \modele\offre($row);
            if (isset($row['entreprise_nom'])) {
                $o->entreprise_nom = $row['entreprise_nom'];
            }
            $offres[] = $o;
        }

        return $offres;
    }

}
