<?php
/**
 * @file    controller_utilisateur.class.php
 * @author  Alex Echeverria
 * @brief   Gère les utilisateurs (inscription, connexion, déconnexion).
 * @version 0.4
 * @date    16/12/2025
 */
class ControllerUtilisateur extends Controller {

    public function __construct(Twig\Environment $twig, Twig\Loader\FilesystemLoader $loader){
        parent::__construct($twig, $loader);
    }

    /**
     * @brief   Gère le formulaire d'inscription et la création de compte.
     */
    public function register() {
        $erreur = null;
        $succes = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email  = $_POST['email'] ?? '';
            // CORRECTION ICI : Vérifiez le 'name' dans votre register.html.twig. 
            // Si c'est <input name="mot_de_passe">, il faut utiliser 'mot_de_passe' ici.
            $mdp    = $_POST['mot_de_passe'] ?? $_POST['mdp'] ?? ''; 
            $nom    = $_POST['nom'] ?? '';
            $prenom = $_POST['prenom'] ?? '';
            $login  = $_POST['login'] ?? '';

            try {
                // Création de l'utilisateur
                // On passe une chaîne vide pour le mot de passe au constructeur car il sera défini par inscription()
                $user = new Utilisateur($email, '', $nom, $prenom, $login);
                
                // Appel de la méthode métier qui valide, hache le mdp et insère en BDD
                $user->inscription($mdp);

                // Redirection
                header('Location: index.php?controleur=utilisateur&methode=login&msg=registered');
                exit;

            } catch (Exception $e) {
                if ($e->getMessage() == 'mdp_faible') {
                    $erreur = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
                } elseif ($e->getMessage() == 'compte_existant') {
                    $erreur = "Un compte existe déjà avec cet email.";
                } else {
                    $erreur = "Erreur technique : " . $e->getMessage();
                }
            }
        }

        echo $this->twig->render('auth/register.html.twig', [
            'error' => $erreur
        ]);
    }

    /**
     * @brief   Connexion sécurisée d'un utilisateur.
     */
    public function login() {
        $erreur = null;
        $msg = $_GET['msg'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            // Même vérification ici pour le login
            $mdp = $_POST['mot_de_passe'] ?? $_POST['mdp'] ?? '';

            try {
                $user = new Utilisateur($email, ''); 

                if ($user->authentification($mdp)) {
                    $_SESSION['user_id'] = $user->getIdUtilisateur();
                    $_SESSION['user'] = [
                        'id' => $user->getIdUtilisateur(),
                        'nom' => $user->getNom(),
                        'prenom' => $user->getPrenom(),
                        'email' => $user->getEmail(),
                        'role' => $user->getRole(),
                        'login' => $user->getNomConnexion()
                    ];
                    
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

    /**
     * @brief   Déconnecte l'utilisateur et détruit la session.
     */
    public function logout() {
        session_destroy();
        header('Location: index.php');
        exit;
    }

    /**
     * @brief   Affiche et permet de modifier le profil utilisateur.
     */
    public function profile() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controleur=utilisateur&methode=login');
            exit;
        }

        $dao = new UtilisateurDao($this->pdo);
        $user = $dao->find($_SESSION['user_id']);
        
        $erreur = null;
        $succes = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom    = $_POST['nom'] ?? '';
            $prenom = $_POST['prenom'] ?? '';
            $email  = $_POST['email'] ?? '';
            $login  = $_POST['login'] ?? '';
            $newMdp = $_POST['new_mdp'] ?? '';

            try {
                // Vérifier si l'email a changé et s'il est déjà pris
                if ($email !== $user->getEmail()) {
                    $tempUser = new Utilisateur($email, '');
                    if ($tempUser->emailExiste()) {
                        throw new Exception("Cet email est déjà utilisé par un autre compte.");
                    }
                }

                $user->setNom($nom);
                $user->setPrenom($prenom);
                $user->setEmail($email);
                $user->setNomConnexion($login);

                if (!empty($newMdp)) {
                    if (!$user->estRobuste($newMdp)) {
                        throw new Exception("Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.");
                    }
                    $user->setMotDePasseHash(password_hash($newMdp, PASSWORD_BCRYPT));
                }

                if ($dao->update($user)) {
                    $succes = "Profil mis à jour avec succès !";
                    // Mettre à jour les données en session
                    $_SESSION['user']['nom'] = $nom;
                    $_SESSION['user']['prenom'] = $prenom;
                    $_SESSION['user']['email'] = $email;
                    $_SESSION['user']['login'] = $login;
                } else {
                    $erreur = "Erreur lors de la mise à jour en base de données.";
                }
            } catch (Exception $e) {
                $erreur = $e->getMessage();
            }
        }

        echo $this->twig->render('auth/profile.html.twig', [
            'user' => $user,
            'error' => $erreur,
            'success' => $succes
        ]);
    }
}