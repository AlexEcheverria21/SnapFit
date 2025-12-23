<?php
/**
 * @file    controller_factory.class.php
 * @author  Team SnapFit
 * @brief   Fabrique de contrôleurs (Pattern Factory).
 * @version 1.0
 * @date    2025-12-23
 */

class ControllerFactory{

    /**
     * @brief   Crée et retourne une instance du contrôleur spécifié.
     * @details Construit le nom de la classe du contrôleur en préfixant la chaîne fournie par "Controlleur".
     * @param   string $controleur Nom du contrôleur (ex: "home" -> ControllerHome)
     * @param   Twig\Loader\FilesystemLoader $loader Hérité de l'index
     * @param   Twig\Environment $twig Hérité de l'index
     * @return  Controller Instance du contrôleur
     * @throws  Exception Si le fichier ou la classe n'existe pas
     */

    public static function getController($controleur, Twig\Loader\FilesystemLoader $loader, Twig\Environment $twig){
        //Construit le nom de la classe
        $controllerName = "Controller".ucfirst($controleur);
        //Construit le nom du fichier
        $fileName = "controller_" . strtolower($controleur) . ".class.php";
        //Cherche le fichier et on l'inclut 
        $filePath = __DIR__ . '/' . $fileName;
        if (file_exists($filePath)) {
            require_once $filePath;
        } else {
            // Si le fichier n'existe pas, on lance une erreur
            throw new Exception("Le fichier du contrôleur '$fileName' est introuvable.");
        }
        if (!class_exists($controllerName)) {
            throw new Exception("Le controlleur $controllerName n'existe pas");
        }
        return new $controllerName($twig, $loader);
    }
}