<?php
/**
 * @file    controller_article.class.php
 * @author  Louis (Team SnapFit)
 * @brief   Définit un contrôleur gérant les articles (scan, upload, recherche).
 *          Intègre l'API Google Lens (SerpAPI) et le filtrage Anti-Scam.
 * @version 1.2
 * @date    24/02/2026
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
     * @brief   Affiche le squelette des résultats de la recherche.
     *          La recherche réelle est effectuée en AJAX via api_results().
     */
    public function result() {
        $imageScan = $_GET['scan'] ?? null;
        
        echo $this->twig->render('article/result.html.twig', [
            'scanImage' => $imageScan,
            'articles' => [],
            'nbBloques' => 0
        ]);
    }

    /**
     * @brief   Endpoint API retournant les résultats de recherche au format JSON.
     *          Utilisé pour permettre l'interruption de la recherche côté client.
     */
    public function api_results() {
        header('Content-Type: application/json');
        
        $imageScan = $_GET['scan'] ?? null;
        if (!$imageScan) {
            echo json_encode(['error' => 'Aucune image spécifiée']);
            exit;
        }

        try {
            global $config;
            $apiKey = $config['api']['serpapi_key'] ?? '';
            
            $dossierUpload = 'public/uploads/';
            $cheminLocal = realpath($dossierUpload . $imageScan);
            
            if (!$cheminLocal || !file_exists($cheminLocal)) {
                throw new Exception("Image introuvable sur le serveur.");
            }

            // Étape 1 : Upload vers hôte temporaire (Nécessite Internet)
            $urlImageApi = $this->uploadToEphemeralHost($cheminLocal);

            if (!$urlImageApi) {
                throw new Exception("L'upload temporaire a échoué sans erreur précise.");
            }

            // Étape 2 : Appel SerpAPI (Nécessite Internet)
            $service = new SerpApiService($apiKey);
            $rawResults = $service->search($urlImageApi);
            
            $pdo = Bd::getInstance()->getConnexion();
            $sqlScam = "SELECT url_racine FROM DOMAINE WHERE statut = 'scam'";
            $stmtScam = $pdo->query($sqlScam);
            $scamDomains = $stmtScam->fetchAll(PDO::FETCH_COLUMN);

            $sqlEco = "SELECT url_racine FROM DOMAINE WHERE statut = 'eco'";
            $stmtEco = $pdo->query($sqlEco);
            $ecoDomains = $stmtEco->fetchAll(PDO::FETCH_COLUMN);
            
            $articles = [];
            $nbBloques = 0;

            foreach ($rawResults as $res) {
                $estScam = false;
                foreach ($scamDomains as $scam) {
                    if (stripos($res['source'], $scam) !== false || stripos($res['url'], $scam) !== false) {
                        $estScam = true;
                        break;
                    }
                }
                
                if ($estScam) {
                    $nbBloques++;
                } else {
                    $res['label'] = null;
                    foreach ($ecoDomains as $eco) {
                         if (stripos($res['source'], $eco) !== false || stripos($res['url'], $eco) !== false) {
                             $res['label'] = 'eco';
                             break;
                         }
                    }
                    $articles[] = $res;
                }
            }

            usort($articles, function($a, $b) {
                $isEcoA = isset($a['label']) && $a['label'] === 'eco';
                $isEcoB = isset($b['label']) && $b['label'] === 'eco';
                return $isEcoB <=> $isEcoA; 
            });

            if (isset($_SESSION['user_id'])) {
                $rechercheDao = new RechercheDao($pdo);
                $recherche = new Recherche($_SESSION['user_id'], $imageScan);
                $rechercheDao->add($recherche);
            }

            echo json_encode([
                'articles' => $articles,
                'nbBloques' => $nbBloques
            ]);

        } catch (Exception $e) {
            // On renvoie l'erreur en JSON pour que le JS puisse l'afficher proprement
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * @brief   Upload l'image vers un service temporaire (tmpfiles.org) pour obtenir une URL publique.
     * @param   string $filePath Chemin local du fichier.
     * @return  string|null L'URL publique ou null en cas d'échec.
     */
    private function uploadToEphemeralHost($filePath) {
        if (!file_exists($filePath)) return null;

        $ch = curl_init("https://tmpfiles.org/api/v1/upload");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        
        $cfile = new CURLFile($filePath);
        $data = ['file' => $cfile];
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            $err = curl_error($ch);
            curl_close($ch);
            throw new Exception("Problème de connexion : Votre ordinateur semble hors-ligne (Services d'upload inaccessibles).");
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Le serveur d'analyse est temporairement saturé ou indisponible (Code HTTP $httpCode).");
        }
        
        $json = json_decode($response, true);
        
        if (isset($json['data']['url'])) {
            return str_replace('tmpfiles.org/', 'tmpfiles.org/dl/', $json['data']['url']);
        }
        
        return null;
    }
}