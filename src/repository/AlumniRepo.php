<?php

namespace repository;

class AlumniRepo
{
    private $bdd;

    public function __construct()
    {
        $bdd = new \bdd\Bdd();
        $this->bdd = $bdd->getBdd();
    }

    public function ajouterAlumni($data)
    {
        try {
            $query = "INSERT INTO alumni (ref_user, emploi_actuel, ref_entreprise) 
                     VALUES (:ref_user, :emploi_actuel, :ref_entreprise)";

            $stmt = $this->bdd->prepare($query);
            $stmt->execute([
                ':ref_user' => $data['ref_user'],
                ':emploi_actuel' => $data['emploi_actuel'],
                ':ref_entreprise' => $data['ref_entreprise']
            ]);

            return $this->bdd->lastInsertId();
        } catch (\PDOException $e) {
            error_log("Erreur lors de l'ajout de l'alumni : " . $e->getMessage());
            return false;
        }
    }
}