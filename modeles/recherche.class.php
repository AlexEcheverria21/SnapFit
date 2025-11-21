<?php

class Recherche {
    // Attributs correspondant aux colonnes de la BDD
    private ?int $id_recherche = null; // Nullable car il n'existe pas avant l'insertion
    private int $id_utilisateur;
    private string $image;
    private string $date_recherche;
    private string $api_id;

    // Constructeur
    public function __construct(int $id_utilisateur, string $image, string $api_id, ?string $date_recherche = null, ?int $id_recherche = null) {
        $this->id_utilisateur = $id_utilisateur;
        $this->image = $image;
        $this->api_id = $api_id;
        // Si la date n'est pas fournie, on peut mettre la date actuelle ou laisser la BDD gérer (ici date actuelle PHP)
        $this->date_recherche = $date_recherche ?? date('Y-m-d H:i:s'); 
        $this->id_recherche = $id_recherche;
    }

    // --- Getters ---
    public function getIdRecherche(): ?int { return $this->id_recherche; }
    public function getIdUtilisateur(): int { return $this->id_utilisateur; }
    public function getImage(): string { return $this->image; }
    public function getDateRecherche(): string { return $this->date_recherche; }
    public function getApiId(): string { return $this->api_id; }

    // --- Setters ---
    public function setIdRecherche(int $id): void { $this->id_recherche = $id; }
    public function setIdUtilisateur(int $id): void { $this->id_utilisateur = $id; }
    public function setImage(string $image): void { $this->image = $image; }
    public function setDateRecherche(string $date): void { $this->date_recherche = $date; }
    public function setApiId(string $api_id): void { $this->api_id = $api_id; }
}
?>