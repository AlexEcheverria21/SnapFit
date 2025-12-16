<?php
class Article {
    private $id;
    private $url;
    private $image;
    private $categorie;
    private $marque;
    private $apiRefId;

    // Getters et Setters simplifiés pour V1
    public function getUrl() { return $this->url; }
    public function setUrl($url) { $this->url = $url; }
    
    public function getImage() { return $this->image; }
    public function setImage($image) { $this->image = $image; }
    
    public function getApiRefId() { return $this->apiRefId; }
    public function setApiRefId($id) { $this->apiRefId = $id; }
}