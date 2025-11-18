<?php
// Script pour corriger les chemins d'accès dans les fichiers PHP

// Dossier contenant les fichiers à corriger
$vueDir = __DIR__ . '/vue/';

// Parcourir tous les fichiers PHP
echo "Début de la correction des chemins...\n";
$count = 0;

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($vueDir));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $filePath = $file->getPathname();
        $relativePath = str_replace($vueDir, '', $filePath);
        
        // Lire le contenu du fichier
        $content = file_get_contents($filePath);
        
        // Vérifier si le fichier contient le mauvais chemin
        if (strpos($content, "require_once __DIR__ . '/../../includes/header.php'") !== false) {
            // Remplacer par le bon chemin
            $newContent = str_replace(
                "require_once __DIR__ . '/../../includes/header.php'",
                "require_once __DIR__ . '/../includes/header.php'",
                $content
            );
            
            // Sauvegarder le fichier modifié
            file_put_contents($filePath, $newContent);
            echo "Corrigé : $relativePath\n";
            $count++;
        }
    }
}

echo "\nTerminé ! $count fichiers ont été corrigés.\n";
?>
