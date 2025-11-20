<?php
class favori {
    private int|null $id_favori;
    private string|null $url;
    private string|null $image;
    private string|null $categorie;
    private string|null $marque;
    private string|null $date_fav;
    
    public function __construct(?int $id_favori, ?string $url, ?string $image, ?string $categorie, ?string $marque, ?string $date_fav) {
       $this->id_favori = $id_favori;
       $this->url = $url;
       $this->image = $image;
       $this->categorie = $categorie;
       $this->marque = $marque;
       $this->date_fav = $date_fav;
    }


    /**
     * Get the value of id_favori
     */ 
    public function getId_favori(): ?int
    {
        return $this->id_favori;
    }

    /**
     * Set the value of id_favori
     *
     */ 
    public function setId_favori(?int $id_favori): void
    {
        $this->id_favori = $id_favori;

    }

    /**
     * Get the value of url
     */ 
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * Set the value of url
     *
     */ 
    public function setUrl(?string $url): void
    {
        $this->url = $url;

    }

    /**
     * Get the value of image
     */ 
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * Set the value of image
     *
     */ 
    public function setImage(?string $image): void
    {
        $this->image = $image;

    }

    /**
     * Get the value of categorie
     */ 
    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    /**
     * Set the value of categorie
     *
     */ 
    public function setCategorie(?string $categorie): void
    {
        $this->categorie = $categorie;

    }

    /**
     * Get the value of marque
     */ 
    public function getMarque(): ?string
    {
        return $this->marque;
    }

    /**
     * Set the value of marque
     *
     */ 
    public function setMarque(?string $marque): void
    {
        $this->marque = $marque;

    }

    /**
     * Get the value of date_fav
     */ 
    public function getDate_fav(): ?string
    {
        return $this->date_fav;
    }

    /**
     * Set the value of date_fav
     *
     */ 
    public function setDate_fav(?string $date_fav): void
    {
        $this->date_fav = $date_fav;

    }
}

?>