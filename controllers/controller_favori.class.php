<?php
class ControllerFavori extends Controller{
    public function __construct(Twig\Environment $twig, Twig\Loader\FilesystemLoader $loader){
        parent::__construct($twig, $loader);
    }

    public function afficher(){
        //Récupére le DAO
        $favoriDao = new FavoriDAO($this->getPdo());
        //Récupére les données(on utilise findAll pour tester)
        $listeFavoris = $favoriDao->findAll();
        //Affiche la vue en lui passant les données
        echo $this->getTwig()->render('favori.html.twig', [
            'favoris' => $listeFavoris
        ]);

    }
}