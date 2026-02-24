<?php
/**
 * @file    controller_admin.class.php
 * @author  Clément (Team SnapFit)
 * @brief   Contrôleur d'administration pour la gestion des utilisateurs.
 *          Accès restreint aux administrateurs.
 * @version 0.3
 * @date    17/12/2025
 */
class ControllerAdmin extends Controller {

    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader) {
        parent::__construct($twig, $loader);
    }

    /**
     * @brief   Affiche le tableau de bord administrateur (Utilisateurs ou Domaines).
     */
    public function index() {
        // 1. VÉRIFICATION SÉCURITÉ
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: index.php?controleur=utilisateur&methode=login');
            exit;
        }

        $mode = $_GET['mode'] ?? 'users';
        $pdo = Bd::getInstance()->getConnexion();
        $data = [];

        if ($mode === 'domains') {
            $dao = new AnnuaireDao($pdo);
            $data['domains'] = $dao->findAll();
        } else {
            $utilisateurDao = new UtilisateurDao($pdo);
            $data['users'] = $utilisateurDao->findAll();
        }

        // 3. Affichage
        echo $this->twig->render('admin/dashboard.html.twig', array_merge($data, [
            'mode' => $mode
        ]));
    }

    /**
     * @brief   Supprime un domaine (Action Admin).
     */
    public function delete_domain() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { exit; }

        $id = $_GET['id'] ?? null;
        if ($id) {
            $pdo = Bd::getInstance()->getConnexion();
            $dao = new AnnuaireDao($pdo);
            $dao->delete((int)$id);
        }
        header('Location: index.php?controleur=admin&methode=index&mode=domains');
        exit;
    }

    /**
     * @brief   Ajoute un nouveau domaine (Action Admin).
     */
    public function add_domain() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { exit; }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $url = $_POST['url'] ?? '';
            $nom = $_POST['nom'] ?? '';
            $statut = $_POST['statut'] ?? 'scam';

            if (!empty($url) && !empty($nom)) {
                $pdo = Bd::getInstance()->getConnexion();
                $dao = new AnnuaireDao($pdo);
                $domaine = new Annuaire(null, $url, $nom, $statut);
                $dao->add($domaine);
            }
        }
        header('Location: index.php?controleur=admin&methode=index&mode=domains');
        exit;
    }

    /**
     * @brief   Supprime un utilisateur (Action Admin).
     */
    public function delete_user() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { exit; }

        $id = $_GET['id'] ?? null;
        // Sécurité : Impossible de se supprimer soi-même
        if ($id && (int)$id !== (int)$_SESSION['user_id']) {
            $pdo = Bd::getInstance()->getConnexion();
            $dao = new UtilisateurDao($pdo);
            $dao->delete((int)$id);
        }
        header('Location: index.php?controleur=admin&methode=index&mode=users');
        exit;
    }

    /**
     * @brief   Change le rôle d'un utilisateur (Action Admin).
     */
    public function toggle_role() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { exit; }

        $id = $_GET['id'] ?? null;
        if ($id && (int)$id !== (int)$_SESSION['user_id']) {
            $pdo = Bd::getInstance()->getConnexion();
            $dao = new UtilisateurDao($pdo);
            $user = $dao->find((int)$id);
            if ($user) {
                $newRole = ($user->getRole() === 'admin') ? 'user' : 'admin';
                $user->setRole($newRole);
                $dao->update($user);
            }
        }
        header('Location: index.php?controleur=admin&methode=index&mode=users');
        exit;
    }
}