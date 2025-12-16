<?php
/**
 * @file    article.class.php
 * @author  Thomas (Team SnapFit)
 * @brief   Classe représentant un Article (vêtement).
 *          Mappe la table ARTICLE de la BDD (V3).
 * @version 0.3
 * @date    14/12/2025
 */
class Article {
    private ?int $id; // id_article en BDD
    private ?string $url;
    private ?string $image;
    private ?string $categorie;
    private ?string $marque;
    private ?string $apiRefId; // api_ref_id
    private ?string $dateCreation; // date_creation

    public function __construct(?int $id = null, ?string $url = null, ?string $image = null, ?string $categorie = null, ?string $marque = null, ?string $apiRefId = null, ?string $date = null) {
        $this->id = $id;
        $this->url = $url;
        $this->image = $image;
        $this->categorie = $categorie;
        $this->marque = $marque;
        $this->apiRefId = $apiRefId;
        $this->dateCreation = $date;
    }

    // Getters & Setters
    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getUrl(): ?string { return $this->url; }
    public function setUrl(?string $url): void { $this->url = $url; }

    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $image): void { $this->image = $image; }

    public function getCategorie(): ?string { return $this->categorie; }
    public function setCategorie(?string $categorie): void { $this->categorie = $categorie; }

    public function getMarque(): ?string { return $this->marque; }
    public function setMarque(?string $marque): void { $this->marque = $marque; }

    public function getApiRefId(): ?string { return $this->apiRefId; }
    public function setApiRefId(?string $apiRefId): void { $this->apiRefId = $apiRefId; }

    public function getDateCreation(): ?string { return $this->dateCreation; }
    public function setDateCreation(?string $date): void { $this->dateCreation = $date; }
}