<?php

namespace App\Services;

class MelhorEnvioService
{
    protected string $token;
    protected string $baseUrl;
    protected string $cepOrigem;
    protected int $handlingTime;

    public function __construct()
    {
        $this->token = env('melhorenvio.token', '');
        $this->cepOrigem = env('melhorenvio.cepOrigem', '01310100');
        $this->handlingTime = config('Shipping')->handlingTime ?? 3;

        // Sandbox or production URL
        $sandboxEnv = env('melhorenvio.sandbox', 'false');
        $sandbox = ($sandboxEnv === true || $sandboxEnv === 'true' || $sandboxEnv === '1');

        $this->baseUrl = $sandbox
            ? 'https://sandbox.melhorenvio.com.br/api/v2'
            : 'https://melhorenvio.com.br/api/v2';
    }

    /**
     * Format CEP - returns with hyphen (XXXXX-XXX)
     */
    protected function formatCep(string $cep): string
    {
        $cep = preg_replace('/\D/', '', $cep);
        // Garantir que tem 8 dígitos
        if (strlen($cep) < 8) {
            $cep = str_pad($cep, 8, '0', STR_PAD_LEFT);
        }
        $cep = substr($cep, 0, 8);
        // Formatar com hífen: XXXXX-XXX
        return substr($cep, 0, 5) . '-' . substr($cep, 5, 3);
    }

    /**
     * Calculate shipping for products
     */
    public function calculate(string $cepDestino, array $products): array
    {
        // Get token from database settings
        $token = $this->getAccessToken();
        if (empty($token)) {
            return $this->fallbackCalculation($cepDestino, $products);
        }

        // Prepare products for API
        $apiProducts = [];
        $totalValue = 0;

        foreach ($products as $product) {
            $apiProducts[] = [
                'id' => $product['id'] ?? uniqid(),
                'width' => (int) ($product['width'] ?? 11),
                'height' => (int) ($product['height'] ?? 2),
                'length' => (int) ($product['length'] ?? 16),
                'weight' => (float) ($product['weight'] ?? 0.3),
                'insurance_value' => (float) ($product['price'] ?? 0),
                'quantity' => (int) ($product['quantity'] ?? 1),
            ];
            $totalValue += ($product['price'] ?? 0) * ($product['quantity'] ?? 1);
        }

        $payload = [
            'from' => ['postal_code' => $this->formatCep($this->cepOrigem)],
            'to' => ['postal_code' => $this->formatCep($cepDestino)],
            'products' => $apiProducts,
            'options' => [
                'insurance_value' => $totalValue,
                'receipt' => false,
                'own_hand' => false,
            ],
            'services' => '1,2' // PAC, SEDEX
        ];

        try {
            $this->token = $token;
            $response = $this->request('POST', '/me/shipment/calculate', $payload);

            if (empty($response)) {
                return $this->fallbackCalculation($cepDestino, $products);
            }

            return $this->formatResponse($response, $totalValue);

        } catch (\Exception $e) {
            log_message('error', 'MelhorEnvio Error: ' . $e->getMessage());
            return $this->fallbackCalculation($cepDestino, $products);
        }
    }

