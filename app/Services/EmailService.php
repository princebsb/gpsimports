<?php

namespace App\Services;

use CodeIgniter\Email\Email;

class EmailService
{
    protected Email $email;
    protected $db;

    public function __construct()
    {
        $this->email = \Config\Services::email();
        $this->db = \Config\Database::connect();

        $this->configureEmail();
    }

    /**
     * Configure email settings - uses Email.php config
     */
    protected function configureEmail(): void
    {
        // Email.php already has the SMTP settings configured
        // Just ensure mailType is html
        $this->email->setMailType('html');
    }

    /**
     * Get email template by code
     */
    public function getTemplate(string $code): ?array
    {
        return $this->db->table('email_templates')
            ->where('code', $code)
            ->where('status', 'active')
            ->get()
            ->getRowArray();
    }

    /**
     * Replace variables in template
     */
    protected function replaceVariables(string $content, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
            $content = str_replace('{{ ' . $key . ' }}', $value, $content);
        }
        return $content;
    }

    /**
     * Get store name from settings
     */
    protected function getStoreName(): string
    {
        return setting('store_name', 'GPS Imports');
    }

    /**
     * Get store email from settings
     */
    protected function getStoreEmail(): string
    {
        return 'vendas@gpsimports.com.br';
    }

    /**
     * Send email using template
     */
    public function sendWithTemplate(string $templateCode, string $toEmail, string $toName, array $variables = []): bool
    {
        $template = $this->getTemplate($templateCode);

        if (!$template) {
            log_message('error', 'EmailService: Template not found - ' . $templateCode);
            return false;
        }

        // Add default variables
        $variables['store_name'] = $this->getStoreName();
        $variables['store_email'] = $this->getStoreEmail();
        $variables['store_url'] = base_url();
        $variables['current_year'] = date('Y');

        $subject = $this->replaceVariables($template['subject'], $variables);
        $body = $this->replaceVariables($template['body'], $variables);

        // Wrap body in HTML template
        $htmlBody = $this->wrapInHtmlTemplate($body, $subject);

        return $this->send($toEmail, $toName, $subject, $htmlBody);
    }

    /**
     * Wrap content in HTML email template
     */
    protected function wrapInHtmlTemplate(string $content, string $title): string
    {
        $storeName = $this->getStoreName();
        $storeUrl = base_url();
        $year = date('Y');

        return <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #2563eb; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { background-color: white; padding: 30px; border-radius: 0 0 8px 8px; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        .button { display: inline-block; background-color: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 10px 0; }
        .status-badge { display: inline-block; padding: 8px 16px; border-radius: 20px; font-weight: bold; }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-paid { background-color: #d1fae5; color: #065f46; }
        .status-processing { background-color: #dbeafe; color: #1e40af; }
        .status-shipped { background-color: #e0e7ff; color: #3730a3; }
        .status-delivered { background-color: #d1fae5; color: #065f46; }
        .status-cancelled { background-color: #fee2e2; color: #991b1b; }
        .order-details { background-color: #f9fafb; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .tracking-box { background-color: #eff6ff; border: 1px solid #2563eb; padding: 15px; border-radius: 5px; margin: 15px 0; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{$storeName}</h1>
        </div>
        <div class="content">
            {$content}
        </div>
        <div class="footer">
            <p>&copy; {$year} {$storeName}. Todos os direitos reservados.</p>
            <p><a href="{$storeUrl}">{$storeUrl}</a></p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Send email directly
     */
    public function send(string $toEmail, string $toName, string $subject, string $body): bool
    {
        try {
            $this->email->clear();
            $this->email->setFrom($this->getStoreEmail(), $this->getStoreName());
            $this->email->setTo($toEmail);
            $this->email->setSubject($subject);
            $this->email->setMessage($body);

            $result = $this->email->send(false);

            if (!$result) {
                log_message('error', 'EmailService: Failed to send email to ' . $toEmail . ' - ' . $this->email->printDebugger(['headers', 'subject', 'body']));

                // Add to queue for retry
                $this->addToQueue($toEmail, $toName, $subject, $body);
                return false;
            }

            log_message('info', 'EmailService: Email sent to ' . $toEmail . ' - Subject: ' . $subject);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'EmailService: Exception sending email - ' . $e->getMessage());

            // Add to queue for retry
            $this->addToQueue($toEmail, $toName, $subject, $body);
            return false;
        }
    }

    /**
     * Add email to queue for later sending
     */
    public function addToQueue(string $toEmail, string $toName, string $subject, string $body, int $priority = 5, ?string $templateCode = null): int
    {
        $this->db->table('email_queue')->insert([
            'to_email' => $toEmail,
            'to_name' => $toName,
            'subject' => $subject,
            'body' => $body,
            'template_code' => $templateCode,
            'priority' => $priority,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->db->insertID();
    }

    /**
     * Process email queue
     */
    public function processQueue(int $limit = 10): array
    {
        $emails = $this->db->table('email_queue')
            ->where('status', 'pending')
            ->where('attempts <', 3)
            ->where('(scheduled_at IS NULL OR scheduled_at <= NOW())')
            ->orderBy('priority', 'ASC')
            ->orderBy('created_at', 'ASC')
            ->limit($limit)
            ->get()
            ->getResultArray();

        $results = ['sent' => 0, 'failed' => 0];

        foreach ($emails as $email) {
            $this->db->table('email_queue')
                ->where('id', $email['id'])
                ->update(['attempts' => $email['attempts'] + 1]);

            $sent = $this->sendDirect($email['to_email'], $email['subject'], $email['body']);

            if ($sent) {
                $this->db->table('email_queue')
                    ->where('id', $email['id'])
                    ->update([
                        'status' => 'sent',
                        'sent_at' => date('Y-m-d H:i:s'),
                    ]);
                $results['sent']++;
            } else {
                $status = $email['attempts'] >= 2 ? 'failed' : 'pending';
                $this->db->table('email_queue')
                    ->where('id', $email['id'])
                    ->update([
                        'status' => $status,
                        'error_message' => 'Failed to send after ' . ($email['attempts'] + 1) . ' attempts',
                    ]);
                $results['failed']++;
            }
        }

        return $results;
    }

    /**
     * Send email directly without queue fallback
     */
    protected function sendDirect(string $toEmail, string $subject, string $body): bool
    {
        try {
            $this->email->clear();
            $this->email->setFrom($this->getStoreEmail(), $this->getStoreName());
            $this->email->setTo($toEmail);
            $this->email->setSubject($subject);
            $this->email->setMessage($body);

            return $this->email->send(false);
        } catch (\Exception $e) {
            log_message('error', 'EmailService: Exception in sendDirect - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send order status update email
     */
    public function sendOrderStatusEmail(array $order, string $status): bool
    {
        $customer = $order['customer'] ?? [];

        if (empty($customer['email'])) {
            log_message('error', 'EmailService: No customer email for order #' . $order['order_number']);
            return false;
        }

        $statusLabels = [
            'pending' => 'Pendente',
            'paid' => 'Pagamento Confirmado',
            'processing' => 'Em Preparacao',
            'shipped' => 'Enviado',
            'delivered' => 'Entregue',
            'cancelled' => 'Cancelado',
            'refunded' => 'Reembolsado',
        ];

        $statusLabel = $statusLabels[$status] ?? ucfirst($status);

        $variables = [
            'customer_name' => $customer['name'] ?? 'Cliente',
            'order_number' => $order['order_number'],
            'status' => $status,
            'status_label' => $statusLabel,
            'order_total' => 'R$ ' . number_format($order['total'], 2, ',', '.'),
            'order_date' => date('d/m/Y', strtotime($order['created_at'])),
            'tracking_code' => $order['tracking_code'] ?? '',
            'tracking_url' => $order['tracking_url'] ?? '',
            'order_url' => base_url('minha-conta/pedidos/' . $order['order_number']),
        ];

        // Try to use template first
        $templateCode = 'order_status_' . $status;
        $template = $this->getTemplate($templateCode);

        if ($template) {
            return $this->sendWithTemplate(
                $templateCode,
                $customer['email'],
                $customer['name'] ?? 'Cliente',
                $variables
            );
        }

        // Fallback: use generic status email
        $template = $this->getTemplate('order_status_update');
        if ($template) {
            return $this->sendWithTemplate(
                'order_status_update',
                $customer['email'],
                $customer['name'] ?? 'Cliente',
                $variables
            );
        }

        // Final fallback: generate email content directly
        return $this->sendStatusEmailDirect($customer, $order, $status, $statusLabel, $variables);
    }

    /**
     * Send status email directly without template
     */
    protected function sendStatusEmailDirect(array $customer, array $order, string $status, string $statusLabel, array $variables): bool
    {
        $customerName = $customer['name'] ?? 'Cliente';
        $orderNumber = $order['order_number'];
        $orderUrl = $variables['order_url'];

        $content = "<h2>Ola, {$customerName}!</h2>";
        $content .= "<p>O status do seu pedido <strong>#{$orderNumber}</strong> foi atualizado.</p>";
        $content .= "<div class='order-details'>";
        $content .= "<p><strong>Novo Status:</strong> <span class='status-badge status-{$status}'>{$statusLabel}</span></p>";
        $content .= "<p><strong>Pedido:</strong> #{$orderNumber}</p>";
        $content .= "<p><strong>Data:</strong> {$variables['order_date']}</p>";
        $content .= "<p><strong>Total:</strong> {$variables['order_total']}</p>";
        $content .= "</div>";

        // Add specific content based on status
        switch ($status) {
            case 'paid':
                $content .= "<p>Seu pagamento foi confirmado! Estamos preparando seu pedido.</p>";
                break;
            case 'processing':
                $content .= "<p>Seu pedido esta sendo preparado para envio.</p>";
                break;
            case 'shipped':
                $content .= "<p>Seu pedido foi enviado!</p>";
                if (!empty($order['tracking_code'])) {
                    $trackingUrl = $order['tracking_url'] ?? 'https://www.linkcorreios.com.br/?id=' . $order['tracking_code'];
                    $content .= "<div class='tracking-box'>";
                    $content .= "<p><strong>Codigo de Rastreio:</strong></p>";
                    $content .= "<p style='font-size: 18px; font-weight: bold;'>{$order['tracking_code']}</p>";
                    $content .= "<p><a href='{$trackingUrl}' class='button'>Rastrear Pedido</a></p>";
                    $content .= "</div>";
                }
                break;
            case 'delivered':
                $content .= "<p>Seu pedido foi entregue! Esperamos que voce aproveite sua compra.</p>";
                $content .= "<p>Se tiver alguma duvida ou problema, entre em contato conosco.</p>";
                break;
            case 'cancelled':
                $content .= "<p>Infelizmente seu pedido foi cancelado.</p>";
                $content .= "<p>Se voce nao solicitou o cancelamento, por favor entre em contato conosco.</p>";
                break;
            case 'refunded':
                $content .= "<p>O reembolso do seu pedido foi processado.</p>";
                $content .= "<p>O valor sera creditado conforme o metodo de pagamento utilizado.</p>";
                break;
        }

        $content .= "<p style='margin-top: 20px;'><a href='{$orderUrl}' class='button'>Ver Detalhes do Pedido</a></p>";
        $content .= "<p style='margin-top: 20px;'>Obrigado por comprar conosco!</p>";

        $subject = "Pedido #{$orderNumber} - {$statusLabel}";
        $htmlBody = $this->wrapInHtmlTemplate($content, $subject);

        return $this->send($customer['email'], $customerName, $subject, $htmlBody);
    }

    /**
     * Send order confirmation email
     */
    public function sendOrderConfirmationEmail(array $order): bool
    {
        $customer = $order['customer'] ?? [];

        if (empty($customer['email'])) {
            return false;
        }

        $variables = [
            'customer_name' => $customer['name'] ?? 'Cliente',
            'order_number' => $order['order_number'],
            'order_total' => 'R$ ' . number_format($order['total'], 2, ',', '.'),
            'order_date' => date('d/m/Y H:i', strtotime($order['created_at'])),
            'order_url' => base_url('minha-conta/pedidos/' . $order['order_number']),
            'payment_method' => $this->getPaymentMethodLabel($order['payment_method'] ?? ''),
        ];

        // Try template first
        $template = $this->getTemplate('order_confirmation');
        if ($template) {
            return $this->sendWithTemplate(
                'order_confirmation',
                $customer['email'],
                $customer['name'] ?? 'Cliente',
                $variables
            );
        }

        // Fallback
        $customerName = $customer['name'] ?? 'Cliente';
        $content = "<h2>Obrigado pela sua compra, {$customerName}!</h2>";
        $content .= "<p>Recebemos seu pedido e ele esta sendo processado.</p>";
        $content .= "<div class='order-details'>";
        $content .= "<p><strong>Numero do Pedido:</strong> #{$order['order_number']}</p>";
        $content .= "<p><strong>Data:</strong> {$variables['order_date']}</p>";
        $content .= "<p><strong>Total:</strong> {$variables['order_total']}</p>";
        $content .= "<p><strong>Pagamento:</strong> {$variables['payment_method']}</p>";
        $content .= "</div>";
        $content .= "<p><a href='{$variables['order_url']}' class='button'>Ver Pedido</a></p>";

        $subject = "Pedido #{$order['order_number']} Recebido!";
        $htmlBody = $this->wrapInHtmlTemplate($content, $subject);

        return $this->send($customer['email'], $customerName, $subject, $htmlBody);
    }

    /**
     * Get payment method label
     */
    protected function getPaymentMethodLabel(string $method): string
    {
        return match($method) {
            'pix' => 'PIX',
            'credit_card' => 'Cartao de Credito',
            'debit_card' => 'Cartao de Debito',
            'boleto' => 'Boleto Bancario',
            'checkout_pro' => 'Mercado Pago',
            'account_money' => 'Saldo Mercado Pago',
            default => ucfirst($method)
        };
    }
}
