<?php

namespace App\Services;

class OpenAIService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.openai.com/v1';

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY', '');
    }

    /**
     * Extract product dimensions from specifications
     */
    public function extractDimensions(string $productName, ?string $specifications): ?array
    {
        if (empty($this->apiKey)) {
            return null;
        }

        $specs = '';
        if (!empty($specifications)) {
            $specsArray = json_decode($specifications, true);
            if (is_array($specsArray)) {
                foreach ($specsArray as $key => $value) {
                    $specs .= "$key: $value\n";
                }
            }
        }

        $prompt = <<<PROMPT
Produto: {$productName}

Especificacoes:
{$specs}

TAREFA: Calcular peso e dimensoes da CAIXA para envio.

PASSO 1 - CONVERTER UNIDADES:
- Libras (lb) para kg: multiplicar por 0.4536
- Gramas (g) para kg: dividir por 1000
- Polegadas (") para cm: multiplicar por 2.54
- Milimetros (mm) para cm: dividir por 10

PASSO 2 - ADICIONAR EMBALAGEM:
- Peso: adicionar 15% ao peso convertido
- Dimensoes: adicionar 5cm a cada dimensao convertida

EXEMPLO:
Se especificacao diz "15.8 lb" e "17.2 x 14.8 x 7 polegadas":
- Peso: 15.8 * 0.4536 = 7.17 kg, com embalagem: 7.17 * 1.15 = 8.2 kg
- Largura: 17.2 * 2.54 = 43.7 cm, com embalagem: 43.7 + 5 = 49 cm
- Altura: 14.8 * 2.54 = 37.6 cm, com embalagem: 37.6 + 5 = 43 cm
- Comprimento: 7 * 2.54 = 17.8 cm, com embalagem: 17.8 + 5 = 23 cm

RESPOSTA (somente JSON):
{"weight": 8.2, "width": 49, "height": 43, "length": 23}
PROMPT;

        try {
            $response = $this->chat($prompt);

            // Extract JSON from response
            if (preg_match('/\{[^}]+\}/', $response, $matches)) {
                $data = json_decode($matches[0], true);

                if ($data && isset($data['weight'], $data['width'], $data['height'], $data['length'])) {
                    return [
                        'weight' => max(0.1, (float) $data['weight']),
                        'width' => max(5, (int) $data['width']),
                        'height' => max(2, (int) $data['height']),
                        'length' => max(5, (int) $data['length']),
                    ];
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'OpenAI Error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Chat completion
     */
    public function chat(string $prompt, string $model = 'gpt-4o-mini'): string
    {
        $payload = [
            'model' => $model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.3,
            'max_tokens' => 150,
        ];

        $response = $this->request('POST', '/chat/completions', $payload);

        return $response['choices'][0]['message']['content'] ?? '';
    }

    /**
     * Make API request
     */
    protected function request(string $method, string $endpoint, array $data = []): array
    {
        $ch = curl_init();

        $url = $this->baseUrl . $endpoint;

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey,
        ];

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            throw new \Exception('cURL Error: ' . $error);
        }

        $decoded = json_decode($response, true) ?? [];

        if ($httpCode >= 400) {
            $errorMsg = $decoded['error']['message'] ?? 'Unknown error';
            throw new \Exception('API Error: ' . $errorMsg);
        }

        return $decoded;
    }
}
