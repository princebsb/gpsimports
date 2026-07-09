<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedOrderEmailTemplates extends Migration
{
    public function up()
    {
        $templates = [
            [
                'code' => 'order_confirmation',
                'name' => 'Confirmacao de Pedido',
                'subject' => 'Pedido #{{order_number}} Recebido!',
                'body' => '<h2>Obrigado pela sua compra, {{customer_name}}!</h2>
<p>Recebemos seu pedido e ele esta sendo processado.</p>
<div class="order-details">
    <p><strong>Numero do Pedido:</strong> #{{order_number}}</p>
    <p><strong>Data:</strong> {{order_date}}</p>
    <p><strong>Total:</strong> {{order_total}}</p>
    <p><strong>Pagamento:</strong> {{payment_method}}</p>
</div>
<p>Voce pode acompanhar o status do seu pedido a qualquer momento.</p>
<p><a href="{{order_url}}" class="button">Ver Pedido</a></p>
<p>Obrigado por comprar conosco!</p>',
                'variables' => json_encode(['customer_name', 'order_number', 'order_date', 'order_total', 'payment_method', 'order_url']),
                'is_system' => 1,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code' => 'order_status_update',
                'name' => 'Atualizacao de Status (Generico)',
                'subject' => 'Pedido #{{order_number}} - {{status_label}}',
                'body' => '<h2>Ola, {{customer_name}}!</h2>
<p>O status do seu pedido <strong>#{{order_number}}</strong> foi atualizado.</p>
<div class="order-details">
    <p><strong>Novo Status:</strong> <span class="status-badge status-{{status}}">{{status_label}}</span></p>
    <p><strong>Pedido:</strong> #{{order_number}}</p>
    <p><strong>Data:</strong> {{order_date}}</p>
    <p><strong>Total:</strong> {{order_total}}</p>
</div>
<p><a href="{{order_url}}" class="button">Ver Detalhes do Pedido</a></p>
<p>Obrigado por comprar conosco!</p>',
                'variables' => json_encode(['customer_name', 'order_number', 'order_date', 'order_total', 'status', 'status_label', 'order_url']),
                'is_system' => 1,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code' => 'order_status_paid',
                'name' => 'Status: Pagamento Confirmado',
                'subject' => 'Pedido #{{order_number}} - Pagamento Confirmado!',
                'body' => '<h2>Ola, {{customer_name}}!</h2>
<p>Otima noticia! O pagamento do seu pedido <strong>#{{order_number}}</strong> foi confirmado.</p>
<div class="order-details">
    <p><strong>Status:</strong> <span class="status-badge status-paid">Pagamento Confirmado</span></p>
    <p><strong>Pedido:</strong> #{{order_number}}</p>
    <p><strong>Total:</strong> {{order_total}}</p>
</div>
<p>Estamos preparando seu pedido para envio. Voce recebera uma notificacao assim que ele for despachado.</p>
<p><a href="{{order_url}}" class="button">Acompanhar Pedido</a></p>
<p>Obrigado pela confianca!</p>',
                'variables' => json_encode(['customer_name', 'order_number', 'order_total', 'order_url']),
                'is_system' => 1,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code' => 'order_status_processing',
                'name' => 'Status: Em Preparacao',
                'subject' => 'Pedido #{{order_number}} - Em Preparacao',
                'body' => '<h2>Ola, {{customer_name}}!</h2>
<p>Seu pedido <strong>#{{order_number}}</strong> esta sendo preparado!</p>
<div class="order-details">
    <p><strong>Status:</strong> <span class="status-badge status-processing">Em Preparacao</span></p>
    <p><strong>Pedido:</strong> #{{order_number}}</p>
    <p><strong>Total:</strong> {{order_total}}</p>
</div>
<p>Nossa equipe esta cuidadosamente preparando seus produtos para envio. Em breve voce recebera o codigo de rastreamento.</p>
<p><a href="{{order_url}}" class="button">Acompanhar Pedido</a></p>
<p>Obrigado pela paciencia!</p>',
                'variables' => json_encode(['customer_name', 'order_number', 'order_total', 'order_url']),
                'is_system' => 1,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code' => 'order_status_shipped',
                'name' => 'Status: Pedido Enviado',
                'subject' => 'Pedido #{{order_number}} - Seu pedido foi enviado!',
                'body' => '<h2>Ola, {{customer_name}}!</h2>
<p>Seu pedido <strong>#{{order_number}}</strong> foi enviado!</p>
<div class="order-details">
    <p><strong>Status:</strong> <span class="status-badge status-shipped">Enviado</span></p>
    <p><strong>Pedido:</strong> #{{order_number}}</p>
    <p><strong>Total:</strong> {{order_total}}</p>
</div>
{{#tracking_code}}
<div class="tracking-box">
    <p><strong>Codigo de Rastreio:</strong></p>
    <p style="font-size: 18px; font-weight: bold;">{{tracking_code}}</p>
    <p><a href="https://www.linkcorreios.com.br/?id={{tracking_code}}" class="button">Rastrear Pedido</a></p>
</div>
{{/tracking_code}}
<p>Acompanhe a entrega do seu pedido atraves do codigo de rastreio acima.</p>
<p><a href="{{order_url}}" class="button">Ver Detalhes</a></p>
<p>Obrigado por comprar conosco!</p>',
                'variables' => json_encode(['customer_name', 'order_number', 'order_total', 'tracking_code', 'order_url']),
                'is_system' => 1,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code' => 'order_status_delivered',
                'name' => 'Status: Pedido Entregue',
                'subject' => 'Pedido #{{order_number}} - Entregue com sucesso!',
                'body' => '<h2>Ola, {{customer_name}}!</h2>
<p>Seu pedido <strong>#{{order_number}}</strong> foi entregue!</p>
<div class="order-details">
    <p><strong>Status:</strong> <span class="status-badge status-delivered">Entregue</span></p>
    <p><strong>Pedido:</strong> #{{order_number}}</p>
    <p><strong>Total:</strong> {{order_total}}</p>
</div>
<p>Esperamos que voce aproveite sua compra!</p>
<p>Se tiver alguma duvida ou problema com o produto, nao hesite em entrar em contato conosco.</p>
<p><a href="{{order_url}}" class="button">Ver Detalhes do Pedido</a></p>
<p>Obrigado por ser nosso cliente!</p>',
                'variables' => json_encode(['customer_name', 'order_number', 'order_total', 'order_url']),
                'is_system' => 1,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code' => 'order_status_cancelled',
                'name' => 'Status: Pedido Cancelado',
                'subject' => 'Pedido #{{order_number}} - Cancelado',
                'body' => '<h2>Ola, {{customer_name}}!</h2>
<p>Informamos que seu pedido <strong>#{{order_number}}</strong> foi cancelado.</p>
<div class="order-details">
    <p><strong>Status:</strong> <span class="status-badge status-cancelled">Cancelado</span></p>
    <p><strong>Pedido:</strong> #{{order_number}}</p>
    <p><strong>Total:</strong> {{order_total}}</p>
</div>
<p>Se o pagamento ja foi efetuado, o reembolso sera processado conforme o metodo de pagamento utilizado.</p>
<p>Se voce nao solicitou o cancelamento ou tem duvidas, entre em contato conosco.</p>
<p><a href="{{order_url}}" class="button">Ver Detalhes</a></p>
<p>Esperamos ve-lo novamente em breve!</p>',
                'variables' => json_encode(['customer_name', 'order_number', 'order_total', 'order_url']),
                'is_system' => 1,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'code' => 'order_status_refunded',
                'name' => 'Status: Pedido Reembolsado',
                'subject' => 'Pedido #{{order_number}} - Reembolso Processado',
                'body' => '<h2>Ola, {{customer_name}}!</h2>
<p>Informamos que o reembolso do seu pedido <strong>#{{order_number}}</strong> foi processado.</p>
<div class="order-details">
    <p><strong>Status:</strong> <span class="status-badge status-refunded">Reembolsado</span></p>
    <p><strong>Pedido:</strong> #{{order_number}}</p>
    <p><strong>Valor:</strong> {{order_total}}</p>
</div>
<p>O valor sera creditado conforme o metodo de pagamento utilizado:</p>
<ul>
    <li><strong>Cartao de credito:</strong> O estorno aparecera na sua proxima fatura ou na seguinte.</li>
    <li><strong>PIX/Boleto:</strong> O valor sera depositado na conta informada em ate 5 dias uteis.</li>
</ul>
<p>Se tiver duvidas sobre o reembolso, entre em contato conosco.</p>
<p><a href="{{order_url}}" class="button">Ver Detalhes</a></p>
<p>Esperamos ve-lo novamente!</p>',
                'variables' => json_encode(['customer_name', 'order_number', 'order_total', 'order_url']),
                'is_system' => 1,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $db = \Config\Database::connect();

        foreach ($templates as $template) {
            // Check if template already exists
            $existing = $db->table('email_templates')
                ->where('code', $template['code'])
                ->get()
                ->getRow();

            if (!$existing) {
                $db->table('email_templates')->insert($template);
            }
        }
    }

    public function down()
    {
        $codes = [
            'order_confirmation',
            'order_status_update',
            'order_status_paid',
            'order_status_processing',
            'order_status_shipped',
            'order_status_delivered',
            'order_status_cancelled',
            'order_status_refunded',
        ];

        $db = \Config\Database::connect();
        $db->table('email_templates')
            ->whereIn('code', $codes)
            ->where('is_system', 1)
            ->delete();
    }
}