    /**
     * Make API request
     */
    protected function request(string $method, string $endpoint, array $data = []): array
    {
        $ch = curl_init();

        $url = $this->baseUrl . $endpoint;

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token,
            'User-Agent: GPSImports/1.0',
        ];

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        // Configurar CA bundle para SSL (Windows/WAMP) se existir
        $caBundles = [
            'C:\\wamp64\\bin\\php\\php8.1.31\\extras\\ssl\\cacert.pem',
            '/etc/ssl/certs/ca-certificates.crt',
            '/etc/pki/tls/certs/ca-bundle.crt',
        ];
        foreach ($caBundles as $caBundle) {
            if (file_exists($caBundle)) {
                curl_setopt($ch, CURLOPT_CAINFO, $caBundle);
                break;
            }
        }

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'GET' && !empty($data)) {
            $url .= '?' . http_build_query($data);
            curl_setopt($ch, CURLOPT_URL, $url);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            throw new \Exception('cURL Error: ' . $error);
        }

        if ($httpCode >= 400) {
            throw new \Exception('API Error: HTTP ' . $httpCode);
        }

        return json_decode($response, true) ?? [];
    }

    /**
     * Format API response
     */
    protected function formatResponse(array $response, float $totalValue): array
    {
        $options = [];

        foreach ($response as $service) {
            // Skip if error or no price
            if (!empty($service['error']) || empty($service['price'])) {
                continue;
            }

            $price = (float) $service['price'];

            $options[] = [
                'code' => $service['id'] ?? '',
                'name' => $service['name'] ?? 'Envio',
                'company' => $service['company']['name'] ?? 'Transportadora',
                'price' => $price,
                'deadline' => (int) ($service['delivery_time'] ?? 10),
                'logo' => $service['company']['picture'] ?? null,
            ];
        }

        // Sort by price
        usort($options, fn($a, $b) => $a['price'] <=> $b['price']);

        return $options;
    }

    /**
     * Fallback calculation when API is not available
     */
    /**
     * Get access token from settings
     */
    protected function getAccessToken(): string
    {
        if (!empty($this->token)) {
            return $this->token;
        }

        $settingModel = model('SettingModel');
        return $settingModel->get('melhorenvio_access_token') ?? '';
    }

    /**
     * Get wallet balance
     */
    public function getBalance(): ?float
    {
        $token = $this->getAccessToken();
        if (empty($token)) {
            log_message('debug', 'MelhorEnvio getBalance: Token vazio');
            return null;
        }

        try {
            $this->token = $token;

            // Tentar endpoint /me/balance primeiro
            $response = $this->requestWithDetails('GET', '/me/balance', []);

            log_message('debug', 'MelhorEnvio getBalance /me/balance Response: ' . json_encode($response));

            // Verificar diferentes formatos de resposta
            $balance = $this->extractBalance($response);
            if ($balance !== null) {
                return $balance;
            }

            // Se nao encontrou, tentar /me/shipment/balance
            $response = $this->requestWithDetails('GET', '/me/shipment/balance', []);
            log_message('debug', 'MelhorEnvio getBalance /me/shipment/balance Response: ' . json_encode($response));

            return $this->extractBalance($response);

        } catch (\Exception $e) {
            log_message('error', 'MelhorEnvio getBalance Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Extract balance from API response
     */
    protected function extractBalance($response): ?float
    {
        // Formato 1: {'balance': 8.42}
        if (isset($response['balance'])) {
            return (float) $response['balance'];
        }

        // Formato 2: {'data': {'balance': 8.42}}
        if (isset($response['data']['balance'])) {
            return (float) $response['data']['balance'];
        }

        // Formato 3: [{'balance': 8.42}] - array
        if (is_array($response) && isset($response[0]['balance'])) {
            return (float) $response[0]['balance'];
        }

        // Formato 4: valor direto como numero
        if (is_numeric($response)) {
            return (float) $response;
        }

        return null;
    }

    /**
     * Check if connected
     */
    public function isConnected(): bool
    {
        $token = $this->getAccessToken();
        return !empty($token);
    }

    /**
     * Add credits to wallet
     */
    public function addCredits(float $value, string $method = 'pix'): array
    {
        $token = $this->getAccessToken();
        if (empty($token)) {
            return ['success' => false, 'message' => 'Token do Melhor Envio nao configurado. Acesse /melhor-envio/autorizar'];
        }

        // Formato da API Melhor Envio - usar yapay-transparente com slug pix ou boleto
        $payload = [
            'gateway' => 'yapay-transparente',
            'value' => number_format($value, 2, '.', ''),
            'slug' => $method, // 'pix' ou 'boleto'
        ];

        try {
            $this->token = $token;

            // Log para debug
            log_message('debug', 'MelhorEnvio addCredits Request: ' . json_encode($payload));

            $response = $this->requestWithDetails('POST', '/me/balance', $payload);

            log_message('debug', 'MelhorEnvio addCredits Response: ' . json_encode($response));

            // Formato yapay-transparente:
            // - digitable: codigo PIX copia e cola
            // - redirect: URL para pagamento/QR code
            // - payment.link: link alternativo

            // PIX: tem digitable (codigo copia e cola) ou redirect (link)
            if (!empty($response['digitable']) || !empty($response['redirect'])) {
                return [
                    'success' => true,
                    'id' => $response['id'] ?? null,
                    'pix_code' => $response['digitable'] ?? null,
                    'link' => $response['redirect'] ?? $response['payment']['link'] ?? null,
                    'data' => $response,
                ];
            }

            // Link direto de pagamento
            if (!empty($response['link'])) {
                return [
                    'success' => true,
                    'id' => $response['id'] ?? null,
                    'link' => $response['link'],
                    'data' => $response,
                ];
            }

            // payment.link alternativo
            if (!empty($response['payment']['link'])) {
                return [
                    'success' => true,
                    'id' => $response['id'] ?? null,
                    'link' => $response['payment']['link'],
                    'data' => $response,
                ];
            }

            // ID de transacao criada
            if (!empty($response['id'])) {
                $paymentLink = str_replace('/api/v2', '', $this->baseUrl) . '/painel/carteira/pagamento/' . $response['id'];
                return [
                    'success' => true,
                    'id' => $response['id'],
                    'link' => $paymentLink,
                    'data' => $response,
                ];
            }

            // Se a resposta tem erro
            $errorMsg = $response['message'] ?? $response['error'] ?? 'Resposta inesperada da API';
            if (isset($response['errors']) && is_array($response['errors'])) {
                $errorsFlat = [];
                foreach ($response['errors'] as $field => $msgs) {
                    if (is_array($msgs)) {
                        $errorsFlat[] = $field . ': ' . implode(', ', $msgs);
                    } else {
                        $errorsFlat[] = $msgs;
                    }
                }
                $errorMsg = implode(' | ', $errorsFlat);
            }

            // Log da resposta completa para debug
            log_message('error', 'MelhorEnvio addCredits - Resposta sem link: ' . json_encode($response));

            return [
                'success' => false,
                'message' => $errorMsg,
                'data' => $response,
            ];

        } catch (\Exception $e) {
            log_message('error', 'MelhorEnvio addCredits Error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Make API request with full response details
     */
    protected function requestWithDetails(string $method, string $endpoint, array $data = []): array
    {
        $ch = curl_init();

        $url = $this->baseUrl . $endpoint;

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token,
            'User-Agent: GPSImports/1.0',
        ];

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        // Configurar CA bundle para SSL se existir (Windows/WAMP ou Linux)
        $caBundles = [
            'C:\\wamp64\\bin\\php\\php8.1.31\\extras\\ssl\\cacert.pem',
            '/etc/ssl/certs/ca-certificates.crt',
            '/etc/pki/tls/certs/ca-bundle.crt',
        ];
        foreach ($caBundles as $caBundle) {
            if (file_exists($caBundle)) {
                curl_setopt($ch, CURLOPT_CAINFO, $caBundle);
                break;
            }
        }

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'GET' && !empty($data)) {
            $url .= '?' . http_build_query($data);
            curl_setopt($ch, CURLOPT_URL, $url);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            throw new \Exception('cURL Error: ' . $error);
        }

        $decoded = json_decode($response, true) ?? [];

        // Log HTTP errors but return response for better error handling
        if ($httpCode >= 400) {
            log_message('error', "MelhorEnvio API Error HTTP {$httpCode}: " . $response);
        }

        return $decoded;
    }

    /**
     * Add item to cart (first step to generate label)
     */
    public function addToCart(array $order, array $package, int $serviceId): array
    {
        $token = $this->getAccessToken();
        if (empty($token)) {
            return ['success' => false, 'message' => 'Token do Melhor Envio nao configurado. Acesse /melhor-envio/autorizar'];
        }

        $payload = [
            'service' => $serviceId,
            'agency' => null,
            'from' => [
                'name' => env('melhorenvio.senderName', 'GPS Imports'),
                'phone' => env('melhorenvio.senderPhone', ''),
                'email' => env('melhorenvio.senderEmail', ''),
                'document' => env('melhorenvio.senderCpf', ''),
                'company_document' => env('melhorenvio.senderCnpj', ''),
                'state_register' => '',
                'address' => env('melhorenvio.senderStreet', ''),
                'complement' => env('melhorenvio.senderComplement', ''),
                'number' => env('melhorenvio.senderNumber', ''),
                'district' => env('melhorenvio.senderDistrict', ''),
                'city' => env('melhorenvio.senderCity', ''),
                'country_id' => 'BR',
                'postal_code' => $this->formatCep($this->cepOrigem),
                'note' => '',
            ],
            'to' => [
                'name' => trim(substr($order['shipping_name'] ?? 'Cliente', 0, 60)),
                'phone' => preg_replace('/\D/', '', $order['shipping_phone'] ?? ''),
                'email' => trim($order['customer']['email'] ?? $order['customer_email'] ?? ''),
                'document' => preg_replace('/\D/', '', $order['billing_cpf'] ?? ''),
                'company_document' => '',
                'state_register' => '',
                'address' => trim(substr($order['shipping_street'] ?? '', 0, 60)),
                'complement' => trim(substr($order['shipping_complement'] ?? '', 0, 60)),
                'number' => trim(substr($order['shipping_number'] ?? 'SN', 0, 10)),
                'district' => trim(substr($order['shipping_neighborhood'] ?? '', 0, 60)),
                'city' => trim(substr($order['shipping_city'] ?? '', 0, 60)),
                'state_abbr' => strtoupper(trim(substr($order['shipping_state'] ?? '', 0, 2))),
                'country_id' => 'BR',
                'postal_code' => $this->formatCep(trim($order['shipping_zipcode'] ?? '')),
                'note' => '',
            ],
            'products' => [
                [
                    'name' => 'Pedido #' . $order['order_number'],
                    'quantity' => 1,
                    'unitary_value' => (float) $order['total'],
                ]
            ],
            'volumes' => [
                [
                    'height' => (int) $package['height'],
                    'width' => (int) $package['width'],
                    'length' => (int) $package['length'],
                    'weight' => (float) $package['weight'],
                ]
            ],
            'options' => [
                'insurance_value' => (float) $order['total'],
                'receipt' => false,
                'own_hand' => false,
                'reverse' => false,
                // Envio comercial para valores acima de R$ 4.477,36
                'non_commercial' => ((float) $order['total'] <= 4477.36),
                'invoice' => [
                    'key' => '',
                ],
                'platform' => 'GPS Imports',
                'tags' => [
                    [
                        'tag' => 'Pedido #' . $order['order_number'],
                        'url' => base_url('admin/pedidos/' . $order['id']),
                    ]
                ],
            ],
        ];

        try {
            $this->token = $token;

            // Log do payload para debug
            log_message('error', 'MelhorEnvio addToCart Payload: ' . json_encode($payload, JSON_UNESCAPED_UNICODE));
            file_put_contents(WRITEPATH . 'logs/melhorenvio_debug.log', date('Y-m-d H:i:s') . " PAYLOAD:\n" . json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n", FILE_APPEND);

            $response = $this->requestWithDetails('POST', '/me/cart', $payload);

            // Log da resposta
            log_message('error', 'MelhorEnvio addToCart Response: ' . json_encode($response, JSON_UNESCAPED_UNICODE));
            file_put_contents(WRITEPATH . 'logs/melhorenvio_debug.log', date('Y-m-d H:i:s') . " RESPONSE:\n" . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n", FILE_APPEND);

            if (!empty($response['id'])) {
                return [
                    'success' => true,
                    'cart_id' => $response['id'],
                    'data' => $response,
                ];
            }

            // Capturar erro detalhado
            $errorMsg = $response['message'] ?? 'Erro ao adicionar ao carrinho';
            if (!empty($response['errors'])) {
                $errorsDetail = [];
                foreach ($response['errors'] as $field => $msgs) {
                    if (is_array($msgs)) {
                        $errorsDetail[] = $field . ': ' . implode(', ', $msgs);
                    } else {
                        $errorsDetail[] = $field . ': ' . $msgs;
                    }
                }
                $errorMsg = implode(' | ', $errorsDetail);
            }

            return [
                'success' => false,
                'message' => $errorMsg,
                'errors' => $response['errors'] ?? [],
            ];

        } catch (\Exception $e) {
            log_message('error', 'MelhorEnvio addToCart Error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Checkout cart (pay for label)
     */
    public function checkout(array $cartIds): array
    {
        $token = $this->getAccessToken();
        if (empty($token)) {
            return ['success' => false, 'message' => 'Token nao configurado'];
        }

        $payload = ['orders' => $cartIds];

        try {
            $this->token = $token;

            // Log do payload para debug
            log_message('debug', 'MelhorEnvio checkout Payload: ' . json_encode($payload));

            $response = $this->requestWithDetails('POST', '/me/shipment/checkout', $payload);

            // Log da resposta
            log_message('debug', 'MelhorEnvio checkout Response: ' . json_encode($response));

            if (!empty($response['purchase'])) {
                return [
                    'success' => true,
                    'purchase' => $response['purchase'],
                    'data' => $response,
                ];
            }

            // Capturar erro detalhado
            $errorMsg = $response['message'] ?? 'Erro no checkout';
            if (!empty($response['errors'])) {
                $errorsDetail = [];
                foreach ($response['errors'] as $field => $msgs) {
                    if (is_array($msgs)) {
                        $errorsDetail[] = $field . ': ' . implode(', ', $msgs);
                    } else {
                        $errorsDetail[] = $field . ': ' . $msgs;
                    }
                }
                $errorMsg = implode(' | ', $errorsDetail);
            }

            // Verificar saldo insuficiente
            if (isset($response['message']) && stripos($response['message'], 'saldo') !== false) {
                $balance = $this->getBalance();
                $errorMsg .= ' (Saldo atual: R$ ' . number_format($balance ?? 0, 2, ',', '.') . ')';
            }

            return [
                'success' => false,
                'message' => $errorMsg,
                'data' => $response,
            ];

        } catch (\Exception $e) {
            log_message('error', 'MelhorEnvio checkout Error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Generate label (after checkout)
     */
    public function generateLabel(array $cartIds): array
    {
        $token = $this->getAccessToken();
        if (empty($token)) {
            return ['success' => false, 'message' => 'Token nao configurado'];
        }

        $payload = ['orders' => $cartIds];

        try {
            $this->token = $token;

            // Log do payload para debug
            log_message('debug', 'MelhorEnvio generateLabel Payload: ' . json_encode($payload));

            $response = $this->requestWithDetails('POST', '/me/shipment/generate', $payload);

            // Log da resposta
            log_message('debug', 'MelhorEnvio generateLabel Response: ' . json_encode($response));

            // Verificar se gerou com sucesso
            if (!empty($response) && !isset($response['errors'])) {
                return [
                    'success' => true,
                    'data' => $response,
                ];
            }

            // Capturar erro detalhado
            $errorMsg = $response['message'] ?? 'Erro ao gerar etiqueta';
            if (!empty($response['errors'])) {
                $errorsDetail = [];
                foreach ($response['errors'] as $field => $msgs) {
                    if (is_array($msgs)) {
                        $errorsDetail[] = $field . ': ' . implode(', ', $msgs);
                    } else {
                        $errorsDetail[] = $field . ': ' . $msgs;
                    }
                }
                $errorMsg = implode(' | ', $errorsDetail);
            }

            return [
                'success' => false,
                'message' => $errorMsg,
                'data' => $response,
            ];

        } catch (\Exception $e) {
            log_message('error', 'MelhorEnvio generateLabel Error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Print label
     */
    public function printLabel(array $cartIds): array
    {
        $token = $this->getAccessToken();
        if (empty($token)) {
            return ['success' => false, 'message' => 'Token nao configurado'];
        }

        $payload = [
            'mode' => 'public',
            'orders' => $cartIds,
        ];

        try {
            $this->token = $token;
            $response = $this->request('POST', '/me/shipment/print', $payload);

            if (!empty($response['url'])) {
                return [
                    'success' => true,
                    'url' => $response['url'],
                ];
            }

            return [
                'success' => false,
                'message' => 'Erro ao gerar URL da etiqueta',
            ];

        } catch (\Exception $e) {
            log_message('error', 'MelhorEnvio printLabel Error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Track shipment
     */
    public function tracking(array $cartIds): array
    {
        $token = $this->getAccessToken();
        if (empty($token)) {
            return ['success' => false, 'message' => 'Token nao configurado'];
        }

        $payload = ['orders' => $cartIds];

        try {
            $this->token = $token;
            $response = $this->request('POST', '/me/shipment/tracking', $payload);

            return [
                'success' => true,
                'data' => $response,
            ];

        } catch (\Exception $e) {
            log_message('error', 'MelhorEnvio tracking Error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Quote shipping for order
     */
    public function quoteForOrder(array $order, array $package): array
    {
        $cepDestino = $order['shipping_zipcode'] ?? '';
        $fallbackProducts = [
            ['weight' => $package['weight'], 'width' => $package['width'], 'height' => $package['height'], 'length' => $package['length'], 'price' => $order['total'] ?? 0, 'quantity' => 1]
        ];

        // Se não tiver CEP, retorna fallback
        if (empty($cepDestino)) {
            log_message('debug', 'MelhorEnvio quoteForOrder: CEP destino vazio, usando fallback');
            return $this->fallbackCalculation('01310100', $fallbackProducts);
        }

        $token = $this->getAccessToken();
        if (empty($token)) {
            log_message('debug', 'MelhorEnvio quoteForOrder: Token vazio, usando fallback');
            return $this->fallbackCalculation($cepDestino, $fallbackProducts);
        }

        $payload = [
            'from' => ['postal_code' => $this->formatCep($this->cepOrigem)],
            'to' => ['postal_code' => $this->formatCep($cepDestino)],
            'products' => [
                [
                    'id' => 'order_' . ($order['id'] ?? 0),
                    'width' => (int) $package['width'],
                    'height' => (int) $package['height'],
                    'length' => (int) $package['length'],
                    'weight' => (float) $package['weight'],
                    'insurance_value' => (float) ($order['total'] ?? 0),
                    'quantity' => 1,
                ]
            ],
        ];

        try {
            $this->token = $token;
            log_message('debug', 'MelhorEnvio quoteForOrder Payload: ' . json_encode($payload));

            $response = $this->request('POST', '/me/shipment/calculate', $payload);

            log_message('debug', 'MelhorEnvio quoteForOrder Response: ' . json_encode($response));

            if (empty($response)) {
                log_message('debug', 'MelhorEnvio quoteForOrder: Resposta vazia, usando fallback');
                return $this->fallbackCalculation($cepDestino, $fallbackProducts);
            }

            $formatted = $this->formatResponse($response, $order['total'] ?? 0);

            // Se não conseguiu formatar nenhuma opção, usa fallback
            if (empty($formatted)) {
                log_message('debug', 'MelhorEnvio quoteForOrder: Nenhuma opcao formatada, usando fallback');
                return $this->fallbackCalculation($cepDestino, $fallbackProducts);
            }

            return $formatted;

        } catch (\Exception $e) {
            log_message('error', 'MelhorEnvio quoteForOrder Error: ' . $e->getMessage());
            return $this->fallbackCalculation($cepDestino, $fallbackProducts);
        }
    }

    /**
     * Fallback calculation when API is not available
     */
    protected function fallbackCalculation(string $cepDestino, array $products): array
    {
        $cep = preg_replace('/[^0-9]/', '', $cepDestino);
        $region = substr($cep, 0, 1);

        // Calculate total weight and value
        $totalWeight = 0;
        $totalValue = 0;
        $maxWidth = 0;
        $maxHeight = 0;
        $maxLength = 0;

        foreach ($products as $product) {
            $qty = (int) ($product['quantity'] ?? 1);
            $totalWeight += (float) ($product['weight'] ?? 0.3) * $qty;
            $totalValue += (float) ($product['price'] ?? 0) * $qty;
            $maxWidth = max($maxWidth, (int) ($product['width'] ?? 11));
            $maxHeight = max($maxHeight, (int) ($product['height'] ?? 2));
            $maxLength = max($maxLength, (int) ($product['length'] ?? 16));
        }

        // Region multiplier
        $regionMultiplier = match($region) {
            '0', '1' => 1.0,   // SP
            '2' => 1.15,       // RJ, ES
            '3' => 1.25,       // MG
            '4' => 1.35,       // BA, SE
            '5' => 1.45,       // PE, AL, PB, RN
            '6' => 1.60,       // CE, PI, MA, PA, AP, AM
            '7' => 1.40,       // DF, GO, TO, MT, MS, RO, AC
            '8' => 1.20,       // PR, SC
            '9' => 1.30,       // RS
            default => 1.35,
        };

        // Calculate base price (weight + volume)
        $volumetricWeight = ($maxWidth * $maxHeight * $maxLength) / 6000;
        $chargeableWeight = max($totalWeight, $volumetricWeight);
        $basePrice = max(18, $chargeableWeight * 12);

        // Dobrar o valor do frete (fallback = estimativa com margem de seguranca)
        $fallbackMultiplier = 2;

        $options = [];

        // PAC
        $pacPrice = round($basePrice * $regionMultiplier * $fallbackMultiplier, 2);
        $pacDeadline = 7 + (int) $region;

        $options[] = [
            'code' => 'PAC',
            'name' => 'PAC',
            'company' => 'Correios',
            'price' => $pacPrice,
            'deadline' => $pacDeadline,
            'logo' => null,
        ];

        // SEDEX
        $sedexPrice = round($basePrice * $regionMultiplier * 1.9 * $fallbackMultiplier, 2);
        $sedexDeadline = 2 + (int) floor((int) $region / 3);

        $options[] = [
            'code' => 'SEDEX',
            'name' => 'SEDEX',
            'company' => 'Correios',
            'price' => $sedexPrice,
            'deadline' => $sedexDeadline,
            'logo' => null,
        ];

        return $options;
    }
}
