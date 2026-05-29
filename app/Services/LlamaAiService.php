<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LlamaAiService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('GROQ_API_KEY', '');
        $this->model = 'llama-3.3-70b-versatile'; // Modèle sur-puissant et très rapide
        $this->baseUrl = 'https://api.groq.com/openai/v1/chat/completions';
    }

    /**
     * Envoie une requête à l'API LLaMA (Groq).
     * 
     * @param array $messages Structure: [['role' => 'system', 'content' => '...'], ['role' => 'user', 'content' => '...']]
     * @param float $temperature 0.0 pour strict, 1.0 pour créatif
     * @return string La réponse de l'IA
     */
    public function generateResponse(array $messages, float $temperature = 0.5): string
    {
        if (empty($this->apiKey)) {
            Log::error("GROQ_API_KEY est manquante dans le fichier .env");
            return "Configuration IA incomplète. Veuillez contacter l'administrateur (Clé API manquante).";
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->post($this->baseUrl, [
                    'model' => $this->model,
                    'messages' => $messages,
                    'temperature' => $temperature,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? "Je n'ai pas pu générer une réponse.";
            }

            Log::error("Erreur API Groq : " . $response->body());
            return "Désolé, l'intelligence artificielle est actuellement indisponible. Erreur: " . $response->status();
        } catch (\Exception $e) {
            Log::error("Exception IA Groq : " . $e->getMessage());
            return "Désolé, une erreur technique s'est produite lors de la connexion à l'IA.";
        }
    }
}
