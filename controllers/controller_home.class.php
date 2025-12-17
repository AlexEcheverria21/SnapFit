<?php

class ControllerHome extends Controller {

    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader) {
        parent::__construct($twig, $loader);
    }

    public function index() {
        // Pas de DAO à appeler pour l'instant car la page d'accueil est statique
        
        // Chargement du template
        $template = $this->getTwig()->load('home.html.twig');
        
        // Affichage de la page
        $loginSuccess = (isset($_GET['login']) && $_GET['login'] === 'success');
        
        echo $template->render(array(
            'login_success' => $loginSuccess
        ));
    }
}
