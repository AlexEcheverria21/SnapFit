<?php
class ControllerArticle extends Controller {
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader) {
        parent::__construct($twig, $loader);
    }

    public function result() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?controleur=utilisateur&methode=login');
            exit();
        }

        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
            $uploadDir = 'public/uploads/';
            $filename = uniqid() . '_' . basename($_FILES['photo']['name']);
            $targetPath = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
                // Appel API
                global $config;
                $service = new SerpApiService($config['api']['serpapi_key']);
                
                // Note: En local on utilise une image demo car SerpAPI ne peut pas lire localhost
                // $tempUrl = $this->uploadToEphemeralHost($targetPath); 
                // Pour la démo J3 :
                $results = $service->search("https://i.imgur.com/example.jpg");

                echo $this->twig->render('article/result.html.twig', ['articles' => $results]);
                return;
            }
        }
        echo "Erreur upload";
    }
} 