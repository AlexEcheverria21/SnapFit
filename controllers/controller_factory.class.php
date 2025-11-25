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
        $controlleurName = "Controlleur".ucfirst($controleur);
        if (!class_exists($controlleurName)) {
            throw new Exception("Le controlleur $controlleurName n'existe pas");
        }
        return new $controlleurName($twig, $loader);
    }
}