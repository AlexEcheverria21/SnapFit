<?php

class articleDao{
    private ?PDO $pdo;  

    public function __construct(?PDO $pdo = null){
    $this->pdo = $pdo;
    }

    /**
     * Get the value of pdo
     */ 
    public function getPdo(): ?PDO
    {
        return $this->pdo;
    }

    /**
     * Set the value of pdo
     *
     */ 
    public function setPdo($pdo): void
    {
        $this->pdo = $pdo;

    }

    public function find($id){

    }

    public function findAll(){
        
    }
}