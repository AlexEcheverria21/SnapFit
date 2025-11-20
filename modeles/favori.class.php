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

}

?>