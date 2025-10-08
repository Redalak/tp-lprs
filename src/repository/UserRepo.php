<?php
use modele\User;
require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/User.php';

class UserRepo {
    public function connexion(User $user){
        $bdd = new Bdd();
        $database = $bdd->getBdd();

        $req = $database->prepare('SELECT * FROM user WHERE email = :email LIMIT 1');
        $req->execute(['email' => $user->getEmail()]);
        $row = $req->fetch();

        if(!$row){ return null; }

        return new User([
            'idUser' => $row['id_user'],
            'email'  => $row['email'],
            'nom'    => $row['nom'],
            'prenom' => $row['prenom'],
            'mdp'    => $row['mdp'],
            'role'   => $row['role'],
        ]);
    }

    public function inscription(User $user){
        $bdd = new Bdd();
        $database = $bdd->getBdd();

        $req = $database->prepare("
            INSERT INTO user(nom, prenom, email, mdp, role) VALUES (:nom, :prenom, :email, :mdp, :role)");


        $req->execute([
            "nom"    => $user->getNom(),
            "prenom" => $user->getPrenom(),
            "email"  => $user->getEmail(),
            "mdp"    => $user->getMdp(),
            "role"   => $user->getRole(), // doit être 'admin' ou 'etudiant' etc.
        ]);
        return $user;
    }

    public function nombreUtilisateur(){
        $bdd = new Bdd();
        $database = $bdd->getBdd();
        $req = $database->prepare('SELECT COUNT(id_user) AS n FROM user');
        $req->execute();
        $row = $req->fetch();
        return (int)$row['n'];
    }

    public function verifDoublonEmail(User $user){
        $bdd = new Bdd();
        $database = $bdd->getBdd();
        $req = $database->prepare('SELECT 1 FROM user WHERE email = :email LIMIT 1');
        $req->execute(["email" => $user->getEmail()]);
        return (bool)$req->fetch();
    }


    public function listeUser(){
        $listeUser = [];
        $bdd = new Bdd();
        $datebase = $bdd ->getBdd();
        $req = $datebase->prepare('SELECT * FROM user');
        $req->execute();
        $listeUsersBdd = $req->fetchAll();
        foreach($listeUsersBdd as $listeUserBdd){
            $listeUser[] = new User([
                'idUser' => $listeUserBdd['id_user'],
                'nom' => $listeUserBdd['nom'],
                'prenom' => $listeUserBdd['prenom'],
                'email' => $listeUserBdd['email'],
                'mdp' => $listeUserBdd['mdp'],
                'role' => $listeUserBdd['role'],
            ]);
        }
        return $listeUser;
    }
    public function modifUser(User $user){
        $bdd = new Bdd();
        $database=$bdd->getBdd();
        $req = $database->prepare("UPDATE user SET role = :role WHERE id_user = :id_user");
        $req->execute(array(
            "role"=>$user->getRole(),
            "id_user"=> $user->getIdUser()
        ));
        return $user;
    }
    public function deleteUser(User $user){
        $bdd = new Bdd();
        $database=$bdd->getBdd();
        $req = $database->prepare("DELETE FROM user WHERE id_user = :id_user");
        $req->execute(array(
            "id_user"=>$user->getIdUser()
        ));
        return $user;
    }


}