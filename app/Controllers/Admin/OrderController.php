<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class OrderController extends BaseController
{
    protected $orderModel;
    protected $orderService;

    public function __construct()
    {
        $this->orderModel = model('OrderModel');
        $this->orderService = service('order');
    }

    public function index()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'status' => $this->request->getGet('status'),
            'payment_status' => $this->request->getGet('payment_status'),
            'from_date' => $this->request->getGet('from_date'),
            'to_date' => $this->request->getGet('to_date'),
        ];

        $result = $this->orderModel->getFiltered($filters, 20);

        // Stats
        $db = \Config\Database::connect();
        $stats = [
            'pending' => $db->table('orders')->where('status', 'pending')->where('deleted_at IS NULL')->countAllResults(),
            'processing' => $db->table('orders')->whereIn('status', ['paid', 'processing'])->where('deleted_at IS NULL')->countAllResults(),
            'shipped' => $db->table('orders')->where('status', 'shipped')->where('deleted_at IS NULL')->countAllResults(),
            'delivered' => $db->table('orders')->where('status', 'delivered')->where('deleted_at IS NULL')->countAllResults(),
        ];

        return view('admin/orders/index', [
            'title' => 'Pedidos',
            'orders' => $result['orders'],
            'pager' => $result['pager'],
            'filters' => $filters,
            'stats' => $stats,
            'statuses' => [
                'pending' => 'Pendente',
                'paid' => 'Pago',
                'processing' => 'Em Preparacao',
                'shipped' => 'Enviado',
                'delivered' => 'Entregue',
                'cancelled' => 'Cancelado',
            ],
            'paymentStatuses' => [
                'pending' => 'Aguardando',
                'approved' => 'Aprovado',
                'rejected' => 'Rejeitado',
                'refunded' => 'Reembolsado',
            ],
        ]);
    }

    public function show($id)
    {
        $order = $this->orderModel->getWithItems($id);

        if (!$order) {
            return redirect()->to('/admin/pedidos')->with('error', 'Pedido nao encontrado.');
        }

        // Buscar saldo do Melhor Envio
        $melhorEnvio = new \App\Services\MelhorEnvioService();
        $meBalance = $melhorEnvio->getBalance();

        return view('admin/orders/show', [
            'title' => 'Pedido #' . $order['order_number'],
            'order' => $order,
            'meBalance' => $meBalance,
        ]);
    }

    public function updateStatus($id)
    {
        $status = $this->request->getPost('status');
        $comment = $this->request->getPost('comment');
        $notifyCustomer = (bool) $this->request->getPost('notify_customer');

        $result = $this->orderService->updateStatus($id, $status, $comment, $notifyCustomer);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => $result,
                'message' => $result ? 'Status atualizado!' : 'Erro ao atualizar status.',
            ]);
        }

        if (!$result) {
            return redirect()->back()->with('error', 'Erro ao atualizar status.');
        }

        return redirect()->back()->with('success', 'Status atualizado com sucesso!');
    }

    /**
     * Alterar status via URL (GET)
     */
    public function changeStatus($id, $status)
    {
        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'];

        if (!in_array($status, $validStatuses)) {
            return redirect()->to('/admin/pedidos/' . $id)->with('error', 'Status invalido.');
        }

        $result = $this->orderService->updateStatus($id, $status, null, false);

        if (!$result) {
            return redirect()->to('/admin/pedidos/' . $id)->with('error', 'Erro ao atualizar status.');
        }

        return redirect()->to('/admin/pedidos/' . $id)->with('success', 'Status alterado para ' . $status . '!');
    }

    public function addTracking($id)
    {
        $trackingCode = $this->request->getPost('tracking_code');
        $trackingUrl = $this->request->getPost('tracking_url');

        if (empty($trackingCode)) {
            return redirect()->back()->with('error', 'Codigo de rastreio obrigatorio.');
        }

        $result = $this->orderService->addTracking($id, $trackingCode, $trackingUrl);

        if (!$result) {
            return redirect()->back()->with('error', 'Erro ao adicionar rastreio.');
        }

        return redirect()->back()->with('success', 'Codigo de rastreio adicionado!');
    }

    public function print($id)
    {
        $order = $this->orderModel->getWithItems($id);

        if (!$order) {
            return redirect()->back()->with('error', 'Pedido nao encontrado.');
        }

        return view('admin/orders/print', [
            'order' => $order,
        ]);
    }

    public function export()
    {
        $filters = [
            'status' => $this->request->getGet('status'),
            'from_date' => $this->request->getGet('from_date'),
            'to_date' => $this->request->getGet('to_date'),
        ];

        $builder = $this->orderModel->select('orders.*, customers.name as customer_name, customers.email as customer_email')
            ->join('customers', 'customers.id = orders.customer_id');

        if (!empty($filters['status'])) {
            $builder->where('orders.status', $filters['status']);
        }

        if (!empty($filters['from_date'])) {
            $builder->where('orders.created_at >=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $builder->where('orders.created_at <=', $filters['to_date'] . ' 23:59:59');
        }

        $orders = $builder->orderBy('orders.created_at', 'DESC')->findAll();

        // Generate CSV
        $filename = 'pedidos_' . date('Y-m-d_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

        // Header
        fputcsv($output, [
            'Numero',
            'Data',
            'Cliente',
            'Email',
            'Status',
            'Pagamento',
            'Subtotal',
            'Desconto',
            'Frete',
            'Total',
        ], ';');

        // Data
        foreach ($orders as $order) {
            fputcsv($output, [
                $order['order_number'],
                date('d/m/Y H:i', strtotime($order['created_at'])),
                $order['customer_name'],
                $order['customer_email'],
                $this->orderModel->getStatusLabel($order['status']),
                $this->orderModel->getPaymentStatusLabel($order['payment_status']),
                number_format($order['subtotal'], 2, ',', '.'),
                number_format($order['discount'], 2, ',', '.'),
                number_format($order['shipping_cost'], 2, ',', '.'),
                number_format($order['total'], 2, ',', '.'),
            ], ';');
        }

        fclose($output);
        exit;
    }

    /**
     * Cotar frete para o pedido
     */
    public function quotarFrete($id)
    {
        $order = $this->orderModel->getWithItems($id);

        if (!$order) {
            return $this->response->setJSON(['success' => false, 'message' => 'Pedido nao encontrado.']);
        }

        $melhorEnvio = new \App\Services\MelhorEnvioService();

        // Usar dimensoes do request se fornecidas, senao usar padrao
        $package = [
            'weight' => (float) ($this->request->getGet('weight') ?: 0.5),
            'height' => (int) ($this->request->getGet('height') ?: 10),
            'width' => (int) ($this->request->getGet('width') ?: 15),
            'length' => (int) ($this->request->getGet('length') ?: 20),
        ];

        $quotes = $melhorEnvio->quoteForOrder($order, $package);
        $balance = $melhorEnvio->getBalance();

        return $this->response->setJSON([
            'success' => true,
            'quotes' => $quotes,
            'balance' => $balance,
        ]);
    }

    /**
     * Gerar etiqueta de envio
     */
    public function gerarEtiqueta($id)
    {
        $order = $this->orderModel->getWithItems($id);

        if (!$order) {
            return redirect()->back()->with('error', 'Pedido nao encontrado.');
        }

        $serviceId = (int) $this->request->getPost('service_id');
        $package = [
            'weight' => (float) $this->request->getPost('weight'),
            'height' => (int) $this->request->getPost('height'),
            'width' => (int) $this->request->getPost('width'),
            'length' => (int) $this->request->getPost('length'),
        ];

        if (!$serviceId) {
            return redirect()->back()->with('error', 'Selecione uma transportadora.');
        }

        $melhorEnvio = new \App\Services\MelhorEnvioService();

        // Buscar cotacao para saber o valor do frete
        $quotes = $melhorEnvio->quoteForOrder($order, $package);
        $shippingCost = 0;
        foreach ($quotes as $quote) {
            if ($quote['code'] == $serviceId) {
                $shippingCost = $quote['price'];
                break;
            }
        }

        // Se nao encontrou o servico especifico, pegar o primeiro como estimativa
        if ($shippingCost == 0 && !empty($quotes)) {
            $shippingCost = $quotes[0]['price'];
        }

        // Verificar saldo antes de continuar
        $balance = $melhorEnvio->getBalance();
        if ($balance !== null && $shippingCost > 0 && $balance < $shippingCost) {
            $falta = $shippingCost - $balance;
            return redirect()->back()->with('error',
                'Saldo insuficiente no Melhor Envio. ' .
                'Saldo: R$ ' . number_format($balance, 2, ',', '.') . ' | ' .
                'Frete: R$ ' . number_format($shippingCost, 2, ',', '.') . ' | ' .
                'Falta: R$ ' . number_format($falta, 2, ',', '.') . '. ' .
                'Adicione creditos antes de gerar a etiqueta.'
            );
        }

        // 1. Adicionar ao carrinho
        $cartResult = $melhorEnvio->addToCart($order, $package, $serviceId);

        if (!$cartResult['success']) {
            return redirect()->back()->with('error', 'Erro ao adicionar ao carrinho: ' . ($cartResult['message'] ?? 'Erro desconhecido'));
        }

        $cartId = $cartResult['cart_id'];

        // 2. Fazer checkout (pagar)
        $checkoutResult = $melhorEnvio->checkout([$cartId]);

        if (!$checkoutResult['success']) {
            // Adicionar info do saldo na mensagem de erro
            $errorMsg = $checkoutResult['message'] ?? 'Erro desconhecido';
            if ($balance !== null) {
                $errorMsg .= ' (Saldo: R$ ' . number_format($balance, 2, ',', '.') . ')';
            }
            return redirect()->back()->with('error', 'Erro no checkout: ' . $errorMsg);
        }

        // 3. Gerar etiqueta
        $labelResult = $melhorEnvio->generateLabel([$cartId]);

        if (!$labelResult['success']) {
            return redirect()->back()->with('error', 'Erro ao gerar etiqueta: ' . ($labelResult['message'] ?? 'Erro desconhecido'));
        }

        // 4. Obter código de rastreio
        $trackingCode = null;

        // Tentar obter do resultado da geração de etiqueta
        if (!empty($labelResult['data'])) {
            // Pode estar em diferentes formatos
            $labelData = is_array($labelResult['data']) ? $labelResult['data'] : [];
            if (isset($labelData[$cartId]['tracking'])) {
                $trackingCode = $labelData[$cartId]['tracking'];
            } elseif (isset($labelData['tracking'])) {
                $trackingCode = $labelData['tracking'];
            }
        }

        // Se não encontrou, tentar via endpoint de tracking
        if (!$trackingCode) {
            // Aguardar 2 segundos para o Melhor Envio processar
            sleep(2);

            $trackingResult = $melhorEnvio->tracking([$cartId]);
            log_message('debug', 'Tracking Result: ' . json_encode($trackingResult));

            if ($trackingResult['success'] && !empty($trackingResult['data'])) {
                // O retorno pode ser array indexado pelo cartId ou array simples
                $trackingData = $trackingResult['data'][$cartId] ?? ($trackingResult['data'][0] ?? reset($trackingResult['data']));
                $trackingCode = $trackingData['tracking'] ?? $trackingData['code'] ?? null;
            }
        }

        // Salvar ID da etiqueta e código de rastreio no pedido
        $updateData = ['me_label_id' => $cartId];

        if ($trackingCode) {
            $trackingUrl = 'https://www.melhorrastreio.com.br/rastreio/' . $trackingCode;
            $updateData['tracking_code'] = $trackingCode;
            $updateData['tracking_url'] = $trackingUrl;
        }

        $this->orderModel->update($id, $updateData);

        // Recarregar o pedido com os dados atualizados
        $order = $this->orderModel->getWithItems($id);

        // Atualizar status para "Enviado" e enviar email ao cliente
        $previousStatus = $order['status'] ?? 'pending';
        if (in_array($previousStatus, ['pending', 'paid', 'processing'])) {
            $this->orderService->updateStatus($id, 'shipped', 'Etiqueta gerada - Melhor Envio');
        }

        // Enviar email com código de rastreio (sempre que tiver o código)
        if ($trackingCode) {
            $emailService = new \App\Services\EmailService();
            $emailService->sendOrderStatusEmail($order, 'shipped');
        }

        $successMsg = 'Etiqueta gerada com sucesso!';
        if ($trackingCode) {
            $successMsg .= ' Código de rastreio: ' . $trackingCode;
        } else {
            $successMsg .= ' (Código de rastreio será gerado em breve)';
        }

        return redirect()->back()->with('success', $successMsg);
    }

    /**
     * Imprimir etiqueta
     */
    public function imprimirEtiqueta($id)
    {
        $order = $this->orderModel->find($id);

        if (!$order || empty($order['me_label_id'])) {
            return redirect()->back()->with('error', 'Etiqueta nao encontrada.');
        }

        $melhorEnvio = new \App\Services\MelhorEnvioService();
        $result = $melhorEnvio->printLabel([$order['me_label_id']]);

        if ($result['success'] && !empty($result['url'])) {
            return redirect()->to($result['url']);
        }

        return redirect()->back()->with('error', 'Erro ao imprimir etiqueta.');
    }

    /**
     * Rastrear etiqueta
     */
    public function rastrearEtiqueta($id)
    {
        $order = $this->orderModel->find($id);

        if (!$order || empty($order['me_label_id'])) {
            return redirect()->back()->with('error', 'Etiqueta nao encontrada.');
        }

        $melhorEnvio = new \App\Services\MelhorEnvioService();
        $result = $melhorEnvio->tracking([$order['me_label_id']]);

        if ($result['success']) {
            // Retornar JSON com os dados de rastreamento
            return $this->response->setJSON($result['data']);
        }

        return redirect()->back()->with('error', 'Erro ao rastrear.');
    }

    /**
     * Obter codigo de rastreio da etiqueta gerada
     */
    public function obterRastreio($id)
    {
        $order = $this->orderModel->getWithItems($id);

        if (!$order || empty($order['me_label_id'])) {
            return redirect()->back()->with('error', 'Etiqueta nao encontrada.');
        }

        // Se ja tem codigo de rastreio, nao precisa buscar
        if (!empty($order['tracking_code'])) {
            return redirect()->back()->with('info', 'Codigo de rastreio ja cadastrado: ' . $order['tracking_code']);
        }

        $melhorEnvio = new \App\Services\MelhorEnvioService();
        $labelId = $order['me_label_id'];

        // Buscar informacoes da etiqueta
        $result = $melhorEnvio->tracking([$labelId]);
        log_message('debug', 'obterRastreio - tracking result: ' . json_encode($result));

        $trackingCode = null;

        if ($result['success'] && !empty($result['data'])) {
            // O retorno pode ser array indexado pelo labelId ou array simples
            $trackingData = $result['data'][$labelId] ?? ($result['data'][0] ?? reset($result['data']));
            $trackingCode = $trackingData['tracking'] ?? $trackingData['code'] ?? null;
        }

        if (!$trackingCode) {
            return redirect()->back()->with('error', 'Codigo de rastreio ainda nao disponivel. Tente novamente em alguns minutos.');
        }

        // Salvar codigo de rastreio
        $trackingUrl = 'https://www.melhorrastreio.com.br/rastreio/' . $trackingCode;
        $this->orderModel->update($id, [
            'tracking_code' => $trackingCode,
            'tracking_url' => $trackingUrl,
        ]);

        // Recarregar pedido
        $order = $this->orderModel->getWithItems($id);

        // Enviar email para o cliente
        $emailService = new \App\Services\EmailService();
        $emailService->sendOrderStatusEmail($order, 'shipped');

        return redirect()->back()->with('success', 'Codigo de rastreio obtido: ' . $trackingCode . '. Email enviado ao cliente!');
    }

    /**
     * Adicionar credito Melhor Envio
     */
    public function adicionarCreditoME()
    {
        $json = $this->request->getJSON();
        $valor = (float) ($json->valor ?? 0);
        $metodo = $json->metodo ?? 'pix';

        if ($valor < 10 || $valor > 50000) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Valor deve ser entre R$ 10,00 e R$ 50.000,00'
            ]);
        }

        $melhorEnvio = new \App\Services\MelhorEnvioService();

        // Verificar se esta conectado
        if (!$melhorEnvio->isConnected()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Melhor Envio nao conectado. Acesse /melhor-envio/autorizar para conectar.'
            ]);
        }

        $result = $melhorEnvio->addCredits($valor, $metodo);

        // Log do resultado para debug
        log_message('debug', 'adicionarCreditoME result: ' . json_encode($result));

        return $this->response->setJSON($result);
    }
}
