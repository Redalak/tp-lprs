<?php
namespace repository;

require_once __DIR__ . '/../bdd/bdd.php';
require_once __DIR__ . '/../modele/user.php';

use modele\user;
use PDO;

class userRepo
{
    /** @var string */
    private $table = '`user`'; // ← si ta table est "users", mets : private $table = '`users`';

    /** Liste brute pour la page */
    public function getAllRaw() {
        $db  = \bdd();
        $sql = "SELECT * FROM {$this->table} ORDER BY id_user DESC";
        $req = $db->prepare($sql);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return user|null */
    public function getModelById($id) {
        $db  = \bdd();
        $req = $db->prepare("
            SELECT
                id_user               AS idUser,
                nom                   AS nom,
                prenom                AS prenom,
                email                 AS email,
                mdp                   AS mdp,
                role                  AS role,
                specialite            AS specialite,
                matiere               AS matiere,
                poste                 AS poste,
                annee_promo           AS anneePromo,
                cv                    AS cv,
                promo                 AS promo,
                motif_partenariat     AS motifPartenariat,
                est_verifie           AS estVerifie,
                ref_entreprise        AS refEntreprise,
                ref_formation         AS refFormation,
                created_at            AS createdAt,
                updated_at            AS updatedAt
            FROM {$this->table}
            WHERE id_user = :id
        ");
        $req->execute(['id' => (int)$id]);
        $row = $req->fetch(PDO::FETCH_ASSOC);
        return $row ? new user($row) : null;
    }

    /** @return user */
    public function ajoutUser(user $u) {
        $db  = \bdd();
        $req = $db->prepare("
        INSERT INTO {$this->table}
            (nom, prenom, email, mdp, role, specialite, matiere, poste,
             annee_promo, cv, promo, motif_partenariat, est_verifie, ref_entreprise, ref_formation)
        VALUES
            (:nom, :prenom, :email, :mdp, :role, :specialite, :matiere, :poste,
             :annee_promo, :cv, :promo, :motif_partenariat, :est_verifie, :ref_entreprise, :ref_formation)
    ");

        // 🧠 Nettoyage ref_entreprise
        $refEntreprise = $u->getRefEntreprise();
        if ($refEntreprise === '' || $refEntreprise === null || $refEntreprise == 0) {
            $refEntreprise = null;
        }

        // 🧠 Nettoyage ref_formation
        $refFormation = $u->getRefFormation();
        if ($refFormation === '' || $refFormation === null || $refFormation == 0) {
            $refFormation = null;
        }

        $req->execute([
            'nom'                => $u->getNom(),
            'prenom'             => $u->getPrenom(),
            'email'              => $u->getEmail(),
            'mdp'                => $u->getMdp(), // déjà hashé
            'role'               => $u->getRole(),
            'specialite'         => $u->getSpecialite(),
            'matiere'            => $u->getMatiere(),
            'poste'              => $u->getPoste(),
            'annee_promo'        => $u->getAnneePromo(),
            'cv'                 => $u->getCv(),
            'promo'              => $u->getPromo(),
            'motif_partenariat'  => $u->getMotifPartenariat(),
            'est_verifie'        => $u->getEstVerifie(),
            'ref_entreprise'     => $refEntreprise,
            'ref_formation'      => $refFormation,
        ]);

        $u->setIdUser((int)$db->lastInsertId());
        return $u;
    }


    /** @return user */
    public function modifUser(user $u) {
        $db = \bdd();

        // Nettoyage ref_entreprise
        $refEntreprise = $u->getRefEntreprise();
        if ($refEntreprise === '' || $refEntreprise === null || $refEntreprise == 0) {
            $refEntreprise = null;
        }

        // Nettoyage ref_formation
        $refFormation = $u->getRefFormation();
        if ($refFormation === '' || $refFormation === null || $refFormation == 0) {
            $refFormation = null;
        }

        // SET dynamique : on n’update mdp que s’il est fourni
        $set = "
        nom=:nom,
        prenom=:prenom,
        email=:email,
        role=:role,
        specialite=:specialite,
        matiere=:matiere,
        poste=:poste,
        annee_promo=:annee_promo,
        cv=:cv,
        promo=:promo,
        motif_partenariat=:motif_partenariat,
        est_verifie=:est_verifie,
        ref_entreprise=:ref_entreprise,
        ref_formation=:ref_formation
    ";
        $params = [
            'id_user'            => $u->getIdUser(),
            'nom'                => $u->getNom(),
            'prenom'             => $u->getPrenom(),
            'email'              => $u->getEmail(),
            'role'               => $u->getRole(),
            'specialite'         => $u->getSpecialite(),
            'matiere'            => $u->getMatiere(),
            'poste'              => $u->getPoste(),
            'annee_promo'        => $u->getAnneePromo(),
            'cv'                 => $u->getCv(),
            'promo'              => $u->getPromo(),
            'motif_partenariat'  => $u->getMotifPartenariat(),
            'est_verifie'        => $u->getEstVerifie(),
            'ref_entreprise'     => $refEntreprise,
            'ref_formation'      => $refFormation,
        ];

        if ($u->getMdp() !== null && $u->getMdp() !== '') {
            $set .= ", mdp=:mdp";
            $params['mdp'] = $u->getMdp(); // déjà hashé
        }

        $sql = "UPDATE {$this->table} SET $set WHERE id_user=:id_user";
        $req = $db->prepare($sql);
        $req->execute($params);
        return $u;
    }


    /** @param int $id */
    public function suppUser($id) {
        $db  = \bdd();
        $req = $db->prepare("DELETE FROM {$this->table} WHERE id_user = :id");
        $req->execute(['id' => (int)$id]);
    }

    public function connexion(user $u){
        $db  = \bdd();
        $req = $db->prepare('SELECT * FROM user WHERE email = :email');
        $req->execute(array(
            'email' => $u->getEmail()
        ));
        $utilisateur = $req->fetch();
        if($utilisateur){
            $u->setMdp($utilisateur['mdp']);
            $u->setRole($utilisateur["role"]);
            $u->setIdUser($utilisateur["id_user"]);
            $u->setEmail($utilisateur["email"]);
        }
        return $u;
    }

}
