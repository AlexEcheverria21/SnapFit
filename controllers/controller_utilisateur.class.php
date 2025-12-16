<?php
class ControllerUtilisateur extends Controller{
    public function __construct(Twig\Environment $twig, Twig\Loader\FilesystemLoader $loader){
        parent::__construct($twig, $loader);
    }

    public function login() {
        echo "Page de connexion (A faire)";
    }

    public function register() {
        echo "Page d'inscription (A faire)";
    }
}