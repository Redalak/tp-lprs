<?php

namespace repository;

require_once __DIR__ . '/../bdd/Bdd.php';

use bdd\Bdd;
use PDO;

class UserEntrepriseRepo
{
    private $bdd;

    public function __construct()
    {
        $this->bdd = (new Bdd())->getBdd();
    }

    public function getUserEntreprise($userId)
    {
        $req = $this->bdd->prepare('
            SELECT e.* FROM entreprise e
            INNER JOIN user_entreprise ue ON e.id_entreprise = ue.ref_entreprise
            WHERE ue.ref_user = :userId
        ');
        $req->execute(['userId' => $userId]);
        return $req->fetch(PDO::FETCH_ASSOC);
    }

    public function linkUserToEntreprise($userId, $entrepriseId)
    {
        // Vérifier si l'utilisateur n'a pas déjà une entreprise
        $existing = $this->getUserEntreprise($userId);
        if ($existing) {
            throw new \Exception("Vous êtes déjà associé à une entreprise.");
        }

        $req = $this->bdd->prepare('
            INSERT INTO user_entreprise (ref_user, ref_entreprise, date_liaison)
            VALUES (:userId, :entrepriseId, NOW())
        ');
        return $req->execute([
            'userId' => $userId,
            'entrepriseId' => $entrepriseId
        ]);
    }

    public function unlinkUserFromEntreprise($userId, $entrepriseId)
    {
        $req = $this->bdd->prepare('
            DELETE FROM user_entreprise 
            WHERE ref_user = :userId AND ref_entreprise = :entrepriseId
        ');
        return $req->execute([
            'userId' => $userId,
            'entrepriseId' => $entrepriseId
        ]);
    }

    public function getAllEntreprises()
    {
        $req = $this->bdd->query('SELECT * FROM entreprise ORDER BY nom');
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
}
