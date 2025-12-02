<?php

namespace repository;

require_once __DIR__ . '/../bdd/Bdd.php';

use bdd\Bdd;

class InscriptionEventRepo
{
    // Vérifie si une table existe dans la base de données
    private function tableExists($database, $tableName)
    {
        $result = $database->query("SHOW TABLES LIKE '$tableName'");
        return $result->rowCount() > 0;
    }


    // Vérifie si un utilisateur est déjà inscrit à un événement
    /**
     * Récupère la liste des participants à un événement
     * @param int $idEvenement ID de l'événement
     * @return array Tableau des utilisateurs inscrits à l'événement
     */
    public function getParticipantsEvenement(int $idEvenement): array
    {
        $bdd = new Bdd();
        $database = $bdd->getBdd();

        $req = $database->prepare('
            SELECT u.id_user, u.nom, u.prenom, u.email, u.role,
                   CASE 
                       WHEN u.role LIKE "%CC%" THEN "Utilisateur"
                       WHEN u.role LIKE "%DC%" THEN "Utilisateur"
                       ELSE COALESCE(NULLIF(TRIM(u.role), ""), "Utilisateur")
                   END as role_clean
            FROM inscription_evenement ie
            JOIN user u ON ie.ref_user = u.id_user
            WHERE ie.ref_evenement = :id_evenement
            ORDER BY u.nom, u.prenom
        ');
        $req->execute(['id_evenement' => $idEvenement]);
        
        $participants = $req->fetchAll(\PDO::FETCH_ASSOC);
        
        // Nettoyer les rôles des préfixes indésirables
        foreach ($participants as &$participant) {
            if (isset($participant['role_clean'])) {
                $participant['role'] = $participant['role_clean'];
                unset($participant['role_clean']);
            }
            // Nettoyage supplémentaire au cas où
            $participant['role'] = preg_replace('/^(CC|DC)\s*/i', '', $participant['role']);
            $participant['role'] = trim($participant['role']);
            if (empty($participant['role'])) {
                $participant['role'] = 'Utilisateur';
            }
        }
        
        return $participants;
    }
    
    /**
     * Supprime un participant d'un événement
     * @param int $idEvenement ID de l'événement
     * @param int $idUtilisateur ID de l'utilisateur à supprimer
     * @return bool True si la suppression a réussi, false sinon
     */
    public function supprimerParticipant(int $idEvenement, int $idUtilisateur): bool
    {
        try {
            $bdd = new Bdd();
            $database = $bdd->getBdd();
            
            $req = $database->prepare('DELETE FROM inscription_evenement WHERE ref_evenement = :id_evenement AND ref_user = :id_utilisateur');
            return $req->execute([
                'id_evenement' => $idEvenement,
                'id_utilisateur' => $idUtilisateur
            ]);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la suppression du participant : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifie si un utilisateur est déjà inscrit à un événement
     * @param int $idUtilisateur ID de l'utilisateur
     * @param int $idEvenement ID de l'événement
     * @return bool True si l'utilisateur est déjà inscrit, false sinon
     */
    public function estInscrit(int $idUtilisateur, int $idEvenement): bool
    {
        error_log("Vérification de l'inscription - User: $idUtilisateur, Event: $idEvenement");

        $bdd = new Bdd();
        $database = $bdd->getBdd();

        $req = $database->prepare('SELECT COUNT(*) FROM inscription_evenement WHERE ref_user = :id_utilisateur AND ref_evenement = :id_evenement');
        $req->execute([
            'id_utilisateur' => $idUtilisateur,
            'id_evenement' => $idEvenement
        ]);

        $count = $req->fetchColumn();
        error_log("Nombre d'inscriptions trouvées: " . $count);

        return $count > 0;
    }

    // Inscrit un utilisateur à un événement
    public function inscrireUtilisateur(int $idUtilisateur, int $idEvenement): bool
    {
        error_log("Début de la méthode inscrireUtilisateur - User: $idUtilisateur, Event: $idEvenement");

        $bdd = new Bdd();
        $database = $bdd->getBdd();

        // Vérifier si la table existe, sinon la créer
        if (!$this->tableExists($database, 'inscription_evenement')) {
            error_log("La table inscription_evenement n'existe pas, tentative de création...");
            if (!$this->createTableIfNotExists($database)) {
                error_log("Échec de la création de la table inscription_evenement");
                return false;
            }
            error_log("Table inscription_evenement créée avec succès");
        }

        // Vérifier d'abord si l'utilisateur n'est pas déjà inscrit
        if ($this->estInscrit($idUtilisateur, $idEvenement)) {
            error_log("L'utilisateur $idUtilisateur est déjà inscrit à l'événement $idEvenement");
            return false;
        }

        try {
            error_log("Début de la transaction");
            $database->beginTransaction();

            // Vérifier s'il reste des places disponibles
            $req = $database->prepare('SELECT nombre_place, titre FROM event WHERE id_evenement = :id_evenement');
            $req->execute(['id_evenement' => $idEvenement]);
            $event = $req->fetch();

            if (!$event) {
                error_log("Événement $idEvenement non trouvé");
                $database->rollBack();
                return false;
            }

            error_log("Événement trouvé: " . $event['titre'] . ", places disponibles: " . $event['nombre_place']);

            if ($event['nombre_place'] <= 0) {
                error_log("Plus de places disponibles pour l'événement $idEvenement");
                $database->rollBack();
                return false;
            }

            // Décrémenter le nombre de places disponibles
            $req = $database->prepare('UPDATE event SET nombre_place = nombre_place - 1 WHERE id_evenement = :id_evenement AND nombre_place > 0');
            $req->execute(['id_evenement' => $idEvenement]);
            $rowsAffected = $req->rowCount();

            if ($rowsAffected === 0) {
                error_log("Échec de la mise à jour du nombre de places pour l'événement $idEvenement");
                return false;
            }

            error_log("Nombre de places mises à jour avec succès pour l'événement $idEvenement");

            // Créer l'inscription avec une gestion d'erreur plus détaillée
            try {
                $req = $database->prepare('INSERT INTO inscription_evenement (ref_user, ref_evenement, date_inscription) VALUES (:id_utilisateur, :id_evenement, NOW())');
                $result = $req->execute([
                    'id_utilisateur' => $idUtilisateur,
                    'id_evenement' => $idEvenement
                ]);

                if (!$result) {
                    $errorInfo = $req->errorInfo();
                    error_log("Erreur lors de l'insertion de l'inscription: " . print_r($errorInfo, true));

                    // Vérifier si l'erreur est due à une contrainte d'unicité (déjà inscrit)
                    if (isset($errorInfo[1]) && $errorInfo[1] == 1062) {
                        error_log("Erreur 1062: L'utilisateur est déjà inscrit à cet événement");
                        $database->rollBack();
                        return false;
                    }

                    $database->rollBack();
                    return false;
                }

                $database->commit();
                error_log("Inscription réussie pour l'utilisateur $idUtilisateur à l'événement $idEvenement");
                return true;

            } catch (\PDOException $e) {
                error_log("Exception PDO lors de l'insertion: " . $e->getMessage());
                error_log("Code d'erreur: " . $e->getCode());

                // Si l'erreur est due à une contrainte d'unicité (déjà inscrit)
                if ($e->getCode() == '23000') {
                    error_log("Erreur 23000: L'utilisateur est déjà inscrit à cet événement");
                    $database->rollBack();
                    return false;
                }

                throw $e; // Relancer l'exception pour qu'elle soit capturée par le bloc catch externe
            }

        } catch (\Exception $e) {
            if (isset($database) && $database->inTransaction()) {
                $database->rollBack();
            }
            error_log("Erreur dans inscrireUtilisateur: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Récupère les réservations d'un utilisateur
     * @param int $userId ID de l'utilisateur
     * @return array Tableau des réservations avec les détails des événements
     */
    /**
     * Récupère les réservations d'un utilisateur
     * @param int $userId ID de l'utilisateur
     * @param bool $inclurePasses Si vrai, inclut les événements passés (par défaut: false)
     * @return array Tableau des réservations avec les détails des événements
     */
    public function getReservationsByUser(int $userId, bool $inclurePasses = false): array
    {
        $bdd = new Bdd();
        $database = $bdd->getBdd();

        $sql = 'SELECT i.*, e.titre, e.description, e.lieu, e.date_event, e.type 
                FROM inscription_evenement i 
                JOIN event e ON i.ref_evenement = e.id_evenement 
                WHERE i.ref_user = :user_id';
        
        // Ajouter la condition pour exclure les événements passés si demandé
        if (!$inclurePasses) {
            $sql .= ' AND e.date_event >= CURDATE()';
        }
        
        $sql .= ' ORDER BY e.date_event ASC'; // Du plus proche au plus éloigné
        
        $req = $database->prepare($sql);
        $req->execute(['user_id' => $userId]);

        $result = [];
        while ($row = $req->fetch(\PDO::FETCH_ASSOC)) {
            $result[] = [
                'id_inscription' => $row['id_inscription'],
                'ref_user' => $row['ref_user'],
                'ref_evenement' => $row['ref_evenement'],
                'date_inscription' => $row['date_inscription'],
                'id_evenement' => $row['ref_evenement'],
                'titre' => $row['titre'],
                'description' => $row['description'],
                'lieu' => $row['lieu'],
                'date_event' => $row['date_event'],
                'type' => $row['type']
            ];
        }

        return $result;
    }

    /**
     * Annule la participation d'un utilisateur à un événement
     * @param int $idUtilisateur ID de l'utilisateur
     * @param int $idEvenement ID de l'événement
     * @return bool True si l'annulation a réussi, false sinon
     */
    public function annulerParticipation(int $idUtilisateur, int $idEvenement): bool
    {
        error_log("Début de l'annulation de la participation - User: $idUtilisateur, Event: $idEvenement");

        $bdd = new Bdd();
        $database = $bdd->getBdd();

        try {
            $database->beginTransaction();

            // 1. Vérifier que l'inscription existe
            $req = $database->prepare('SELECT * FROM inscription_evenement WHERE ref_user = :user_id AND ref_evenement = :event_id');
            $req->execute([
                'user_id' => $idUtilisateur,
                'event_id' => $idEvenement
            ]);

            if ($req->rowCount() === 0) {
                error_log("Aucune inscription trouvée pour cet utilisateur et cet événement");
                $database->rollBack();
                return false;
            }

            // 2. Supprimer l'inscription
            $req = $database->prepare('DELETE FROM inscription_evenement WHERE ref_user = :user_id AND ref_evenement = :event_id');
            $result = $req->execute([
                'user_id' => $idUtilisateur,
                'event_id' => $idEvenement
            ]);

            if (!$result) {
                error_log("Échec de la suppression de l'inscription");
                $database->rollBack();
                return false;
            }

            // 3. Incrémenter le nombre de places disponibles
            $req = $database->prepare('UPDATE event SET nombre_place = nombre_place + 1 WHERE id_evenement = :event_id');
            $result = $req->execute(['event_id' => $idEvenement]);

            if (!$result) {
                error_log("Échec de la mise à jour du nombre de places");
                $database->rollBack();
                return false;
            }

            $database->commit();
            error_log("Participation annulée avec succès");
            return true;

        } catch (\Exception $e) {
            if (isset($database) && $database->inTransaction()) {
                $database->rollBack();
            }
            error_log("Erreur lors de l'annulation de la participation: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupère le nombre de places disponibles pour un événement
     * @param int $idEvenement ID de l'événement
     * @return array Tableau contenant le nombre de places disponibles et le statut
     */
    public function getPlacesDisponibles(int $idEvenement): array
    {
        $bdd = new Bdd();
        $database = $bdd->getBdd();
        
        // Récupérer le nombre de places total et le nombre d'inscriptions
        $req = $database->prepare('
            SELECT 
                e.nombre_place as places_totales,
                COUNT(i.id_inscription) as places_prises
            FROM event e
            LEFT JOIN inscription_evenement i ON e.id_evenement = i.ref_evenement
            WHERE e.id_evenement = :id_evenement
            GROUP BY e.id_evenement
        ');
        $req->execute(['id_evenement' => $idEvenement]);
        $result = $req->fetch(\PDO::FETCH_ASSOC);
        
        if (!$result) {
            return [
                'disponibles' => 0,
                'statut' => 'inconnu',
                'total' => 0,
                'pris' => 0
            ];
        }
        
        $placesDisponibles = $result['places_totales'] - $result['places_prises'];
        
        // Déterminer le statut
        $statut = 'disponible';
        if ($placesDisponibles <= 0) {
            $statut = 'complet';
        } elseif ($placesDisponibles <= 5) {
            $statut = 'bientot_complet';
        }
        
        return [
            'disponibles' => $placesDisponibles,
            'statut' => $statut,
            'total' => $result['places_totales'],
            'pris' => $result['places_prises']
        ];
    }
}
