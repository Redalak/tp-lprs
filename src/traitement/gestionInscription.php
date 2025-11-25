<?php
// Inclure les fichiers nécessaires
require_once __DIR__ . "/../bdd/Bdd.php";
require_once __DIR__ . "/../modele/User.php";
require_once __DIR__ . "/../repository/UserRepo.php";
require_once __DIR__ . "/../repository/ElevesRepo.php";
require_once __DIR__ . "/../repository/AlumniRepo.php";
require_once __DIR__ . "/../repository/ProfRepo.php";
require_once __DIR__ . "/../repository/EntrepriseRepo.php";

// Utilisation des classes
use modele\User;
use repository\UserRepo;
use repository\ElevesRepo;
use repository\AlumniRepo;
use repository\ProfRepo;
use repository\EntrepriseRepo;

session_start();

if (
    !empty($_POST["prenom"]) &&
    !empty($_POST["nom"]) &&
    !empty($_POST["email"]) &&
    !empty($_POST["mdp"]) &&
    !empty($_POST["CMdp"]) &&
    !empty($_POST["role"])
) {
    // Vérification de la correspondance des mots de passe
    if ($_POST["mdp"] !== $_POST["CMdp"]) {
        header("Location: ../../vue/inscription.php?msg=mdp");
        exit;
    }

    // Vérification du rôle valide
    $rolesValides = ['etudiant', 'alumni', 'prof'];
    $role = $_POST["role"];
    
    if (!in_array($role, $rolesValides)) {
        header("Location: ../../vue/inscription.php?msg=roleInvalide");
        exit;
    }

    // Vérification des champs spécifiques selon le rôle
    if ($role === 'etudiant' && empty($_POST["annee_promo"])) {
        header("Location: ../../vue/inscription.php?msg=champsManquants&role=" . urlencode($role));
        exit;
    }
    
    if ($role === 'alumni' && (empty($_POST["emploi_actuel"]) || empty($_POST["nom_entreprise"]))) {
        header("Location: ../../vue/inscription.php?msg=champsManquants&role=" . urlencode($role));
        exit;
    }
    
    if ($role === 'prof' && empty($_POST["matiere"])) {
        header("Location: ../../vue/inscription.php?msg=champsManquants&role=" . urlencode($role));
        exit;
    }

    $hash = password_hash($_POST["mdp"], PASSWORD_DEFAULT);
    $userRepository = new UserRepo();

    // 1er utilisateur = admin, sinon le rôle choisi
    $nbUsers = $userRepository->nombreUtilisateur();
    $role = ($nbUsers == 0) ? "admin" : $role;

    // Création de l'utilisateur de base
    $user = new User([
        "email"  => trim($_POST["email"]),
        "nom"    => trim($_POST["nom"]),
        "prenom" => trim($_POST["prenom"]),
        "mdp"    => $hash,
        "role"   => $role,
        "isApproved" => ($role === 'admin') ? 1 : 0 // Le compte admin est approuvé automatiquement
    ]);

    // Vérification des doublons d'email
    if ($userRepository->verifDoublonEmail($user)) {
        header("Location: ../../vue/inscription.php?msg=doublon&role=" . urlencode($role));
        exit;
    }

    // Création de l'utilisateur dans la base de données
    $user = $userRepository->inscription($user);
    $userId = $user->getIdUser();

    // Gestion des données spécifiques selon le rôle
    if ($role === 'etudiant') {
        $elevesRepo = new ElevesRepo();
        $elevesRepo->ajouterEleve([
            'ref_user' => $userId,
            'annee_promo' => $_POST['annee_promo'],
            'date_inscription' => date('Y-m-d')
        ]);
    } 
    elseif ($role === 'alumni') {
        // Vérifier d'abord si l'entreprise existe déjà
        $entrepriseRepo = new EntrepriseRepo();
        $entreprise = $entrepriseRepo->trouverParNom(trim($_POST['nom_entreprise']));
        
        if (!$entreprise) {
            // Créer l'entreprise si elle n'existe pas
            $entrepriseId = $entrepriseRepo->creerEntreprise([
                'nom' => trim($_POST['nom_entreprise']),
                'adresse' => '', // Adresse vide par défaut
                'ville' => '',
                'code_postal' => '',
                'pays' => ''
            ]);
        } else {
            $entrepriseId = $entreprise['id_entreprise'];
        }
        
        // Créer l'entrée alumni
        $alumniRepo = new AlumniRepo();
        $alumniRepo->ajouterAlumni([
            'ref_user' => $userId,
            'emploi_actuel' => trim($_POST['emploi_actuel']),
            'ref_entreprise' => $entrepriseId
        ]);
    } 
    elseif ($role === 'prof') {
        $profRepo = new ProfRepo();
        $profRepo->ajouterProf([
            'ref_user' => $userId,
            'matiere' => trim($_POST['matiere']),
            'date_inscription' => date('Y-m-d')
        ]);
    }

    // Si c'est un admin (premier utilisateur), connectez-le directement
    if ($role === 'admin') {
        $_SESSION["email"] = $user->getEmail();
        $_SESSION["role"] = $role;
        $_SESSION["prenom"] = $user->getPrenom();
        $_SESSION["nom"] = $user->getNom();
        
        header("Location: ../../vue/inscriptionReussite.php?role=admin");
    } else {
        // Pour les autres rôles, rediriger vers une page de succès en attente de validation
        header("Location: ../../vue/inscriptionReussite.php?role=" . urlencode($role));
    }
    exit;
} else {
    $role = isset($_POST["role"]) ? $_POST["role"] : '';
    header("Location: ../../vue/inscription.php?msg=champsVides&role=" . urlencode($role));
    exit;
}