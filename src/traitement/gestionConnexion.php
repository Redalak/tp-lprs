<?php
// src/traitement/gestionConnexion.php
use modele\User;
use repository\UserRepo;
require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/User.php';
require_once __DIR__ . '/../repository/UserRepo.php';

session_start();

if (empty($_POST['email']) || empty($_POST['password'])) {
    header("Location: ../../vue/connexion.php?parametre=infoManquante");
    exit();
}

$userClass = 'modele\\User';

// Création de l'objet user avec l'email fourni
$user = new $userClass([
    'email' => $_POST["email"]
]);

// Instanciation
$userRepository = new UserRepo();

$userFromDb = $userRepository->connexion($user);

if ($userFromDb === null) {
    header("Location: ../../vue/connexion.php?parametre=emailmdpInvalide");
    exit();
}

// Vérification du mot de passe
if (!empty($userFromDb->getIdUser()) && password_verify($_POST['password'], $userFromDb->getMdp())) {
    // Bloquer l'accès si le compte n'est pas approuvé par un admin (hors admin)
    if (method_exists($userFromDb,'getIsApproved')) {
        $approved = (int)$userFromDb->getIsApproved();
        $role = $userFromDb->getRole();
        if ($approved !== 1 && strtolower((string)$role) !== 'admin') {
            header("Location: ../../vue/connexion.php?parametre=nonApprouve");
            exit();
        }
    }
    $_SESSION['id_user'] = $userFromDb->getIdUser();
    $_SESSION['email'] = $userFromDb->getEmail();
    $_SESSION["connexion"] = true;

    if ($userFromDb->getRole() === "admin") {
        $_SESSION["connexionAdmin"] = true;
    }

    header("Location: ../../index.php");
    exit();
} else {
    header("Location: ../../vue/connexion.php?parametre=emailmdpInvalide");
    exit();
}
