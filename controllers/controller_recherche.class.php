<?php
/**
 * @file    controller_recherche.class.php
 * @author  Paul (Team SnapFit)
 * @brief   Gère l'historique des recherches utilisateur.
 * @version 1.0
 * @date    15/12/2025
 */

class ControllerRecherche extends Controller {
    
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader) {
        parent::__construct($twig, $loader);
    }

    /**
     * @brief   Affiche l'historique des recherches de l'utilisateur.
     */
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controleur=utilisateur&methode=login');
            exit;
        }

        $pdo = Bd::getInstance()->getConnexion();
        $rechercheDao = new RechercheDAO($pdo); // majuscule DAO conforme au fichier
        $historique = $rechercheDao->findAllByUtilisateur($_SESSION['user_id']);

        echo $this->twig->render('recherche/history.html.twig', [
            'historique' => $historique
        ]);
    }

    /**
     * @brief   Supprime une entrée de l'historique.
     */
    public function delete() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }

        $idRecherche = $_GET['id'] ?? null;
        if ($idRecherche) {
            $pdo = Bd::getInstance()->getConnexion();
            $rechercheDao = new RechercheDAO($pdo);
            
            // Vérification de sécurité : est-ce que cette recherche appartient bien à l'utilisateur ?
            // (Idéalement RechercheDao->getById() puis check id_user, mais pour simplifier on delete direct)
            // Pour le TP, on suppose que l'ID est valide ou on fait confiance au DAO s'il filtrait.
            // Le DAO `delete` supprime par ID. Il faudrait vérifier la propriété.
            
            $rech = $rechercheDao->getById($idRecherche);
            if ($rech && $rech->getIdUtilisateur() == $_SESSION['user_id']) {
                // Suppression du fichier image physique (pour nettoyer)
                $cheminFichier = 'public/uploads/' . $rech->getImage();
                if (file_exists($cheminFichier)) {
                    unlink($cheminFichier);
                }
                
                $rechercheDao->delete($idRecherche);
            }
        }
        
        header('Location: index.php?controleur=recherche&methode=index&msg=deleted');
    }
}
