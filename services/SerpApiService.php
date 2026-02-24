<?php
/**
 * @file    SerpApiService.php
 * @author  Paul 
 * @brief   Service gérant les appels à SerpAPI (Google Lens).
 *          Gère l'upload direct d'image locale (POST) pour éviter les soucis de localhost.
 * @version 1.2
 * @date    24/02/2026
 */

class SerpApiService {
    private string $apiKey;
    
    public function __construct(string $apiKey) {
        $this->apiKey = $apiKey;
    }

    /**
     * @brief   Envoie une image à Google Lens via SerpAPI.
     * @param   string $imageSource URL de l'image (doit être accessible publiquement).
     * @return  array Liste des résultats (titre, source, prix, image).
     * @throws  Exception En cas de problème de connexion ou d'API.
     */
    public function search(string $imageSource): array {
        $ch = curl_init();
        
        $params = [
            'engine' => 'google_lens',
            'api_key' => $this->apiKey,
            'url' => $imageSource
        ];

        $endpoint = "https://serpapi.com/search.json?" . http_build_query($params);
        
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Timeout de 30 secondes
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Timeout de connexion de 10s

        $response = curl_exec($ch);

        // GESTION DES ERREURS DE CONNEXION
        if (curl_errno($ch)) {
            $errorNo = curl_errno($ch);
            curl_close($ch);
            
            if ($errorNo === CURLE_COULDNT_RESOLVE_HOST || $errorNo === CURLE_COULDNT_CONNECT) {
                throw new Exception("Erreur de connexion : Impossible de contacter SerpAPI. Vérifiez votre connexion Internet.");
            } elseif ($errorNo === CURLE_OPERATION_TIMEDOUT) {
                throw new Exception("Le service de recherche a mis trop de temps à répondre (Timeout).");
            } else {
                throw new Exception("Erreur réseau lors de l'appel à l'API : " . curl_error($ch));
            }
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Le service de recherche a retourné une erreur (Code HTTP $httpCode).");
        }

        $data = json_decode($response, true);
        
        if (!$data || isset($data['error'])) {
            $errMsg = $data['error'] ?? "Réponse de l'API invalide.";
            throw new Exception("Erreur SerpAPI : " . $errMsg);
        }

        $results = [];
        if (isset($data['visual_matches']) && is_array($data['visual_matches'])) {
            $count = 0;
            foreach ($data['visual_matches'] as $match) {
                if ($count >= 20) break; 
                
                $results[] = [
                    'titre'  => $match['title'] ?? 'Article inconnu',
                    'source' => $match['source'] ?? 'Source inconnue',
                    'image'  => $match['thumbnail'] ?? '',
                    'url'    => $match['link'] ?? '#',
                    'prix'   => $match['price']['value'] ?? 'N/C'
                ];
                $count++;
            }
        }
        
        return $results;
    }
}