<?php
namespace repository;
use modele\User;
require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/User.php';
use bdd\Bdd;


class UserRepo
{
    public function connexion(User $user)
    {
        $bdd = new Bdd();
        $database = $bdd->getBdd();

        $req = $database->prepare('SELECT * FROM user WHERE email = :email LIMIT 1');
        $req->execute(['email' => $user->getEmail()]);
        $row = $req->fetch();
        if (!$row) {
            return null;
        }

        return new User([
            'idUser' => $row['id_user'],
            'email' => $row['email'],
            'nom' => $row['nom'],
            'prenom' => $row['prenom'],
            'mdp' => $row['mdp'],
            'role' => $row['role'],
            'isApproved' => $row['is_approved'] ?? 0,
        ]);
    }

    /**
     * Approuve/Désapprouve un utilisateur
     */
    public function setApproval(int $idUser, bool $approved): bool
    {
        $bdd = new Bdd();
        $db = $bdd->getBdd();
        $req = $db->prepare('UPDATE user SET is_approved = :approuve WHERE id_user = :id');
        return $req->execute([
            'approuve' => $approved ? 1 : 0,
            'id' => $idUser,
        ]);
    }

    /**
     * Trouve un utilisateur par son ID
     */
    public function trouverParId($id)
    {
        $bdd = new Bdd();
        $database = $bdd->getBdd();
        $req = $database->prepare('SELECT * FROM user WHERE id_user = :id LIMIT 1');
        $req->execute(['id' => $id]);
        $row = $req->fetch();
        
        if (!$row) {
            return null;
        }
        
        return new User([
            'idUser' => $row['id_user'],
            'email' => $row['email'],
            'nom' => $row['nom'],
            'prenom' => $row['prenom'],
            'mdp' => $row['mdp'],
            'role' => $row['role'],
            'isApproved' => $row['is_approved'] ?? 0,
        ]);
    }
    
    public function inscription(User $user)
    {
        $bdd = new Bdd();
        $database = $bdd->getBdd();

        $req = $database->prepare("\n            INSERT INTO user(nom, prenom, email, mdp, role, is_approved) VALUES (:nom, :prenom, :email, :mdp, :role, :is_approved)");


        $req->execute([
            "nom" => $user->getNom(),
            "prenom" => $user->getPrenom(),
            "email" => $user->getEmail(),
            "mdp" => $user->getMdp(),
            "role" => $user->getRole(), // doit être 'admin' ou 'etudiant' etc.
            "is_approved" => method_exists($user,'getIsApproved') ? (int)$user->getIsApproved() : 0,
        ]);
        return $user;
    }

    public function nombreUtilisateur()
    {
        $bdd = new Bdd();
        $database = $bdd->getBdd();
        $req = $database->prepare('SELECT COUNT(id_user) AS n FROM user');
        $req->execute();
        $row = $req->fetch();
        return (int)$row['n'];
    }

    public function verifDoublonEmail(User $user)
    {
        $bdd = new Bdd();
        $database = $bdd->getBdd();
        $req = $database->prepare('SELECT 1 FROM user WHERE email = :email LIMIT 1');
        $req->execute(["email" => $user->getEmail()]);
        return (bool)$req->fetch();
    }


    public function listeUser()
    {
        $listeUser = [];
        $bdd = new Bdd();
        $datebase = $bdd->getBdd();
        $req = $datebase->prepare('SELECT * FROM user');
        $req->execute();
        $listeUsersBdd = $req->fetchAll();
        foreach ($listeUsersBdd as $listeUserBdd) {
            $listeUser[] = new User([
                'idUser' => $listeUserBdd['id_user'],
                'nom' => $listeUserBdd['nom'],
                'prenom' => $listeUserBdd['prenom'],
                'email' => $listeUserBdd['email'],
                'mdp' => $listeUserBdd['mdp'],
                'role' => $listeUserBdd['role'],
                'isApproved' => $listeUserBdd['is_approved'] ?? 0,
            ]);
        }
        return $listeUser;
    }

