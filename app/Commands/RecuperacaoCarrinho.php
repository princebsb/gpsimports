<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class RecuperacaoCarrinho extends BaseCommand
{
    protected $group       = 'Marketing';
    protected $name        = 'marketing:recuperacao';
    protected $description = 'Envia emails de recuperacao de carrinho e pedidos pendentes';
    protected $usage       = 'marketing:recuperacao [opcao]';
    protected $arguments   = [
        'opcao' => 'carrinho, pendentes ou cancelar',
    ];

    public function run(array $params)
    {
        $opcao = $params[0] ?? 'todos';

        CLI::write('Iniciando rotina de recuperacao...', 'yellow');

        if ($opcao === 'todos' || $opcao === 'carrinho') {
            $this->enviarEmailCarrinhoAbandonado();
        }

        if ($opcao === 'todos' || $opcao === 'pendentes') {
            $this->enviarEmailPedidosPendentes();
        }

        if ($opcao === 'todos' || $opcao === 'cancelar') {
            $this->cancelarPedidosAntigos();
        }

        CLI::write('Rotina finalizada!', 'green');
    }

    /**
     * Enviar email para carrinhos abandonados (mais de 1 hora sem finalizar)
     */
    protected function enviarEmailCarrinhoAbandonado()
    {
        CLI::write('Verificando carrinhos abandonados...', 'blue');

        $db = \Config\Database::connect();

        // Buscar carrinhos abandonados (sessoes com itens, criados ha mais de 1 hora, sem pedido)
        // Precisamos de uma tabela para rastrear isso - vamos usar a tabela de sessoes ou criar uma nova

        // Por enquanto, vamos buscar clientes que tem itens no carrinho mas nao finalizaram
        // Isso requer uma tabela cart_items vinculada ao customer

        $carrinhos = $db->table('cart_abandonment')
            ->select('cart_abandonment.*, customers.name, customers.email')
            ->join('customers', 'customers.id = cart_abandonment.customer_id')
            ->where('cart_abandonment.email_sent', 0)
            ->where('cart_abandonment.created_at <', date('Y-m-d H:i:s', strtotime('-1 hour')))
            ->where('cart_abandonment.recovered', 0)
            ->get()
            ->getResultArray();

        $enviados = 0;
        foreach ($carrinhos as $carrinho) {
            if ($this->enviarEmail($carrinho['email'], 'carrinho_abandonado', [
                'nome' => $carrinho['name'],
                'itens' => json_decode($carrinho['items'], true),
                'total' => $carrinho['total'],
                'link' => base_url('carrinho'),
            ])) {
                $db->table('cart_abandonment')
                    ->where('id', $carrinho['id'])
                    ->update(['email_sent' => 1, 'email_sent_at' => date('Y-m-d H:i:s')]);
                $enviados++;
            }
        }

        CLI::write("Emails de carrinho abandonado enviados: {$enviados}", 'green');
    }

    /**
     * Enviar email para pedidos com pagamento pendente
     */
    protected function enviarEmailPedidosPendentes()
    {
        CLI::write('Verificando pedidos pendentes...', 'blue');

        $orderModel = model('OrderModel');
        $db = \Config\Database::connect();

        // Pedidos pendentes ha mais de 2 horas, que ainda nao receberam email de lembrete
        $pedidos = $orderModel
            ->select('orders.*, customers.name as customer_name, customers.email as customer_email')
            ->join('customers', 'customers.id = orders.customer_id')
            ->where('orders.payment_status', 'pending')
            ->where('orders.status', 'pending')
            ->where('orders.created_at <', date('Y-m-d H:i:s', strtotime('-2 hours')))
            ->where('orders.created_at >', date('Y-m-d H:i:s', strtotime('-2 days')))
            ->where('orders.deleted_at', null)
            ->findAll();

        $enviados = 0;
        foreach ($pedidos as $pedido) {
            // Verificar se ja enviou email de lembrete
            $jaEnviou = $db->table('order_emails')
                ->where('order_id', $pedido['id'])
                ->where('type', 'payment_reminder')
                ->countAllResults();

            if ($jaEnviou > 0) {
                continue;
            }

            // Buscar itens do pedido
            $itens = $orderModel->getItems($pedido['id']);

            if ($this->enviarEmail($pedido['customer_email'], 'pagamento_pendente', [
                'nome' => $pedido['customer_name'],
                'pedido' => $pedido,
                'itens' => $itens,
                'link' => base_url('checkout/sucesso/' . $pedido['order_number']),
            ])) {
                // Registrar envio
                $db->table('order_emails')->insert([
                    'order_id' => $pedido['id'],
                    'type' => 'payment_reminder',
                    'email' => $pedido['customer_email'],
                    'sent_at' => date('Y-m-d H:i:s'),
                ]);
                $enviados++;
            }
        }

        CLI::write("Emails de pagamento pendente enviados: {$enviados}", 'green');
    }

    /**
     * Cancelar pedidos nao pagos apos 2 dias
     */
    protected function cancelarPedidosAntigos()
    {
        CLI::write('Verificando pedidos para cancelar...', 'blue');

        $orderModel = model('OrderModel');
        $db = \Config\Database::connect();

        // Pedidos pendentes ha mais de 2 dias
        $pedidos = $orderModel
            ->select('orders.*, customers.name as customer_name, customers.email as customer_email')
            ->join('customers', 'customers.id = orders.customer_id')
            ->where('orders.payment_status', 'pending')
            ->whereIn('orders.status', ['pending'])
            ->where('orders.created_at <', date('Y-m-d H:i:s', strtotime('-2 days')))
            ->where('orders.deleted_at', null)
            ->findAll();

        $cancelados = 0;
        foreach ($pedidos as $pedido) {
            // Cancelar pedido
            $orderModel->update($pedido['id'], [
                'status' => 'cancelled',
                'payment_status' => 'rejected',
            ]);

            // Adicionar ao historico
            $orderModel->addStatusHistory(
                $pedido['id'],
                'cancelled',
                'Pedido cancelado automaticamente por falta de pagamento apos 2 dias.',
                true,
                null
            );

            // Enviar email de cancelamento
            $itens = $orderModel->getItems($pedido['id']);

            $this->enviarEmail($pedido['customer_email'], 'pedido_cancelado', [
                'nome' => $pedido['customer_name'],
                'pedido' => $pedido,
                'itens' => $itens,
            ]);

            // Registrar envio
            $db->table('order_emails')->insert([
                'order_id' => $pedido['id'],
                'type' => 'order_cancelled',
                'email' => $pedido['customer_email'],
                'sent_at' => date('Y-m-d H:i:s'),
            ]);

            $cancelados++;

            // Devolver estoque (se necessario)
            $this->devolverEstoque($pedido['id'], $itens);
        }

        CLI::write("Pedidos cancelados: {$cancelados}", 'green');
    }

    /**
     * Devolver estoque dos itens do pedido
     */
    protected function devolverEstoque(int $orderId, array $itens)
    {
        $productModel = model('ProductModel');

        foreach ($itens as $item) {
            if (!empty($item['product_id'])) {
                $product = $productModel->find($item['product_id']);
                if ($product) {
                    $novoEstoque = ($product['stock'] ?? 0) + $item['quantity'];
                    $productModel->update($item['product_id'], ['stock' => $novoEstoque]);
                }
            }
        }
    }

    /**
     * Enviar email usando template
     */
    protected function enviarEmail(string $to, string $template, array $data): bool
    {
        try {
            $email = \Config\Services::email();

            $storeName = setting('store_name') ?? 'GPS Imports';
            $storeEmail = setting('store_email') ?? 'contato@gpsimports.com.br';

            $email->setFrom($storeEmail, $storeName);
            $email->setTo($to);

            // Definir assunto baseado no template
            $subjects = [
                'carrinho_abandonado' => 'Voce esqueceu algo no carrinho! - ' . $storeName,
                'pagamento_pendente' => 'Seu pedido aguarda pagamento - ' . $storeName,
                'pedido_cancelado' => 'Pedido cancelado por falta de pagamento - ' . $storeName,
            ];

            $email->setSubject($subjects[$template] ?? 'Mensagem de ' . $storeName);
            $email->setMailType('html');

            // Renderizar template
            $html = view('emails/' . $template, $data);
            $email->setMessage($html);

            return $email->send();
        } catch (\Exception $e) {
            log_message('error', 'Erro ao enviar email: ' . $e->getMessage());
            return false;
        }
    }
}
