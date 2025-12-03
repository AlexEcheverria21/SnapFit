<?php

class ControllerFactory{

    /**
     * @brief Crée et retourne une instance du contrôleur spécifié.
     * @details Construit le nom de la classe du contrôleur en préfixant la chaîne fournie par "Controlleur"
     * et en utilisant une majuscule pour la première lettre du nom de base.
     * Vérifie l'existence de la classe avant de l'instancier et lui passe les objets Twig requis.
     * @throws Exception Si la classe du contrôleur généré n'existe pas.
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