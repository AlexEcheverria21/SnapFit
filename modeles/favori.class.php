<?php
/**
 * @file    favori.class.php
 * @author  Team SnapFit
 * @brief   Classe représentant une liaison Favori (Utilisateur <-> Article).
 *          Utilisée pour lier un article à un utilisateur dans l'historique des favoris.
 * @version 1.0
 * @date    2025-12-23
 */
class favori {
    private int|null $id_favori;
    private string|null $url;
    private string|null $image;
    private string|null $date_fav;

    public function __construct(?int $id_favori, ?string $url, ?string $image, ?string $date_fav) {
        $this->id_favori = $id_favori;
        $this->url = $url;
        $this->image = $image;
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