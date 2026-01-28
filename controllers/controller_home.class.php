<?php
/**
 * @file    controller_home.class.php
 * @author  Louis (Team SnapFit)
 * @brief   Gère l'affichage de la page d'accueil.
 * @version 1.0
 * @date    17/12/2025
 */
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

    public function labels_info() {
        echo $this->twig->render('labels_info.html.twig');
    }
}
