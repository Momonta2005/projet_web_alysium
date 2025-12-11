<?php
// Controller/GeminiController.php

require_once __DIR__ . '/../Model/config.php';

class GeminiController {
    
    private $apiKey;
    private $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

    public function __construct() {
        $this->apiKey = Config::getGeminiKey();
    }

    public function askGemini(string $prompt): string {
        $url = $this->apiUrl . '?key=' . $this->apiKey;

        $data = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ]
        ];

        $json_data = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Pour éviter les erreurs SSL en local (XAMPP)

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return "Erreur curl: " . $error_msg;
        }

        curl_close($ch);

        if ($httpCode === 200) {
            $decoded = json_decode($response, true);
            if (isset($decoded['candidates'][0]['content']['parts'][0]['text'])) {
                return $decoded['candidates'][0]['content']['parts'][0]['text'];
            }
        }
        
        return "Désolé, je n'ai pas pu obtenir de réponse. Code: " . $httpCode . ". Reponse: " . substr($response, 0, 100);
    }
}
