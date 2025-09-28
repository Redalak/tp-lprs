<?php
require_once __DIR__ . '/../repository/userRepo.php';
require_once __DIR__ . '/../modele/user.php';

use repository\userRepo;
use modele\user;

$repo = new userRepo();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? 'eleve';
    $allowed = ['admin','prof','eleve','alumni','entreprise'];
    if (!in_array($role, $allowed, true)) $role = 'eleve';

    $mdpPlain = trim($_POST['mdp'] ?? '');
    if ($mdpPlain === '') {
        header('Location: ../../vue/adminUser.php?msg=error');
        exit;
    }
    $mdpHash = password_hash($mdpPlain, PASSWORD_DEFAULT);

    $u = new user([
        'nom'               => trim($_POST['nom'] ?? ''),
        'prenom'            => trim($_POST['prenom'] ?? ''),
        'email'             => trim($_POST['email'] ?? ''),
        'mdp'               => $mdpHash,
        'role'              => $role,
        'specialite'        => trim($_POST['specialite'] ?? ''),
        'matiere'           => trim($_POST['matiere'] ?? ''),
        'poste'             => trim($_POST['poste'] ?? ''),
        'anneePromo'        => $_POST['annee_promo'] ?? null,
        'cv'                => trim($_POST['cv'] ?? ''),
        'promo'             => trim($_POST['promo'] ?? ''),
        'motifPartenariat'  => trim($_POST['motif_partenariat'] ?? ''),
        'estVerifie'        => isset($_POST['est_verifie']) ? 1 : 0,
        'refEntreprise'     => $_POST['ref_entreprise'] ?? null,
        'refFormation'      => $_POST['ref_formation'] ?? null,
    ]);

    if ($u->getNom()!=='' && $u->getPrenom()!=='' && $u->getEmail()!=='') {
        $repo->ajoutUser($u);
        header('Location: ../../vue/adminUser.php?msg=added');
        exit;
    }
}
header('Location: ../../vue/adminUser.php?msg=error');
exit;
