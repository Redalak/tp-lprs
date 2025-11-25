<?php

namespace repository;

class ProfRepo
{
    private $bdd;

    public function __construct()
    {
        $this->bdd = new \PDO(
            'mysql:host=localhost;dbname=tplprs;charset=utf8',
            'root',
            'root',
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]
        );
    }

    public function ajouterProf($data)
    {
        try {
            $query = "INSERT INTO prof (ref_user, matiere, date_inscription) 
                     VALUES (:ref_user, :matiere, :date_inscription)";
            
            $stmt = $this->bdd->prepare($query);
            $stmt->execute([
                ':ref_user' => $data['ref_user'],
                ':matiere' => $data['matiere'],
                ':date_inscription' => $data['date_inscription']
            ]);
            
            return $this->bdd->lastInsertId();
        } catch (\PDOException $e) {
            error_log("Erreur lors de l'ajout du professeur : " . $e->getMessage());
            return false;
        }
    }
}