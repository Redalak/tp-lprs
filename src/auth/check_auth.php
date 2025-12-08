<?php
// Détection du chemin de base
$base_path = '/tp-lprs';

// Liste des dossiers et fichiers accessibles sans authentification
$public_paths = [
    '/vue/index.php',
    '/vue/formations.php',
    '/vue/connexion.php',
    '/vue/inscription.php',
    '/vue/inscriptionReussite.html',
    '/vue/oublie_mdp.php',
    '/vue/supportContact.php',
    '/index.php'
];

// Récupérer l'URL actuelle
$current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Si c'est la racine du site, autoriser l'accès
if ($current_path === $base_path . '/' || $current_path === $base_path . '/index.php') {
    return;
}

// Vérifier si le chemin actuel est dans la liste des chemins publics
$is_public = false;
foreach ($public_paths as $public_path) {
    if (strpos($current_path, $public_path) !== false) {
        $is_public = true;
        break;
    }
}

// Si l'utilisateur n'est pas connecté et tente d'accéder à une page protégée
if (!isset($_SESSION['connexion']) || $_SESSION['connexion'] !== true) {
    if (!$is_public) {
        // Stocker la page actuelle pour redirection après connexion
        $_SESSION['redirect_after_login'] = $current_path;
        // Rediriger vers la page de connexion
        header('Location: ' . $base_path . '/vue/connexion.php');
        exit();
    }
} else {
    // Si l'utilisateur est connecté et essaie d'accéder aux pages de connexion/inscription
    if (in_array($current_path, [
        $base_path . '/vue/connexion.php',
        $base_path . '/vue/inscription.php'
    ])) {
        header('Location: ' . $base_path . '/vue/evenement.php');
        exit();
    }
}
