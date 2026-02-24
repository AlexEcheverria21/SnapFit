<?php
/**
 * @file    index.php
 * @author  Clément 
 * @brief   Point d'entrée de l'application (Routeur)
 *          Redirige vers le bon contrôleur en fonction des paramètres GET.
 * @version 0.3
 * @date    17/12/2025
 */
require_once 'include.php';

try  {
    if (isset($_GET['controleur'])){
        $nomControleur = $_GET['controleur'];
    } else {
        $nomControleur = '';
    }

    if (isset($_GET['methode'])){
        $nomMethode = $_GET['methode'];
    } else {
        $nomMethode = '';
    }

    // Gestion de la page d'accueil par défaut
    if ($nomControleur == '' && $nomMethode == ''){
        $nomControleur = 'home';
        $nomMethode = 'index';
    }

    if ($nomControleur == '' ){
        throw new Exception('Le controleur n\'est pas défini');
    }

    if ($nomMethode == '' ){
        throw new Exception('La méthode n\'est pas définie');
    }

    // $loader et $twig viennent de config/twig.php
    $controleur = ControllerFactory::getController($nomControleur, $loader, $twig);
  
    $controleur->call($nomMethode);

} catch (Exception $e) {
    // Graceful error handling with Twig 
    // On masque les détails techniques en production (ici debug = true pour le dev)
    $debugMode = true; 
    
    echo $twig->render('error.html.twig', [
        'message' => $e->getMessage(),
        'debug'   => $debugMode
    ]);
}