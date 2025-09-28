<?php
namespace repository;

require_once __DIR__ . '/../bdd/bdd.php';
require_once __DIR__ . '/../modele/event.php';

use modele\event;
use PDO;

class eventRepo
{
    /** @var string */
    private $table = '`event`'; // IMPORTANT : backticks car EVENT est un mot réservé MySQL

    /** Liste brute pour la page */
    public function getAllRaw() {
        $db  = \bdd(); // DB par défaut = Tplprs (cf. bdd.php)
        $sql = "SELECT * FROM {$this->table} ORDER BY id_evenement DESC";
        $req = $db->prepare($sql);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @param int $id @return event|null */
    public function getModelById($id) {
        $db  = \bdd();
        $req = $db->prepare("
            SELECT 
                id_evenement   AS idEvent,
                type           AS type,
                titre          AS titre,
                description    AS description,
                lieu           AS lieu,
                element_requis AS elementRequis,
                nombre_place   AS nombrePlace,
                date_creation  AS dateCreation,
                etat           AS etat
            FROM {$this->table}
            WHERE id_evenement = :id
        ");
        $req->execute(['id' => (int)$id]);
        $row = $req->fetch(PDO::FETCH_ASSOC);
        return $row ? new event($row) : null;
    }

    /** @param event $e @return event */
    public function ajoutEvent(event $e) {
        $db  = \bdd();
        $req = $db->prepare("
            INSERT INTO {$this->table}
                (type, titre, description, lieu, element_requis, nombre_place, etat)
            VALUES
                (:type, :titre, :description, :lieu, :element_requis, :nombre_place, :etat)
        ");
        $req->execute([
            'type'            => $e->getType(),
            'titre'           => $e->getTitre(),
            'description'     => $e->getDescription(),
            'lieu'            => $e->getLieu(),
            'element_requis'  => $e->getElementRequis(),
            'nombre_place'    => $e->getNombrePlace(),
            'etat'            => $e->getEtat(),
        ]);
        $e->setIdEvent((int)$db->lastInsertId());
        return $e;
    }

    /** @param event $e @return event */
    public function modifEvent(event $e) {
        $db  = \bdd();
        $req = $db->prepare("
            UPDATE {$this->table}
            SET type = :type,
                titre = :titre,
                description = :description,
                lieu = :lieu,
                element_requis = :element_requis,
                nombre_place = :nombre_place,
                etat = :etat
            WHERE id_evenement = :id_evenement
        ");
        $req->execute([
            'id_evenement'   => $e->getIdEvent(),
            'type'           => $e->getType(),
            'titre'          => $e->getTitre(),
            'description'    => $e->getDescription(),
            'lieu'           => $e->getLieu(),
            'element_requis' => $e->getElementRequis(),
            'nombre_place'   => $e->getNombrePlace(),
            'etat'           => $e->getEtat(),
        ]);
        return $e;
    }

    /** @param int $id */
    public function suppEvent($id) {
        $db  = \bdd();
        $req = $db->prepare("DELETE FROM {$this->table} WHERE id_evenement = :id");
        $req->execute(['id' => (int)$id]);
    }
}
