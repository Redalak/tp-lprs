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
    }

    /**
     * Supprime un événement par son ID
     * @param int $idEvent ID de l'événement à supprimer
     */
    public function suppEvent(int $idEvent) {
        $bdd = new Bdd();
        $database = $bdd->getBdd();
        $req = $database->prepare('DELETE FROM event WHERE id_evenement = :id_evenement');
        $req->execute(['id_evenement' => $idEvent]);
    }

    /**
     * Récupère la liste des événements
     * @param bool $inclurePasses Si vrai, inclut les événements passés (par défaut: false)
     * @return array Tableau d'objets Event
     */
    /**
     * Alias de listeEvent() pour la rétrocompatibilité
     * @deprecated Utiliser listeEvent() à la place
     */
    public function getTousLesEvenements() {
        return $this->listeEvent(false);
    }
    
    /**
     * Récupère la liste des événements
     * @param bool $inclurePasses Si vrai, inclut les événements passés (par défaut: false)
     * @return array Tableau d'objets Event
     */
    public function listeEvent(bool $inclurePasses = false) {
        $bdd = new Bdd();
        $database = $bdd->getBdd();
        
        $sql = 'SELECT * FROM event';
        
        // Ajouter la condition pour exclure les événements passés si demandé
        if (!$inclurePasses) {
            $sql .= ' WHERE date_event >= CURDATE()';
        }
        
        $sql .= ' ORDER BY date_event ASC'; // Du plus proche au plus éloigné
        
        $req = $database->query($sql);
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
    /**
     * Récupère un événement par son ID
     * @param int $idEvent ID de l'événement
     * @return Event|null L'événement ou null si non trouvé
     */
    public function getEvenementById(int $idEvent): ?Event {
        $bdd = new Bdd();
        $database = $bdd->getBdd();
        
        $req = $database->prepare('SELECT * FROM event WHERE id_evenement = :id_evenement');
        $req->execute(['id_evenement' => $idEvent]);
        
        $row = $req->fetch(\PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        return new Event([
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
    
    /**
     * Récupère les derniers événements
     * @param int $limit Nombre d'événements à récupérer
     * @return array Tableau d'objets Event
     */
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
