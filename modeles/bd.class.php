<?php
/**
 * @file    bd.class.php
 * @author  Team SnapFit
 * @brief   Gestionnaire de connexion Base de Données (Singleton).
 * @details Assure une unique connexion PDO pour toute l'application.
 * @version 1.0
 * @date    2025-12-23
 */

class Bd{
    private static ?Bd $instance = null;

    private ?PDO $pdo;

    /**
     * @brief   Constructeur privé (Singleton).
     * @details Initialise la connexion PDO avec les constantes globales.
     *          Configure le mode d'erreur sur Exception.
     * @throws  PDOException En cas d'échec de connexion.
     */
    private function __construct(){
        try {
            $this->pdo = new PDO('mysql:host='. DB_HOST . ';dbname='. DB_NAME, DB_USER, DB_PASS);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e){

            die('Connexion à la base de données échouée : ' . $e->getMessage());
        }
    }

    /**
     * @brief   Récupère l'instance unique de la classe Bd.
     * @return  Bd L'instance unique.
     */
    public static function getInstance(): Bd{
        if (self::$instance == null){
            self::$instance = new Bd();
        }
        return self::$instance;
    }

    /**
     * @brief   Récupère l'objet PDO de connexion.
     * @return  PDO L'objet de connexion actif.
     */
    public function getConnexion(): PDO{
        return $this->pdo;
    }

    /**
     * @brief   Empêche le clonage du Singleton.
     */
    private function __clone(){

    }

    /**
     * @brief   Empêche la désérialisation du Singleton.
     * @throws  Exception Toujours, car interdit.
     */
    public function __wakeup(){
        throw new Exception("Un singleton ne doit pas être deserialisé");
    }

}