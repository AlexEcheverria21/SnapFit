<?php
/**
 * @file    controller_favori.class.php
 * @author  Paul (Team SnapFit)
 * @brief   Gère les favoris (Ajout, Liste, Suppression).
 * @version 2.0
 * @date    15/12/2025
 */
class ControllerFavori extends Controller {
    
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader) {
        parent::__construct($twig, $loader);
    }

    /**
     * @brief   Affiche la liste des favoris de l'utilisateur connecté.
     */
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controleur=utilisateur&methode=login');
            exit;
        }

        $pdo = Bd::getInstance()->getConnexion();
        $favoriDao = new FavoriDao($pdo);
        $favoris = $favoriDao->findAllByUser($_SESSION['user_id']);

        echo $this->twig->render('favori/index.html.twig', [
            'favoris' => $favoris
        ]);
    }

    /**
     * @brief   Ajoute un article aux favoris (via POST depuis les résultats).
     */
    public function add() {
        if (!isset($_SESSION['user_id'])) {
            // Si pas connecté, redirection login avec msg
            header('Location: index.php?controleur=utilisateur&methode=login&msg=fav_login_required');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $url = $_POST['url'] ?? '';
            $image = $_POST['image'] ?? '';
            $titre = $_POST['titre'] ?? 'Article sans titre'; // Mapped a 'categorie' ou 'marque' selon dispo
            $prix = $_POST['prix'] ?? ''; // Prix pas stocké en BDD V3 (simplification), ou concaténé dans titre

            // Création de l'objet Article
            $article = new Article();
            $article->setUrl($url);
            $article->setImage($image);
            $article->setCategorie($titre); // On utilise categorie pour le titre/nom du produit
            $article->setMarque('Inconnue'); // Par défaut car pas toujours dispo en hidden field

            $pdo = Bd::getInstance()->getConnexion();
            $favoriDao = new FavoriDao($pdo);
            
            if ($favoriDao->add($_SESSION['user_id'], $article)) {
                
                // Si c'est une requête AJAX, on renvoie du JSON
                if (isset($_GET['ajax'])) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true]);
                    exit;
                }

                // Sinon comportement classique (Redirection)
                header('Location: index.php?controleur=favori&methode=index&msg=added');
            } else {
                if (isset($_GET['ajax'])) {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'error' => "Erreur BDD"]);
                    exit;
                }
                echo "Erreur lors de l'ajout.";
            }
        }
    }

    /**
     * @brief   Supprime un favori.
     */
    public function delete() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }

        $idArticle = $_GET['id'] ?? null;
        if ($idArticle) {
            $pdo = Bd::getInstance()->getConnexion();
            $favoriDao = new FavoriDao($pdo);
            $favoriDao->delete($_SESSION['user_id'], $idArticle);
        }
        
        header('Location: index.php?controleur=favori&methode=index&msg=deleted');
    }
}