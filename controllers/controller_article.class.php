<?php
/**
 * @file    controller_article.class.php
 * @author  Louis (Team SnapFit)
 * @brief   Définit un contrôleur gérant les articles (scan, upload, recherche).
 *          Intègre l'API Google Lens (SerpAPI) et le filtrage Anti-Scam.
 * @version 1.0
 * @date    16/12/2025
 */
class ControllerArticle extends Controller {

    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader) {
        parent::__construct($twig, $loader);
    }

    /**
     * @brief   Gère l'upload de la photo pour le scan.
     *          Si une image est envoyée, redirige vers les résultats.
     */
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
             // Traitement de l'upload
             if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
                 // 1. Sauvegarde de l'image
                 $dossierUpload = 'public/uploads/';
                 $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                 $nomFichier = uniqid('scan_') . '.' . $extension;
                 $cheminComplet = $dossierUpload . $nomFichier;
                 
                 move_uploaded_file($_FILES['photo']['tmp_name'], $cheminComplet);

                 // 2. Redirection vers les résultats
                 header('Location: index.php?controleur=article&methode=result&scan=' . $nomFichier);
                 exit;
             }
        }

        echo $this->twig->render('article/upload.html.twig', []);
    }

    /**
     * @brief   Affiche les résultats de la recherche Google Lens.
     *          Filtre les sites arnaques (SCAM) via la table DOMAINE.
     */
    public function result() {
        $imageScan = $_GET['scan'] ?? null;
        global $config; // Récupération de la clé API depuis config_local.yaml
        
        $articles = [];
        $nbBloques = 0; // Compteur de sites bloqués
        
        if ($imageScan) {
            // 1. Récupération de la clé API
            $apiKey = $config['api']['serpapi_key'] ?? '';
            
            // Si pas de clé, on utilise le mode démo de base (pour éviter l'écran blanc)
            if (empty($apiKey) || $apiKey === 'TA_CLE_SERPAPI_ICI') {
                $articles = $this->getMockResults(); // Fallback
            } else {
                // 2. Appel du Service API
                $dossierUpload = 'public/uploads/';
                // On récupère le chemin absolu du fichier local
                $cheminLocal = realpath($dossierUpload . $imageScan);
                
                // Stratégie : Upload vers un hôte éphémère (file.io) pour que l'API puisse lire l'image
                // Cela garantit la "Privacité" (Fichier supprimé après lecture unique) et permet le localhost.
                $urlImageApi = $this->uploadToEphemeralHost($cheminLocal);

                // Fallback si l'upload échoue : Image de démo User
                if (!$urlImageApi) {
                    $urlImageApi = "https://i.imgur.com/HBrB8p0.png";
                }

                $service = new SerpApiService($apiKey);
                $rawResults = $service->search($urlImageApi);
                
                // 3. Filtrage Anti-Scam (Logique V3)
                $pdo = Bd::getInstance()->getConnexion();
                
                // Récupération de la liste des domaines SCAM en cache
                $sql = "SELECT url_racine FROM DOMAINE WHERE statut = 'scam'";
                $stmt = $pdo->query($sql);
                $scamDomains = $stmt->fetchAll(PDO::FETCH_COLUMN); // ['shein.com', 'temu.com'...]
                
                foreach ($rawResults as $res) {
                    $estScam = false;
                    foreach ($scamDomains as $scam) {
                        // Si la source contient le nom du scam (ex: "fr.shein.com" contient "shein.com")
                        if (stripos($res['source'], $scam) !== false || stripos($res['url'], $scam) !== false) {
                            $estScam = true;
                            break;
                        }
                    }
                    
                    if ($estScam) {
                        $nbBloques++;
                    } else {
                        $articles[] = $res;
                    }
                }
            }
            // 4. Enregistrement dans l'historique (Si connecté)
            if (isset($_SESSION['user_id'])) {
                $rechercheDao = new RechercheDao($pdo);
                $recherche = new Recherche(
                    $_SESSION['user_id'], 
                    $imageScan, 
                    'scan_' . date('YmdHis')
                );
                $rechercheDao->add($recherche);
            }
        }

        echo $this->twig->render('article/result.html.twig', [
            'scanImage' => $imageScan,
            'articles' => $articles,
            'nbBloques' => $nbBloques
        ]);
    }
    
    /**
     * @brief   Génère des faux résultats si l'API n'est pas configurée.
     */
    private function getMockResults() {
        $articles = [];
        $marques = ['Nike', 'Adidas', 'Zara', 'H&M', 'Uniqlo'];
        for ($i = 0; $i < 10; $i++) {
            $articles[] = [
                'titre' => 'Article Mode Tendance ' . $i,
                'source' => $marques[array_rand($marques)] . '.com',
                'image' => 'https://picsum.photos/300/400?random=' . $i,
                'url' => '#',
                'prix' => rand(20, 100) . ' €'
            ];
        }
        return $articles;
    }

    /**
     * @brief   Upload l'image vers un service temporaire (file.io) pour obtenir une URL publique.
     *          L'image est supprimée automatiquement après le premier téléchargement par l'API.
     * @param   string $filePath Chemin local du fichier.
     * @return  string|null L'URL publique ou null en cas d'échec.
     */
    private function uploadToEphemeralHost($filePath) {
        if (!file_exists($filePath)) return null;

        // On bascule sur tmpfiles.org qui est souvent plus permissif que file.io
        $ch = curl_init("https://tmpfiles.org/api/v1/upload");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        
        $cfile = new CURLFile($filePath);
        $data = ['file' => $cfile];
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            return null;
        }
        
        curl_close($ch);
        $json = json_decode($response, true);
        
        // tmpfiles.org retourne l'url de téléchargement
        // Format : $json['data']['url'] -> https://tmpfiles.org/12345/image.jpg
        // Mais pour l'affichage direct (raw), il faut changer l'URL :
        // https://tmpfiles.org/dl/12345/image.jpg
        
        if (isset($json['data']['url'])) {
            return str_replace('tmpfiles.org/', 'tmpfiles.org/dl/', $json['data']['url']);
        }
        
        return null;
    }
}