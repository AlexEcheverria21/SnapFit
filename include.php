<?php
/**
 * @file    include.php
 * @author  Team SnapFit
 * @brief   Chargeur de dépendances centralisé.
 * @details Iincorpore l'autoloader Composer, la configuration et tous les fichiers de classes nécessaires.
 * @version 1.0
 * @date    2025-12-23
 */

//Ajout de vendor
require_once 'vendor/autoload.php';

//Ajout de la constantes
require_once 'config/constantes.php';

//Ajout des contrôleurs
require_once 'controllers/controller.class.php';
require_once 'controllers/controller_admin.php';
require_once 'controllers/controller_factory.class.php';
require_once 'controllers/controller_article.class.php';
require_once 'controllers/controller_favori.class.php';
require_once 'controllers/controller_utilisateur.class.php';
require_once 'controllers/controller_home.class.php';

//Ajout des modèles
require_once 'modeles/bd.class.php';
require_once 'modeles/article.class.php';
require_once 'modeles/favori.class.php';
require_once 'modeles/recherche.class.php';
require_once 'modeles/utilisateur.class.php';

//Ajout des DAO
require_once 'modeles/article.dao.php';
require_once 'modeles/favori.dao.php';
require_once 'modeles/recherche.dao.php';
require_once 'modeles/utilisateur.dao.php';
require_once 'services/SerpApiService.php'; // Service API Google Lens

//Ajout des config
require_once 'config/twig.php';
