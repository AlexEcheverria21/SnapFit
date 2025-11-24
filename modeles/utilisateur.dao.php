<?php

class UtilisateurDao {

    //Attributs privés
    private ?PDO $pdo;


    //Constructeur
    public function __construct(?PDO $pdo=null){
        $this->pdo = $pdo;
    }

    //Getters
    public function getPdo(): ?PDO {
        return $this->pdo;
    }

    //Setters
    public function setPdo(?PDO $pdo): void {
        $this->pdo = $pdo;
    }

}