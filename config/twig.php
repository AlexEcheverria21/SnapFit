<?php
/**
 * @file    twig.php
 * @author  Team SnapFit
 * @brief   Initialisation du moteur de template Twig.
 * @details Configure le loader, l'environnement et les extensions (Debug, Intl) pour les vues.
 * @version 1.0
 * @date    2025-12-23
 */
//ajout de la classe IntlExtension et creation de l’alias IntlExtension
use Twig\Extra\Intl\IntlExtension;

//initialisation twig : chargement du dossier contenant les views
$loader = new Twig\Loader\FilesystemLoader('views');

//Paramétrage de l'environnement twig
$twig = new Twig\Environment($loader, [
    /*passe en mode debug à enlever en environnement de prod : permet d'utiliser dans un views {{dump
    (variable)}} pour afficher le contenu d'une variable. Nécessite l'utilisation de l'extension debug*/
    'debug' => true,
    // Il est possible de définir d'autre variable d'environnement
    //...
]);

// Ajout de la variable session globale pour les vues
$twig->addGlobal('session', $_SESSION);

//Définition de la timezone pour que les filtres date tiennent compte du fuseau horaire français.
$twig->getExtension(\Twig\Extension\CoreExtension::class)->setTimezone('Europe/Paris');

//Ajouter l'extension debug
$twig->addExtension(new \Twig\Extension\DebugExtension());

//Ajout de l'extension d'internationalisation qui permet d'utiliser les filtres de date dans twig
$twig->addExtension(new IntlExtension());