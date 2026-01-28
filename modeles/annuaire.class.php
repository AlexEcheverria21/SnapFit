<?php
/**
 * @file    annuaire.class.php
 * @author  Antigravity (Team SnapFit)
 * @brief   Classe représentant un Domaine (Site web répertorié).
 *          Mappe la table DOMAINE de la BDD.
 * @version 1.0
 * @date    28/01/2026
 */
class Annuaire {
    private ?int $id;         // id_domaine
    private ?string $urlRacine; // url_racine
    private ?string $nom;
    private ?string $statut;    // eco, scam
    private ?string $description;

    public function __construct(?int $id = null, ?string $urlRacine = null, ?string $nom = null, ?string $statut = null, ?string $description = null) {
        $this->id = $id;
        $this->urlRacine = $urlRacine;
        $this->nom = $nom;
        $this->statut = $statut;
        $this->description = $description;
    }

    // Getters & Setters
    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getUrlRacine(): ?string { return $this->urlRacine; }
    public function setUrlRacine(?string $url): void { $this->urlRacine = $url; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(?string $nom): void { $this->nom = $nom; }

    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(?string $statut): void { $this->statut = $statut; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $desc): void { $this->description = $desc; }
}
