<?php

namespace repository;

require_once __DIR__ . '/../bdd/Bdd.php';
use bdd\Bdd;

class ElevesRepo
{
    private $bdd;

    public function __construct()
    {
        // Utilise la configuration centralisée (MAMP root/root port 8889 avec fallback WAMP)
        $this->bdd = (new Bdd())->getBdd();
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
