<?php
/**
 * @file    controller_annuaire.class.php
 * @author  Antigravity (Team SnapFit)
 * @brief   Définit un contrôleur gérant l'Annuaire (Domaines).
 * @version 1.0
 * @date    28/01/2026
 */
class ControllerAnnuaire extends Controller {

    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader) {
        parent::__construct($twig, $loader);
    }

    /**
     * @brief   Affiche la page de l'annuaire avec la liste des sites.
     */
    public function index() {
        $annuaireDao = new AnnuaireDao($this->pdo);
        
        // Récupération des sites par catégories
        $sitesEco = $annuaireDao->findByStatut('eco');
        $sitesScam = $annuaireDao->findByStatut('scam');
        
        echo $this->twig->render('annuaire/index.html.twig', [
            'sitesEco' => $sitesEco,
            'sitesScam' => $sitesScam
        ]);
    }

    /**
     * @brief   Gère la soumission d'une suggestion de site (placeholder).
     */
    public function suggérer() {
        $to      = 'snapfit.dev@gmail.com';
        $subject = 'Suggestion';
        $message = $_POST['nom'] . ' ' . $_POST['url'] . ' ' . $_POST['statut'];

        mail($to, $subject, $message);

        header('Location: index.php?controleur=annuaire&methode=index&success=1');
        exit();
    }
}
