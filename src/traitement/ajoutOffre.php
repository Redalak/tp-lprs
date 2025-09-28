<?php
namespace repository;

require_once __DIR__ . '/../bdd/bdd.php';
require_once __DIR__ . '/../modele/offre.php';

use modele\offre;
use PDO;

class offreRepo
{
    /** @var string */
    private $table = '`offre`'; // ← si ta table est "offres", mets '`offres`'

    /** Liste brute */
    public function getAllRaw() {
        $db  = \bdd();
        $sql = "SELECT * FROM {$this->table} ORDER BY id_offre DESC";
        $req = $db->prepare($sql);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return offre|null */
    public function getModelById($id) {
        $db  = \bdd();
        $req = $db->prepare("
            SELECT
               id_offre      AS idOffre,
               titre         AS titre,
               description   AS description,
               mission       AS mission,
               salaire       AS salaire,
               type_offre    AS typeOffre,
               date_creation AS dateCreation,
               etat          AS etat
            FROM {$this->table}
            WHERE id_offre = :id
        ");
        $req->execute(['id' => (int)$id]);
        $row = $req->fetch(PDO::FETCH_ASSOC);
        return $row ? new offre($row) : null;
    }

    /** @return offre */
    public function ajoutOffre(offre $o) {
        $db  = \bdd();
        $req = $db->prepare("
            INSERT INTO {$this->table}
              (titre, description, mission, salaire, type_offre, etat)
            VALUES
              (:titre, :description, :mission, :salaire, :type_offre, :etat)
        ");
        $req->execute([
            'titre'       => $o->getTitre(),
            'description' => $o->getDescription(),
            'mission'     => $o->getMission(),
            'salaire'     => ($o->getSalaire() === '' ? null : $o->getSalaire()),
            'type_offre'  => $o->getTypeOffre(),
            'etat'        => $o->getEtat(),
        ]);
        $o->setIdOffre((int)$db->lastInsertId());
        return $o;
    }

    /** @return offre */
    public function modifOffre(offre $o) {
        $db  = \bdd();
        $req = $db->prepare("
            UPDATE {$this->table}
            SET titre=:titre,
                description=:description,
                mission=:mission,
                salaire=:salaire,
                type_offre=:type_offre,
                etat=:etat
            WHERE id_offre=:id_offre
        ");
        $req->execute([
            'id_offre'    => $o->getIdOffre(),
            'titre'       => $o->getTitre(),
            'description' => $o->getDescription(),
            'mission'     => $o->getMission(),
            'salaire'     => ($o->getSalaire() === '' ? null : $o->getSalaire()),
            'type_offre'  => $o->getTypeOffre(),
            'etat'        => $o->getEtat(),
        ]);
        return $o;
    }

    public function suppOffre($id) {
        $db  = \bdd();
        $req = $db->prepare("DELETE FROM {$this->table} WHERE id_offre = :id");
        $req->execute(['id' => (int)$id]);
    }
}
