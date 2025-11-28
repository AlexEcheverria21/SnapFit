<?php

class Utilisateur {

    //Attributs privés
    private int|null $id_utilisateur;
    private string|null $nom;
    private string|null $prenom;
    private string|null $mot_de_passe_hash;
    private string|null $role;
    private string|null $date_inscription;
    private string|null $email;
    private string|null $nom_connexion;
    private string|null $sexe;
    private string|null $pays;


    //Contructeur
    public function __construct(?int $id_utilisateur = null, ?string $nom, ?string $prenom = null, ?string $mot_de_passe_hash = null, ?string $role = null, ?string $date_inscription = null, ?string $email = null, ?string $nom_connexion = null, ?string $sexe = null, ?string $pays = null ) {
        $this->id_utilisateur = $id_utilisateur;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->mot_de_passe_hash = $mot_de_passe_hash;
        $this->role = $role;
        $this->date_inscription = $date_inscription;
        $this->email = $email;
        $this->nom_connexion = $nom_connexion;
        $this->sexe = $sexe;
        $this->pays = $pays;
    }


    //Getters
    public function getIdUtilisateur(): ?int {
        return $this->id_utilisateur;
    }

    public function getNom(): ?string {
        return $this->nom;
    }

    public function getPrenom(): ?string {
        return $this->prenom;
    }

    public function getMotDePasseHash(): ?string {
        return $this->mot_de_passe_hash;
    }

    public function getRole(): ?string {
        return $this->role;
    }

    public function getDateInscription(): ?string {
        return $this->date_inscription;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function getNomConnexion(): ?string {
        return $this->nom_connexion;
    }

    public function getSexe(): ?string {
        return $this->sexe;
    }

    public function getPays(): ?string {
        return $this->pays;
    }

    //Setters
    public function setNom(?string $nom): void {
        $this->nom = $nom;
    }

    public function setPrenom(?string $prenom): void {
        $this->prenom = $prenom;
    }

    public function setMotDePasseHash(?string $mot_de_passe_hash): void {
        $this->mot_de_passe_hash = $mot_de_passe_hash;
    }

    public function setEmail(?string $email): void {
        $this->email = $email;
    }

    public function setNomConnexion(?string $nom_connexion): void {
        $this->nom_connexion = $nom_connexion;
    }

    public function setSexe(?string $sexe): void {
        $this->sexe = $sexe;
    }

    public function setPays(?string $pays): void {
        $this->pays = $pays;
    }

    public function setIdUtilisateur(?int $id_utilisateur): void {
        $this->id_utilisateur = $id_utilisateur;
    }

    public function setRole(?string $role): void {
        $this->role = $role;
    }

    public function setDateInscription(?string $date_inscription): void {
        $this->date_inscription = $date_inscription;
    }

}