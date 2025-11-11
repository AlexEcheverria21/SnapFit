<?php
class favori {
    private int|null $id_favori;
    private string|null $url;
    private string|null $image;
    private string|null $categorie;
    private string|null $marque;
    private string|null $date_fav;
    
    public function __construct(?int $id_favori, ?string $url, ?string $image, ?string $categorie, ?string $marque, ?string $date_fav) {
       $this->id = $id_favori;
       $this->url = $url;
       $this->image = $image;
       $this->categorie = $categorie;
       $this->marque = $marque;
       $this->date = $date_fav;
    }


    /**
     * Get the value of id_favori
     */ 
    public function getId_favori()
    {
        return $this->id_favori;
    }

    /**
     * Set the value of id_favori
     *
     * @return  self
     */ 
    public function setId_favori(?int $id_favori)
    {
        $this->id_favori = $id_favori;

        return $this;
    }

    /**
     * Get the value of url
     */ 
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set the value of url
     *
     * @return  self
     */ 
    public function setUrl(?string $url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get the value of image
     */ 
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set the value of image
     *
     * @return  self
     */ 
    public function setImage(?string $image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get the value of categorie
     */ 
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * Set the value of categorie
     *
     * @return  self
     */ 
    public function setCategorie(?string $categorie)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get the value of marque
     */ 
    public function getMarque()
    {
        return $this->marque;
    }

    /**
     * Set the value of marque
     *
     * @return  self
     */ 
    public function setMarque(?string $marque)
    {
        $this->marque = $marque;

        return $this;
    }

    /**
     * Get the value of date_fav
     */ 
    public function getDate_fav()
    {
        return $this->date_fav;
    }

    /**
     * Set the value of date_fav
     *
     * @return  self
     */ 
    public function setDate_fav(?string $date_fav)
    {
        $this->date_fav = $date_fav;

        return $this;
    }
}

?>