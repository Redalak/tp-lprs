<?php

use repository\userRepo;
use modele\user;

require_once '../modele/user.php';
require_once "../repository/UserRepo.php";
require_once "../bdd/Bdd.php";

session_start();

if (empty($_POST['email']) || empty($_POST['password'])) {
    header("Location: ../../vue/connexion.php?parametre=infoManquante");
    exit();
}

$user = new user([
    'email' => $_POST["email"]
]);

$userRepository = new userRepo();
$user = $userRepository->connexion($user);

if (!empty($user->getIdUser())) {
    if (password_verify($_POST['password'], $user->getMdp())) {
        $_SESSION['id_user'] = $user->getIdUser();
        $_SESSION['email'] = $user->getEmail();
        $_SESSION["connexion"] = true;

        if ($user->getRole() == "admin") {
            $_SESSION["connexionAdmin"] = true;
        }

        header("Location: ../../index.php");
        exit();
    } else {
        header("Location: ../../vue/connexion.php?parametre=emailmdpInvalide");
        exit();
    }
} else {
    header("Location: ../../vue/connexion.php?parametre=emailmdpInvalide");
    exit();
}
?>