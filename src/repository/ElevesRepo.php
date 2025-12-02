<?php

namespace repository;

class ElevesRepo
{
    private $bdd;

    public function __construct()
    {
        $bdd = new \bdd\Bdd();
        $this->bdd = $bdd->getBdd();
    }

    public function ajouterEleve($data)
    {
        try {
            $query = "INSERT INTO eleves (ref_user, annee_promo, date_inscription) 
                     VALUES (:ref_user, :annee_promo, :date_inscription)";
            
            $stmt = $this->bdd->prepare($query);
            $stmt->execute([
                ':ref_user' => $data['ref_user'],
                ':annee_promo' => $data['annee_promo'],
                ':date_inscription' => $data['date_inscription']
            ]);
            
            return $this->bdd->lastInsertId();
        } catch (\PDOException $e) {
            error_log("Erreur lors de l'ajout de l'élève : " . $e->getMessage());
            return false;
        }
    }
}
