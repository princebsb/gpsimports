<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class WebhookController extends BaseController
{
    /**
     * Webhook do Mercado Pago
     */
    public function mercadopago(): ResponseInterface
    {
        // Log da requisicao
        $rawBody = file_get_contents('php://input');
        log_message('info', 'MercadoPago Webhook received: ' . $rawBody);

        // Verificar se e uma notificacao valida
        $data = json_decode($rawBody, true);

        if (!$data) {
            // Tentar pegar dos parametros GET (notificacao IPN)
            $data = [
                'type' => $this->request->getGet('type'),
                'data' => [
                    'id' => $this->request->getGet('data_id') ?? $this->request->getGet('id'),
                ],
            ];
        }

        // Ignorar notificacoes de teste
        if (isset($data['type']) && $data['type'] === 'test') {
            return $this->response->setStatusCode(200)->setBody('OK');
        }

        // Processar apenas notificacoes de pagamento
        if (!isset($data['type']) || !in_array($data['type'], ['payment', 'merchant_order'])) {
            // Pode ser formato antigo
            if (!isset($data['data']['id']) && !isset($data['id'])) {
                return $this->response->setStatusCode(200)->setBody('OK');
            }
        }

        try {
            $paymentService = service('payment');
            $result = $paymentService->processCheckoutProWebhook($data);

            if ($result) {
                log_message('info', 'MercadoPago Webhook processed successfully');
            } else {
                log_message('warning', 'MercadoPago Webhook processing returned false');
            }

            return $this->response->setStatusCode(200)->setBody('OK');

        } catch (\Exception $e) {
            log_message('error', 'MercadoPago Webhook Error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setBody('Error');
        }
    }
}
