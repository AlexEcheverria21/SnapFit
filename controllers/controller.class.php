<?php
/**
 * @file    controller.class.php
 * @author  Team SnapFit
 * @brief   Classe abstraite parente de tous les contrôleurs.
 * @details Fournit l'accès partagé à Twig, à la BDD (PDO) et aux variables de requête (GET/POST).
 * @version 1.0
 * @date    2025-12-23
 */
class Controller {
    protected PDO $pdo;
    protected Twig\Loader\FilesystemLoader $loader;
    protected Twig\Environment $twig;
    protected ?array $get = null;
    protected ?array $post = null;

    /**
     * @brief   Constructeur du contrôleur de base.
     * @param   Twig\Environment $twig Instance de l'environnement Twig
     * @param   Twig\Loader\FilesystemLoader $loader Instance du chargeur de fichiers Twig
     */
    public function __construct(Twig\Environment $twig, Twig\Loader\FilesystemLoader $loader) {
        $db = Bd::getInstance();
        $this->pdo = $db->getConnexion();

        $this->loader = $loader;
        $this->twig = $twig;

        if (isset($_GET) && !empty($_GET)) {
            $this->get = $_GET;
        }

        if (isset($_POST) && !empty($_POST)) {
            $this->post = $_POST;
        }

    }

    /**
     * @brief   Appelle une méthode du contrôleur.
     * @param   string $methode Nom de la méthode à exécuter
     * @return  mixed Retourne le résultat de la méthode appelée
     * @throws  Exception Si la méthode n'existe pas
     */
    public function call (string $methode): mixed {
        if (!method_exists($this, $methode)) {
            throw new Exception("La méthode $methode n'existe pas dans le controller __CLASS__".__CLASS__);
        }
        return $this->$methode(); 
    }


    /**
     * @brief   Récupère l'instance PDO.
     * @return  PDO|null
     */ 
    public function getPdo(): ?PDO
    {
        return $this->pdo;
    }

    /**
     * @brief   Définit l'instance PDO.
     * @param   PDO|null $pdo
     */ 
    public function setPdo(?PDO $pdo): void
    {
        $this->pdo = $pdo;
    }

    /**
     * @brief   Récupère le loader Twig.
     * @return  Twig\Loader\FilesystemLoader
     */ 
    public function getLoader(): Twig\Loader\FilesystemLoader
    {
        return $this->loader;
    }

    /**
     * @brief   Définit le loader Twig.
     * @param   Twig\Loader\FilesystemLoader $loader
     */ 
    public function setLoader(Twig\Loader\FilesystemLoader $loader): void
    {
        $this->loader = $loader;
    }

    /**
     * @brief   Récupère l'environnement Twig.
     * @return  Twig\Environment
     */ 
    public function getTwig(): Twig\Environment
    {
        return $this->twig;
    }

    /**
     * @brief   Définit l'environnement Twig.
     * @param   Twig\Environment $twig
     */ 
    public function setTwig(Twig\Environment $twig): void
    {
        $this->twig = $twig;
    }

    /**
     * @brief   Récupère le tableau $_GET.
     * @return  array|null
     */ 
    public function getGet(): ?array
    {
        return $this->get;
    }

    /**
     * @brief   Définit le tableau $_GET.
     * @param   array|null $get
     */ 
    public function setGet(?array $get): void
    {
        $this->get = $get;
    }

    /**
     * @brief   Récupère le tableau $_POST.
     * @return  array|null
     */ 
    public function getPost(): ?array
    {
        return $this->post;
    }

    /**
     * @brief   Définit le tableau $_POST.
     * @param   array|null $post
     */ 
    public function setPost(?array $post): void
    {
        $this->post = $post;
    }
}