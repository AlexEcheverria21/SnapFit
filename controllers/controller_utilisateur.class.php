<?php
/**
 * @file    controller_utilisateur.class.php
 * @author  Alex Echeverria
 * @brief   Gère les utilisateurs (inscription, connexion, déconnexion).
 * @version 0.3
 * @date    16/12/2025
 */
class ControllerUtilisateur extends Controller{

    public function __construct(Twig\Environment $twig, Twig\Loader\FilesystemLoader $loader){
        parent::__construct($twig, $loader);
    }

    /**
     * @brief   Connexion sécurisée d'un utilisateur.
     */
    public function login() {
        $erreur = null;
        $msg = $_GET['msg'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $mdp = $_POST['mdp'] ?? '';

            try {
                // Instanciation de l'User pour authentification
                $user = new Utilisateur($email, ''); 

                if ($user->authentification($mdp)) {
                    // Mise en session
                    $_SESSION['user_id'] = $user->getId();
                    $_SESSION['user'] = [
                        'id' => $user->getId(),
                        'nom' => $user->getNom(),
                        'prenom' => $user->getPrenom(),
                        'email' => $user->getEmail(),
                        'role' => $user->getRole(),
                        'login' => $user->getNomConnexion()
                    ];
                    
                    // Redirection
                    header('Location: index.php?controleur=home&methode=index&login=success'); 
                    exit;
                } else {
                    $erreur = "Identifiant ou mot de passe incorrect.";
                }

            } catch (Exception $e) {
                if ($e->getMessage() == 'compte_desactive') {
                    $erreur = "Compte bloqué suite à trop de tentatives. Réessayez dans 5 minutes.";
                } else {
                    $erreur = "Erreur : " . $e->getMessage();
                }
            }
        }

        echo $this->twig->render('auth/login.html.twig', [
            'error' => $erreur,
            'success' => ($msg == 'registered') ? 'Compte créé avec succès ! Connectez-vous.' : null
        ]);
    }

    public function register() {
        echo "Page d'inscription (A faire)";
    }
}