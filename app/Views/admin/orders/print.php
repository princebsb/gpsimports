<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido #<?= esc($order['order_number']) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .order-info {
            text-align: right;
        }

        .order-number {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ccc;
        }

        .row {
            display: flex;
            gap: 30px;
        }

        .col {
            flex: 1;
        }

        .address-box {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
        }

        .address-box strong {
            display: block;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f5f5f5;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals {
            margin-top: 20px;
        }

        .totals table {
            width: 300px;
            margin-left: auto;
        }

        .totals td {
            padding: 5px 10px;
        }

        .totals .total-row {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #333;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-pending { background: #ffc107; color: #000; }
        .badge-paid { background: #28a745; color: #fff; }
        .badge-shipped { background: #17a2b8; color: #fff; }
        .badge-delivered { background: #28a745; color: #fff; }
        .badge-cancelled { background: #dc3545; color: #fff; }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .print-button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">Imprimir</button>

    <div class="header">
        <div class="logo">
            <?= esc(setting('store_name') ?? 'GPS Imports') ?>
        </div>
        <div class="order-info">
            <div class="order-number">Pedido #<?= esc($order['order_number']) ?></div>
            <div>Data: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></div>
            <div>
                <?php
                $statusLabel = match($order['status'] ?? 'pending') {
                    'pending' => 'Pendente',
                    'processing', 'paid' => 'Processando',
                    'shipped' => 'Enviado',
                    'delivered' => 'Entregue',
                    'cancelled' => 'Cancelado',
                    default => ucfirst($order['status'] ?? 'Pendente')
                };
                $statusClass = match($order['status'] ?? 'pending') {
                    'pending' => 'pending',
                    'processing', 'paid' => 'paid',
                    'shipped' => 'shipped',
                    'delivered' => 'delivered',
                    'cancelled' => 'cancelled',
                    default => 'pending'
                };
                ?>
                <span class="badge badge-<?= $statusClass ?>"><?= $statusLabel ?></span>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="row">
            <div class="col">
                <div class="section-title">Cliente</div>
                <div class="address-box">
                    <?php $customer = $order['customer'] ?? []; ?>
                    <strong><?= esc($customer['name'] ?? 'N/A') ?></strong>
                    <?= esc($customer['email'] ?? '') ?><br>
                    <?php if (!empty($customer['phone']) || !empty($customer['mobile'])): ?>
                        <?= esc($customer['phone'] ?? $customer['mobile'] ?? '') ?><br>
                    <?php endif; ?>
                    <?php if (!empty($customer['cpf'])): ?>
                        CPF: <?= esc($customer['cpf']) ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col">
                <div class="section-title">Endereco de Entrega</div>
                <div class="address-box">
                    <strong><?= esc($order['shipping_name'] ?? '') ?></strong>
                    <?= esc($order['shipping_street'] ?? '') ?>, <?= esc($order['shipping_number'] ?? '') ?>
                    <?php if (!empty($order['shipping_complement'])): ?>
                        - <?= esc($order['shipping_complement']) ?>
                    <?php endif; ?>
                    <br>
                    <?= esc($order['shipping_neighborhood'] ?? '') ?><br>
                    <?= esc($order['shipping_city'] ?? '') ?> - <?= esc($order['shipping_state'] ?? '') ?><br>
                    CEP: <?= esc($order['shipping_zipcode'] ?? '') ?>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Itens do Pedido</div>
        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>SKU</th>
                    <th class="text-center">Qtd</th>
                    <th class="text-right">Preco Unit.</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($order['items'])): ?>
                    <?php foreach ($order['items'] as $item): ?>
                        <tr>
                            <td>
                                <?= esc($item['name'] ?? 'Produto') ?>
                                <?php if (!empty($item['attributes'])): ?>
                                    <br><small style="color: #666;"><?= esc($item['attributes']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($item['sku'] ?? '-') ?></td>
                            <td class="text-center"><?= $item['quantity'] ?></td>
                            <td class="text-right">R$ <?= number_format($item['price'], 2, ',', '.') ?></td>
                            <td class="text-right">R$ <?= number_format($item['subtotal'] ?? ($item['price'] * $item['quantity']), 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">R$ <?= number_format($order['subtotal'] ?? 0, 2, ',', '.') ?></td>
                </tr>
                <?php if (($order['discount'] ?? 0) > 0): ?>
                    <tr>
                        <td>Desconto:</td>
                        <td class="text-right">- R$ <?= number_format($order['discount'], 2, ',', '.') ?></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td>Frete (<?= esc($order['shipping_method'] ?? 'N/A') ?>):</td>
                    <td class="text-right">R$ <?= number_format($order['shipping_cost'] ?? 0, 2, ',', '.') ?></td>
                </tr>
                <tr class="total-row">
                    <td><strong>Total:</strong></td>
                    <td class="text-right"><strong>R$ <?= number_format($order['total'] ?? 0, 2, ',', '.') ?></strong></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="row">
            <div class="col">
                <div class="section-title">Pagamento</div>
                <?php
                $paymentMethod = $order['payment_method'] ?? 'N/A';
                $methodLabel = match($paymentMethod) {
                    'credit_card' => 'Cartao de Credito',
                    'debit_card' => 'Cartao de Debito',
                    'pix' => 'PIX',
                    'boleto' => 'Boleto',
                    default => ucfirst($paymentMethod)
                };
                ?>
                <p><strong>Metodo:</strong> <?= $methodLabel ?></p>
                <?php if (($order['installments'] ?? 1) > 1): ?>
                    <p><strong>Parcelas:</strong> <?= $order['installments'] ?>x</p>
                <?php endif; ?>
            </div>
            <?php if (!empty($order['tracking_code'])): ?>
                <div class="col">
                    <div class="section-title">Rastreamento</div>
                    <p><strong>Codigo:</strong> <?= esc($order['tracking_code']) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($order['notes'])): ?>
        <div class="section">
            <div class="section-title">Observacoes</div>
            <p><?= nl2br(esc($order['notes'])) ?></p>
        </div>
    <?php endif; ?>

    <div class="footer">
        <p><?= esc(setting('store_name') ?? 'GPS Imports') ?> - <?= esc(setting('email') ?? '') ?></p>
        <p>Documento gerado em <?= date('d/m/Y H:i') ?></p>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
