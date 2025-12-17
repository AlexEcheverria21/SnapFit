<?php

use Symfony\Component\Yaml\Yaml;

// 1. Chemin vers le fichier config.yaml
$configFile = __DIR__ . '/../config.yaml';

// 2. Vérification de sécurité 
if (!file_exists($configFile)) {
    die("<h1>Erreur de configuration</h1><p>Le fichier <code>config.yaml</code> est introuvable. Veuillez le créer à la racine du projet en copiant <code>config.example.yaml</code>.</p>");
}

// 3. Lecture du fichier YAML
try {
    $config = Yaml::parseFile($configFile);
} catch (Exception $e) {
    die("<h1>Erreur YAML</h1><p>Impossible de lire le fichier de configuration : " . $e->getMessage() . "</p>");
}

if (isset($config['bdd'])) {
    define('DB_HOST', $config['bdd']['host'] );
    define('DB_NAME', $config['bdd']['nom']); // 'nom' dans le YAML
    define('DB_USER', $config['bdd']['user']);
    define('DB_PASS', $config['bdd']['password']);
} else {
    // Valeurs par défaut si la section bdd est absente
    define('DB_HOST', '');
    define('DB_NAME', '');
    define('DB_USER', '');
    define('DB_PASS', '');
}



?>