    public function modifUser(User $user)
    {
        $bdd = new Bdd();
        $database = $bdd->getBdd();

        // Récupérer les valeurs des références, et les mettre à NULL si elles sont vides ou invalides
        $refEntreprise = $user->getRefEntreprise();
        $refFormation = $user->getRefFormation();

        // Vérifier que l'ID de l'entreprise existe dans la table 'entreprise' (si ref_entreprise n'est pas NULL)
        if (!empty($refEntreprise)) {
            $req = $database->prepare("SELECT id_entreprise FROM entreprise WHERE id_entreprise = :id_entreprise LIMIT 1");
            $req->execute(['id_entreprise' => $refEntreprise]);
            $entrepriseExist = $req->fetch();

            if (!$entrepriseExist) {
                // Si l'entreprise n'existe pas, on met ref_entreprise à NULL
                $refEntreprise = null;
            }
        }

        // Vérifier que l'ID de la formation existe dans la table 'formation' (si ref_formation n'est pas NULL)
        if (!empty($refFormation)) {
            $req = $database->prepare("SELECT id_formation FROM formation WHERE id_formation = :id_formation LIMIT 1");
            $req->execute(['id_formation' => $refFormation]);
            $formationExist = $req->fetch();

            if (!$formationExist) {
                // Si la formation n'existe pas, on met ref_formation à NULL
                $refFormation = null;
            }
        }

        // Préparer la requête de mise à jour
        $req = $database->prepare("
        UPDATE user 
        SET nom = :nom, prenom = :prenom, email = :email, role = :role, ref_entreprise = :ref_entreprise, ref_formation = :ref_formation
        WHERE id_user = :id_user
    ");

        // Exécuter la requête avec les données de l'utilisateur
        $result = $req->execute([
            "nom" => $user->getNom(),
            "prenom" => $user->getPrenom(),
            "email" => $user->getEmail(),
            "role" => $user->getRole(),
            "ref_entreprise" => $refEntreprise,  // Peut être NULL si l'entreprise est invalide
            "ref_formation" => $refFormation,   // Peut être NULL si la formation est invalide
            "id_user" => $user->getIdUser()
        ]);

        // Vérifier si la mise à jour a réussi
        if ($result) {
            return $user;  // Retourne l'utilisateur si la mise à jour a réussi
        } else {
            // Si la requête échoue, tu peux afficher un message d'erreur ou loguer
            echo "Erreur lors de la mise à jour de l'utilisateur.";
            return null;
        }
    }


    public function suppUser(User $user)
    {
        $bdd = new Bdd();
        $database = $bdd->getBdd();

        // Vérifier si l'utilisateur existe d'abord
        $req = $database->prepare("SELECT id_user FROM user WHERE id_user = :id_user LIMIT 1");
        $req->execute(['id_user' => $user->getIdUser()]);
        $row = $req->fetch();

        if (!$row) {
            echo "L'utilisateur n'existe pas.";
            return null;  // Si l'utilisateur n'existe pas dans la BDD, on ne fait rien.
        }

        // Supprimer l'utilisateur
        $req = $database->prepare("DELETE FROM user WHERE id_user = :id_user");
        $req->execute(['id_user' => $user->getIdUser()]);

        // Vérifier si la suppression a réussi
        if ($req->rowCount() > 0) {
            echo "L'utilisateur a été supprimé avec succès.";
            return $user;  // Retourne l'utilisateur supprimé
        } else {
            echo "Erreur lors de la suppression de l'utilisateur.";
            return null;
        }
    }


    public function getUserById($id)
    {
        $bdd = new Bdd();
        $database = $bdd->getBdd();

        $req = $database->prepare('SELECT * FROM user WHERE id_user = :id LIMIT 1');
        $req->execute(['id' => $id]);
        $row = $req->fetch();

        if (!$row) {
            return null; // Si l'utilisateur n'est pas trouvé
        }

        // Assurez-vous que le namespace 'modele' est bien utilisé en haut
        // de UserRepo.php (ex: use modele\User;)
        return new User([
            'idUser' => $row['id_user'],
            'email' => $row['email'],
            'nom' => $row['nom'],
            'prenom' => $row['prenom'],
            'mdp' => $row['mdp'],
            'role' => $row['role'],
            'isApproved' => $row['is_approved'] ?? 0,
        ]);
    }

    public function ajoutUser(User $user)
    {
        $bdd = new Bdd();
        $database = $bdd->getBdd();

        // Récupérer les valeurs des références, et les mettre à NULL si elles sont vides
        $refEntreprise = $user->getRefEntreprise();
        $refFormation = $user->getRefFormation();

        if (empty($refEntreprise)) {
            $refEntreprise = null;
        }

        if (empty($refFormation)) {
            $refFormation = null;
        }

        // Préparer la requête d'insertion
        $req = $database->prepare("
        INSERT INTO user (nom, prenom, email, mdp, role, ref_entreprise, ref_formation, is_approved) 
        VALUES (:nom, :prenom, :email, :mdp, :role, :ref_entreprise, :ref_formation, :is_approved)
    ");

        // Exécuter la requête avec les données de l'utilisateur
        $req->execute([
            "nom" => $user->getNom(),
            "prenom" => $user->getPrenom(),
            "email" => $user->getEmail(),
            "mdp" => $user->getMdp(),
            "role" => $user->getRole(),
            "ref_entreprise" => $refEntreprise,  // Peut être NULL
            "ref_formation" => $refFormation,   // Peut être NULL
            "is_approved" => method_exists($user,'getIsApproved') ? (int)$user->getIsApproved() : 0,
        ]);

        // Récupérer l'ID de l'utilisateur ajouté
        $userId = $database->lastInsertId();

        // Récupérer l'utilisateur ajouté (avec l'ID)
        $user->setIdUser($userId);

        return $user;
    }

    public function getReservationsByUserId($userId) {
        // Connexion à la base de données
        $pdo = Database::getConnection();

        // Requête SQL pour récupérer les réservations de l'utilisateur
        $sql = "SELECT events.titre, events.date_event, events.lieu
                FROM reservations
                JOIN events ON reservations.id_event = events.id_event
                WHERE reservations.id_user = :id_user";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_user' => $userId]);

        // Retourne les résultats sous forme de tableau
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}