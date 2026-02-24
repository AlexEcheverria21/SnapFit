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
        
        $returnTo = $_GET['return_to'] ?? $_POST['return_to'] ?? null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email  = $_POST['email'] ?? '';
            $mdp    = $_POST['mot_de_passe'] ?? $_POST['mdp'] ?? ''; 
            $nom    = $_POST['nom'] ?? '';
            $prenom = $_POST['prenom'] ?? '';
            $login  = $_POST['login'] ?? '';

            try {
                $user = new Utilisateur($email, '', $nom, $prenom, $login);
                $user->inscription($mdp);

                // Redirection avec return_to si présent
                $redirectUrl = 'index.php?controleur=utilisateur&methode=login&msg=registered';
                if ($returnTo) {
                    $redirectUrl .= '&return_to=' . urlencode($returnTo);
                }
                header('Location: ' . $redirectUrl);
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
            'error' => $erreur,
            'returnTo' => $returnTo
        ]);
    }

    /**
     * @brief   Connexion sécurisée d'un utilisateur.
     */
    public function login() {
        $erreur = null;
        $msg = $_GET['msg'] ?? null;

        $returnTo = $_GET['return_to'] ?? $_POST['return_to'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
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
                    
                    // Redirection : return_to en priorité, sinon accueil
                    $target = $returnTo ? $returnTo : 'index.php?controleur=home&methode=index&login=success';
                    header('Location: ' . $target); 
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
            'success' => ($msg == 'registered') ? 'Compte créé avec succès ! Connectez-vous.' : null,
            'returnTo' => $returnTo
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
        // Mettre à jour le rôle en session (au cas où il ait changé en BDD)
        $_SESSION['user']['role'] = $user->getRole();

        $erreur = null;
        $succes = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom    = $_POST['nom'] ?? '';
            $prenom = $_POST['prenom'] ?? '';
            $email  = $_POST['email'] ?? '';
            $login  = $_POST['login'] ?? '';
            
            $oldMdp = $_POST['old_mdp'] ?? '';
            $newMdp = $_POST['new_mdp'] ?? '';
            $confirmMdp = $_POST['confirm_mdp'] ?? '';

            try {
                // Vérifier si l'email a changé et s'il est déjà pris
                if ($email !== $user->getEmail()) {
                    $tempUser = new Utilisateur($email, '');
                    if ($tempUser->emailExiste()) {
                        throw new Exception("Cet email est déjà utilisé par un autre compte.");
                    }
                }

                // Si l'un des champs de mot de passe est rempli, on exige une validation complète
                if (!empty($oldMdp) || !empty($newMdp) || !empty($confirmMdp)) {
                    // 1. Vérifier l'ancien mot de passe
                    if (!password_verify($oldMdp, $user->getMotDePasseHash())) {
                        throw new Exception("L'ancien mot de passe est incorrect.");
                    }

                    // 2. Vérifier que les deux nouveaux sont identiques
                    if ($newMdp !== $confirmMdp) {
                        throw new Exception("Les nouveaux mots de passe ne correspondent pas.");
                    }

                    // 3. Vérifier la robustesse
                    if (!$user->estRobuste($newMdp)) {
                        throw new Exception("Le nouveau mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.");
                    }

                    // Tout est OK, on hache le nouveau
                    $user->setMotDePasseHash(password_hash($newMdp, PASSWORD_BCRYPT));
                }

                $user->setNom($nom);
                $user->setPrenom($prenom);
                $user->setEmail($email);
                $user->setNomConnexion($login);

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

    /**
     * @brief   Gère la demande de mot de passe oublié.
     */
    public function forgotPassword() {
        $erreur = null;
        $succes = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $dao = new UtilisateurDao($this->pdo);
            $user = $dao->findByEmail($email);

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

                if ($dao->saveResetToken($user->getIdUtilisateur(), $token, $expiry)) {
                    // Préparation mail
                    $sujet = "SnapFit - Réinitialisation de mot de passe";
                    $lien = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/index.php?controleur=utilisateur&methode=resetPassword&token=" . $token;
                    
                    $message = "Bonjour " . $user->getPrenom() . ",\n\nVous avez demande la reinitialisation de votre mot de passe.\nCliquez ici : " . $lien . "\n\nL'equipe SnapFit";
                    
                    $headers = "From: no-reply@snapfit.com";

                    // Envoi (Peut échouer en local sans SMTP configuré)
                    @mail($email, $sujet, $message, $headers);
                    
                    $succes = "Si cet e-mail existe, un lien de réinitialisation vous a été envoyé.";
                }
            } else {
                // Pour la sécurité, on affiche le même message même si l'email n'existe pas
                $succes = "Si cet e-mail existe, un lien de réinitialisation vous a été envoyé.";
            }
        }

        echo $this->twig->render('auth/forgot_password.html.twig', [
            'error' => $erreur,
            'success' => $succes
        ]);
    }

    /**
     * @brief   Gère la réinitialisation effective du mot de passe.
     */
    public function resetPassword() {
        $token = $_GET['token'] ?? $_POST['token'] ?? null;
        if (!$token) {
            header('Location: index.php');
            exit;
        }

        $dao = new UtilisateurDao($this->pdo);
        $user = $dao->findByToken($token);

        if (!$user) {
            echo "Lien invalide ou expiré.";
            exit;
        }

        $erreur = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mdp = $_POST['mdp'] ?? '';
            $confirm = $_POST['confirm'] ?? '';

            try {
                if ($mdp !== $confirm) {
                    throw new Exception("Les mots de passe ne correspondent pas.");
                }
                if (!$user->estRobuste($mdp)) {
                    throw new Exception("Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.");
                }

                $hash = password_hash($mdp, PASSWORD_BCRYPT);
                if ($dao->updatePassword($user->getIdUtilisateur(), $hash)) {
                    header('Location: index.php?controleur=utilisateur&methode=login&msg=reset_success');
                    exit;
                }
            } catch (Exception $e) {
                $erreur = $e->getMessage();
            }
        }

        echo $this->twig->render('auth/reset_password.html.twig', [
            'token' => $token,
            'error' => $erreur
        ]);
    }
}
