<?php

namespace repository;

class ElevesRepo
{
    private $bdd;

    public function __construct()
    {
        $host = 'localhost';
        $dbName = 'tplprs';
        $user = 'root';

        // Mots de passe possibles : MAMP puis WAMP
        $passwords = ['root', ''];

        $options = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
        ];

        foreach ($passwords as $password) {
            try {
                $this->bdd = new \PDO(
                    "mysql:host={$host};dbname={$dbName};charset=utf8",
                    $user,
                    $password,
                    $options
                );
                // Si la connexion réussit, on sort de la boucle
                break;
            } catch (\PDOException $e) {
                // On tente le mot de passe suivant
                $this->bdd = null;
            }
        }

        // Si aucune connexion n'a marché
        if ($this->bdd === null) {
            throw new \PDOException("Impossible de se connecter à la base de données tplprs avec l'utilisateur root.");
        }
    }

    public function ajouterEleve($data)
    {
        try {
            $query = "INSERT INTO eleves (ref_user, annee_promo, date_inscription) 
                      VALUES (:ref_user, :annee_promo, :date_inscription)";

            $stmt = $this->bdd->prepare($query);
            $stmt->execute([
                ':ref_user'         => $data['ref_user'],
                ':annee_promo'      => $data['annee_promo'],
                ':date_inscription' => $data['date_inscription']
            ]);

            return $this->bdd->lastInsertId();
        } catch (\PDOException $e) {
            error_log("Erreur lors de l'ajout de l'élève : " . $e->getMessage());
            return false;
        }
    }
}