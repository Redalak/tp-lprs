<?php
require_once __DIR__ . "/../bdd/Bdd.php";
require_once __DIR__ . "/../modele/User.php";
require_once __DIR__ . "/../repository/UserRepo.php";

use modele\User; // <— IMPORTANT

session_start();

if (
    !empty($_POST["prenom"]) &&
    !empty($_POST["nom"]) &&
    !empty($_POST["email"]) &&
    !empty($_POST["mdp"]) &&
    !empty($_POST["CMdp"])
) {
    if ($_POST["mdp"] !== $_POST["CMdp"]) {
        header("Location: ../../vue/inscription.php?msg=mdp");
        exit;
    }

    $hash = password_hash($_POST["mdp"], PASSWORD_DEFAULT);

    $userRepository = new UserRepo();

    // 1er utilisateur = admin, sinon "etudiant" (compatible avec l’ENUM actuel)
    $nbUsers = $userRepository->nombreUtilisateur();
    $role    = ($nbUsers == 0) ? "admin" : "etudiant";

    $user = new User([
        "email"  => trim($_POST["email"]),
        "nom"    => trim($_POST["nom"]),
        "prenom" => trim($_POST["prenom"]),
        "mdp"    => $hash,
        "role"   => $role
    ]);

    if ($userRepository->verifDoublonEmail($user)) {
        header("Location: ../../vue/inscription.php?msg=doublon");
        exit;
    }

    $userRepository->inscription($user);

    $_SESSION["email"]  = $user->getEmail();
    $_SESSION["role"]   = $role;
    $_SESSION["prenom"] = $user->getPrenom();
    $_SESSION["nom"]    = $user->getNom();

    header("Location: ../../index.php?msg=inscrit");
    exit;
} else {
    header("Location: ../../vue/inscription.php?msg=champsVides");
    exit;
}