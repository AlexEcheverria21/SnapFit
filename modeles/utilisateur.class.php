<?php
/**
 * @file    utilisateur.class.php
 * @author  Alex Echeverria
 * @brief   Définit la classe Utilisateur.
 * @version 1.0
 * @date    17/12/2025
 */

require_once 'bd.class.php';

// Constantes de sécurité
const MAX_CONNEXIONS_ECHOUEES = 3;
const DELAI_ATTENTE_CONNEXION = 60 * 5; // 5 minutes en secondes

class Utilisateur {

    //Attributs privés
    private ?int $id_utilisateur = null;
    private ?string $nom = null;
    private ?string $prenom = null;
    private ?string $email; // Obligatoire
    private ?string $mot_de_passe_hash; // Obligatoire
    private string $role = 'user';
    private ?string $nom_connexion = null;
    private ?string $date_inscription = null;
    private ?string $sexe = null;
    private ?string $pays = null;

    // Propriétés de Sécurité
    private int $tentativesEchouees = 0;
    private ?string $dateDernierEchecConnexion = null;
    private string $statutCompte = 'actif';

    //Contructeur
    public function __construct(?int $id_utilisateur = null, ?string $nom = null, ?string $prenom = null, ?string $mot_de_passe_hash = null, ?string $role = null, ?string $date_inscription = null, ?string $email = null, ?string $nom_connexion = null, ?string $sexe = null, ?string $pays = null ) {
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