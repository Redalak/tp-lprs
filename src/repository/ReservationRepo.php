<?php
namespace repository;

require_once __DIR__ . '/../bdd/Bdd.php';

use bdd\Bdd;

class ReservationRepo {

    private $db;

    public function __construct() {
        $bdd = new Bdd();
        $this->db = $bdd->getBdd();
    }

    public function dejaReserve($userId, $eventId) {
        $stmt = $this->db->prepare("SELECT * FROM reservation WHERE id_user = ? AND id_event = ?");
        $stmt->execute([$userId, $eventId]);
        return $stmt->fetch() !== false;
    }

    public function ajouterReservation($userId, $eventId) {
        $stmt = $this->db->prepare("INSERT INTO reservation (id_user, id_event) VALUES (?, ?)");
        return $stmt->execute([$userId, $eventId]);
    }
}
