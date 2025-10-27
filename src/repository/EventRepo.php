<?php

namespace repository;

require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Event.php'; // inclusion du modèle Event

use modele\Event;
use bdd\Bdd;

class EventRepo
{
    // Ajout d'un événement
    public function ajoutEvent(Event $event) {
        $bdd = new Bdd();
        $database = $bdd->getBdd();
        $req = $database->prepare('INSERT INTO event (type, titre, description, lieu, nombre_place, date_event, etat, ref_user) VALUES (:type, :titre, :description, :lieu, :nombre_place, :date_event, :etat, :ref_user)');
        $req->execute([
            'type' => $event->getType(),
            'titre' => $event->getTitre(),
            'description' => $event->getDescription(),
            'lieu' => $event->getLieu(),
            'nombre_place' => $event->getNombrePlace(),
            'date_event' => $event->getDateEvent(),
            'etat' => $event->getEtat(),
            'ref_user' => $event->getRefUser()
        ]);
        return $event;
    }

    // Modification d'un événement
    public function modifEvent(Event $event) {
        $bdd = new Bdd();
        $database = $bdd->getBdd();
        $req = $database->prepare('UPDATE event 
            SET type = :type, 
                titre = :titre,
                description = :description,
                lieu = :lieu,
                nombre_place = :nombre_place,
                date_event = :date_event,
                etat = :etat,
                ref_user = :ref_user
            WHERE id_evenement = :id_evenement');

        $req->execute([
            'id_evenement' => $event->getIdEvent(),
            'type' => $event->getType(),
            'titre' => $event->getTitre(),
            'description' => $event->getDescription(),
            'lieu' => $event->getLieu(),
            'nombre_place' => $event->getNombrePlace(),
            'date_event' => $event->getDateEvent(),
            'etat' => $event->getEtat(),
            'ref_user' => $event->getRefUser()
        ]);

        return $event;
    }

    // Suppression d'un événement
    public function suppEvent(int $idEvent) {
        $bdd = new Bdd();
        $database = $bdd->getBdd();
        $req = $database->prepare('DELETE FROM event WHERE id_evenement = :id_evenement');
        $req->execute(['id_evenement' => $idEvent]);
    }

    // Liste des événements
    public function listeEvent() {
        $bdd = new Bdd();
        $database = $bdd->getBdd();
        $req = $database->query('SELECT * FROM event ORDER BY date_event DESC');
        $rows = $req->fetchAll(\PDO::FETCH_ASSOC);

        $events = [];
        foreach ($rows as $row) {
            $events[] = new Event([
                'idEvent'       => $row['id_evenement'],
                'type'          => $row['type'],
                'titre'         => $row['titre'],
                'description'   => $row['description'],
                'lieu'          => $row['lieu'],
                'nombrePlace'   => $row['nombre_place'],
                'dateEvent'     => $row['date_event'],
                'etat'          => $row['etat'],
                'ref_user'      => $row['ref_user']
            ]);
        }
        return $events;
    }

    // --- Nouvelle méthode : récupérer les derniers événements ---
    public function getDerniersEvents(int $limit = 5) {
        $bdd = new Bdd();
        $database = $bdd->getBdd();
        $stmt = $database->prepare('SELECT * FROM event ORDER BY date_event DESC LIMIT :limit');
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $events = [];
        foreach ($rows as $row) {
            $events[] = new Event([
                'idEvent'       => $row['id_evenement'],
                'type'          => $row['type'],
                'titre'         => $row['titre'],
                'description'   => $row['description'],
                'lieu'          => $row['lieu'],
                'nombrePlace'   => $row['nombre_place'],
                'dateEvent'     => $row['date_event'],
                'etat'          => $row['etat'],
                'ref_user'      => $row['ref_user']
            ]);
        }
        return $events;
    }
}
