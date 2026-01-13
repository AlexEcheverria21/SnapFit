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
    private ?string $dateCreation; // date_creation

    public function __construct(?int $id = null, ?string $url = null, ?string $image = null, ?string $date = null) {
        $this->id = $id;
        $this->url = $url;
        $this->image = $image;
        $this->dateCreation = $date;
    }

    // Getters & Setters
    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getUrl(): ?string { return $this->url; }
    public function setUrl(?string $url): void { $this->url = $url; }

    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $image): void { $this->image = $image; }

    public function getDateCreation(): ?string { return $this->dateCreation; }
    public function setDateCreation(?string $date): void { $this->dateCreation = $date; }
}