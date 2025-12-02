<?php
return [
    'host'     => 'localhost',
    'dbname'   => 'tplprs',
    'charset'  => 'utf8',

    // Identifiants dynamiques selon l'environnement
    'user'     => 'root',
    'password' => (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
        ? ''        // WAMP (Windows)
        : 'root',   // MAMP (macOS)
];
