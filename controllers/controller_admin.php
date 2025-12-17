<?php
/**
 * @file    controller_admin.class.php
 * @author  Clément (Team SnapFit)
 * @brief   Contrôleur d'administration pour la gestion des utilisateurs.
 *          Accès restreint aux administrateurs.
 * @version 0.2
 * @date    17/12/2025
 */
class ControllerAdmin extends Controller {

    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader) {
        parent::__construct($twig, $loader);
    }

    /**
     * @brief   Affiche le tableau de bord administrateur (Liste des utilisateurs).
     */
    public function index() {
        // 1. VÉRIFICATION SÉCURITÉ
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: index.php?controleur=utilisateur&methode=login');
            exit;
        }

        // 2. Récupération des données (Liste des users)
        $pdo = Bd::getInstance()->getConnexion();
        $utilisateurDao = new UtilisateurDao($pdo);
        $utilisateurs = $utilisateurDao->findAll();

        // 3. Affichage
        echo $this->twig->render('admin/dashboard.html.twig', [
            'users' => $utilisateurs // On garde la clé 'users' pour la vue
        ]);
    }
}