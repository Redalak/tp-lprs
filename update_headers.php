<?php
// Script pour mettre à jour les en-têtes des fichiers PHP

// Dossier contenant les fichiers à mettre à jour
$vueDir = __DIR__ . '/vue/';

// Fichiers à ignorer (déjà mis à jour ou ne nécessitant pas d'en-tête)
$ignoreFiles = [
    'connexion.php',
    'inscription.php',
    'oublie_mdp.php',
    'inscriptionReussite.html',
    'includes/header.php',
    'includes/footer.php',
    'adminGestion.php' // Fichier vide ou spécial
];

// Fonction pour obtenir le titre de la page à partir du nom du fichier
function getPageTitle($filename) {
    $name = basename($filename, '.php');
    $name = str_replace(['_', '-'], ' ', $name);
    $name = ucwords($name);
    
    // Traductions spéciales
    $translations = [
        'profiluser' => 'Profil Utilisateur',
        'ajout' => 'Ajouter',
        'modif' => 'Modifier',
        'supp' => 'Supprimer',
        'admin' => 'Admin',
        'event' => 'Événement',
        'entreprise' => 'Entreprise',
        'user' => 'Utilisateur',
        'offre' => 'Offre',
        'formation' => 'Formation',
        'detail' => 'Détails',
        'inscription_evenement' => 'Inscription à l\'événement'
    ];
    
    foreach ($translations as $key => $value) {
        $name = str_ireplace($key, $value, $name);
    }
    
    return $name;
}

// Fonction pour mettre à jour un fichier
function updateFile($filePath) {
    $content = file_get_contents($filePath);
    
    // Vérifier si le fichier contient déjà l'en-tête
    if (strpos($content, '<?php
// Définir le titre de la page') !== false) {
        return false; // Déjà mis à jour
    }
    
    // Obtenir le titre de la page
    $title = getPageTitle(basename($filePath));
    
    // Nouvel en-tête
    $header = "<?php\n// Définir le titre de la page\n\$pageTitle = '" . addslashes($title) . "';\n\n" . 
              "// Inclure l'en-tête qui gère la session et l'authentification\n" . 
              "require_once __DIR__ . '/../includes/header.php';\n?>";
    
    // Si le fichier commence par <?php, on le remplace
    if (strpos(trim($content), '<?php') === 0) {
        $content = preg_replace('/^<\?php\s*/', $header . "\n\n", $content, 1);
    } else {
        // Sinon, on ajoute l'en-tête au début
        $content = $header . "\n\n" . $content;
    }
    
    // Sauvegarder le fichier
    file_put_contents($filePath, $content);
    return true;
}

// Parcourir tous les fichiers PHP
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($vueDir));
$updated = 0;

echo "Mise à jour des en-têtes PHP...\n";

foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $relativePath = str_replace('\\', '/', substr($file->getPathname(), strlen($vueDir)));
        
        // Vérifier si le fichier doit être ignoré
        $ignore = false;
        foreach ($ignoreFiles as $ignoreFile) {
            if (strpos($relativePath, $ignoreFile) !== false) {
                $ignore = true;
                break;
            }
        }
        
        if (!$ignore) {
            if (updateFile($file->getPathname())) {
                echo "Mis à jour : " . $relativePath . "\n";
                $updated++;
            }
        }
    }
}

echo "\nTerminé ! $updated fichiers mis à jour.\n";
