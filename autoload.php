<?php

spl_autoload_register(function ($class) {
    // Remplace les backslashes par des slashes et ajoute l'extension .php
    $file = __DIR__ . '/src/' . str_replace('\\', '/', $class) . '.php';
    
    // Si le fichier existe, on l'inclut
    if (file_exists($file)) {
        require $file;
        return true;
    }
    
    return false;
});
