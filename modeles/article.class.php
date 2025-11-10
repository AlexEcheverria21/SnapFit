<?php
class Article {
    private int|null $id;
    private int|null $indice_pertinence; // 1 = le plus pertinent, celui que Google affiche en premier
    private string|null $titre_site;
    private string|null $url;
    private string|null $titre_recherche;
    private string|null $image;

    private bool|null $in_stock;
    private int|null $price;

    /**
     * @param int|null $id
     * @param int|null $indice_pertinence
     * @param string|null $titre_site
     * @param string|null $url
     * @param string|null $titre_recherche
     * @param string|null $image
     * @param bool|null $in_stock
     * @param int|null $price
     */
    public function __construct(?int $id, ?int $indice_pertinence, ?string $titre_site, ?string $url, ?string $titre_recherche, ?string $image, ?bool $in_stock, ?int $price)
    {
        $this->id = $id;
        $this->indice_pertinence = $indice_pertinence;
        $this->titre_site = $titre_site;
        $this->url = $url;
        $this->titre_recherche = $titre_recherche;
        $this->image = $image;
        $this->in_stock = $in_stock;
        $this->price = $price;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getIndicePertinence(): ?int
    {
        return $this->indice_pertinence;
    }

    public function setIndicePertinence(?int $indice_pertinence): void
    {
        $this->indice_pertinence = $indice_pertinence;
    }

    public function getTitreSite(): ?string
    {
        return $this->titre_site;
    }

    public function setTitreSite(?string $titre_site): void
    {
        $this->titre_site = $titre_site;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    public function getTitreRecherche(): ?string
    {
        return $this->titre_recherche;
    }

    public function setTitreRecherche(?string $titre_recherche): void
    {
        $this->titre_recherche = $titre_recherche;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    public function getInStock(): ?bool
    {
        return $this->in_stock;
    }

    public function setInStock(?bool $in_stock): void
    {
        $this->in_stock = $in_stock;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): void
    {
        $this->price = $price;
    }


}