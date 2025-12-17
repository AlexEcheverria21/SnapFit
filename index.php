<?php
/**
 * @file    index.php
 * @author  Clément 
 * @brief   Point d'entrée de l'application (Routeur)
 *          Redirige vers le bon contrôleur en fonction des paramètres GET.
 * @version 0.2
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
   die('Erreur : ' . $e->getMessage());
}