<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;

class TrackingController extends BaseController
{
    /**
     * Pagina de rastreamento de pedido
     */
    public function index()
    {
        $searchTerm = $this->request->getGet('pedido');
        $order = null;
        $error = null;
        $trackingCode = null;

        if ($searchTerm) {
            $searchTerm = trim($searchTerm);
            $orderModel = model('OrderModel');

            // Primeiro tenta buscar por numero do pedido
            $order = $orderModel->getByNumber($searchTerm);

            // Se não encontrou, tenta buscar por código de rastreio
            if (!$order) {
                $order = $orderModel->where('tracking_code', $searchTerm)->first();
            }

            if ($order) {
                // Buscar itens e historico
                $order = $orderModel->getWithItems($order['id']);
            } else {
                // Verificar se parece um código de rastreio dos Correios
                if (preg_match('/^[A-Z]{2}\d{9}[A-Z]{2}$/i', $searchTerm)) {
                    // É um código de rastreio, redirecionar direto para o Melhor Rastreio
                    $trackingCode = strtoupper($searchTerm);
                } else {
                    $error = 'Pedido nao encontrado. Verifique o numero e tente novamente.';
                }
            }
        }

        return view('front/tracking/index', [
            'title' => 'Rastrear Pedido',
            'order' => $order,
            'orderNumber' => $searchTerm,
            'trackingCode' => $trackingCode,
            'error' => $error,
        ]);
    }
}
