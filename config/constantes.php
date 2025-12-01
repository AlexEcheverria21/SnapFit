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

// 4. Définition des constantes
define('DB_HOST', $config['database']['host'] ?? 'localhost');
define('DB_NAME', $config['database']['nom'] ?? 'aecheverria_pro');
define('DB_USER', $config['database']['user'] ?? 'aecheverria_pro');
define('DB_PASS', $config['database']['password'] ?? 'aecheverria_pro');
define('', '');

?>
