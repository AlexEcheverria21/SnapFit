<?php
class ControllerUtilisateur extends Controller{
    public function __construct(Twig\Environment $twig, Twig\Loader\FilesystemLoader $loader){
        parent::__construct($twig, $loader);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];
            
            $userDao = new UtilisateurDao($this->getPdo());
            $user = $userDao->findByEmail($email);

            if ($user && password_verify($password, $user->getMotDePasseHash())) {
                $_SESSION['user'] = $user;
                header('Location: index.php');
                exit();
            } else {
                $error = "Identifiants incorrects.";
                echo $this->getTwig()->render('auth/login.html.twig', ['error' => $error]);
                return;
            }
        }
        echo $this->getTwig()->render('auth/login.html.twig');
    }

    public function register() {
        echo "Page d'inscription (A faire)";
    }
}