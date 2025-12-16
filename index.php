<?php
session_start();
require_once 'include.php';

$loader = new \Twig\Loader\FilesystemLoader('views');
$twig = new \Twig\Environment($loader, [
    'debug' => true,
]);
$twig->addExtension(new \Twig\Extension\DebugExtension());
$twig->addGlobal('session', $_SESSION);

if (isset($_GET['controleur'])) {
    $nomControleur = $_GET['controleur'];
} else {
    $nomControleur = 'home';
}

if (isset($_GET['methode'])) {
    $nomMethode = $_GET['methode'];
} else {
    $nomMethode = 'afficher'; // Méthode par défaut
}

switch ($nomControleur) {
    case 'utilisateur':
        $controleur = new ControllerUtilisateur($twig, $loader);
        break;
    case 'home':
    default:
        $controleur = new ControllerHome($twig, $loader);
        break;
}

if (method_exists($controleur, $nomMethode)) {
    $controleur->$nomMethode();
} else {
    echo "Erreur 404 : Méthode introuvable";
}