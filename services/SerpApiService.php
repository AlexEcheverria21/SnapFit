<?php
class SerpApiService {
    private string $apiKey;
    
    public function __construct(string $apiKey) {
        $this->apiKey = $apiKey;
    }

    public function search(string $imageUrl) {
        $url = "https://serpapi.com/search.json?engine=google_lens&url=" . urlencode($imageUrl) . "&api_key=" . $this->apiKey;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        if (isset($data['visual_matches'])) {
            return $data['visual_matches'];
        }
        return [];
    }
}