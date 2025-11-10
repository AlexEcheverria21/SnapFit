<?php

class Categorie {
    private int|null $id;
    private string|null $nom;
    private string|null $image;

    public function __construct(?int $id = null, ?string $nom = null, ?string $image = null) {
        $this->id = $id;
        $this->nom = $nom;
        $this->image = $image;
    }
}