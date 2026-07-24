<?= $this->extend('layouts/front') ?>

<?= $this->section('content') ?>

<?php
// Google Analytics 4 + Google Ads Conversion Tracking
helper('google_tracking');

// Verificar se a conversao ja foi enviada
$tracking = google_tracking();
$orderId = (int) ($order['id'] ?? 0);
$conversionSent = $orderId > 0 && $tracking->isConversionLogged($orderId);

// Preparar dados do cliente para Enhanced Conversions
$customerData = [];
if (!empty($customer)) {
    $customerData = [
        'email' => $customer['email'] ?? '',
        'phone' => $customer['phone'] ?? '',
        'name' => $customer['name'] ?? '',
        'city' => $order['shipping_city'] ?? '',
        'state' => $order['shipping_state'] ?? '',
        'zipcode' => $order['shipping_zipcode'] ?? '',
    ];
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    </div>

                    <h2 class="mb-3">Pagamento Confirmado!</h2>
                    <p class="text-muted mb-4">
                        Seu pedido <strong>#<?= esc($order['order_number']) ?></strong> foi confirmado com sucesso!
                        <br>Voce recebera um e-mail com os detalhes do pedido.
                    </p>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-4">
                            <div class="border rounded p-3">
                                <small class="text-muted">Numero do Pedido</small>
                                <h5 class="mb-0">#<?= esc($order['order_number']) ?></h5>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="border rounded p-3">
                                <small class="text-muted">Total</small>
                                <h5 class="mb-0 text-primary">R$ <?= number_format($order['total'], 2, ',', '.') ?></h5>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="border rounded p-3">
                                <small class="text-muted">Status</small>
                                <h5 class="mb-0 text-success">Aprovado</h5>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($order['items'])): ?>
                    <div class="card bg-light mb-4">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-bag me-2"></i>Itens do Pedido</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Produto</th>
                                            <th class="text-center">Qtd</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($order['items'] as $item): ?>
                                        <tr>
                                            <td>
                                                <?= esc($item['name']) ?>
                                                <?php if (!empty($item['attributes'])): ?>
                                                    <br><small class="text-muted"><?= esc($item['attributes']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center"><?= $item['quantity'] ?></td>
                                            <td class="text-end">R$ <?= number_format($item['price'] * $item['quantity'], 2, ',', '.') ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="mb-3"><i class="bi bi-truck me-2"></i>Endereco de Entrega</h6>
                            <p class="mb-0">
                                <?= esc($order['shipping_street']) ?>, <?= esc($order['shipping_number']) ?>
                                <?php if (!empty($order['shipping_complement'])): ?>
                                    - <?= esc($order['shipping_complement']) ?>
                                <?php endif; ?>
                                <br>
                                <?= esc($order['shipping_neighborhood']) ?> - <?= esc($order['shipping_city']) ?>/<?= esc($order['shipping_state']) ?>
                                <br>
                                CEP: <?= esc($order['shipping_zipcode']) ?>
                            </p>
                        </div>
                    </div>

                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle me-2"></i>
                        Seu pedido sera processado e enviado em breve. Voce pode acompanhar o status na sua area de cliente.
                    </div>

                    <div class="d-flex justify-content-center gap-3">
                        <a href="<?= base_url('minha-conta/pedidos/' . $order['order_number']) ?>" class="btn btn-primary">
                            <i class="bi bi-eye me-2"></i>Ver Detalhes do Pedido
                        </a>
                        <a href="<?= base_url() ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-house me-2"></i>Continuar Comprando
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php if (!$conversionSent): ?>
<!-- GA4 Purchase + Google Ads Conversion -->
<?php
// Registrar conversao no banco
if ($orderId > 0) {
    $tracking->logConversion($orderId, $order['order_number'] ?? (string) $orderId);
}

// Preparar Enhanced Conversions user data
$userData = [];
if (!empty($customerData)) {
    $userData = $tracking->prepareUserData($customerData);
}
$userDataJson = !empty($userData) ? json_encode($userData, JSON_UNESCAPED_UNICODE) : 'null';

// Preparar itens
$items = [];
foreach ($order['items'] ?? [] as $index => $item) {
    $items[] = [
        'item_id' => $item['sku'] ?? $item['product_id'] ?? '',
        'item_name' => $item['name'] ?? '',
        'price' => (float) ($item['price'] ?? 0),
        'quantity' => (int) ($item['quantity'] ?? 1),
        'index' => $index,
    ];
}
$itemsJson = json_encode($items, JSON_UNESCAPED_UNICODE);

$googleConfig = google_config();
?>
<script>
(function() {
    // Verificar se ja foi enviado (cookie de protecao)
    var cookieName = 'gps_conversion_<?= $orderId ?>';
    if (document.cookie.indexOf(cookieName + '=1') !== -1) {
        console.log('GPS Imports: Conversion already sent for order <?= $orderId ?>');
        return;
    }

    // Marcar como enviado (cookie expira em 1 dia)
    document.cookie = cookieName + '=1; max-age=86400; path=/; SameSite=Strict';

    <?php if (!empty($userData)): ?>
    // Enhanced Conversions - User Data (hashed)
    gtag('set', 'user_data', <?= $userDataJson ?>);
    <?php endif; ?>

    // GA4 - Purchase Event
    gtag('event', 'purchase', {
        transaction_id: '<?= esc($order['order_number'] ?? $order['id'], 'js') ?>',
        value: <?= (float) ($order['total'] ?? 0) ?>,
        currency: 'BRL',
        tax: <?= (float) ($order['tax'] ?? 0) ?>,
        shipping: <?= (float) ($order['shipping_cost'] ?? 0) ?>,
        coupon: '<?= esc($order['coupon_code'] ?? '', 'js') ?>',
        items: <?= $itemsJson ?>
    });

    <?php if ($googleConfig->hasGoogleAds() && !empty($googleConfig->adsConversionLabel)): ?>
    // Google Ads - Conversion Event
    gtag('event', 'conversion', {
        'send_to': '<?= $googleConfig->adsConversionId ?>/<?= $googleConfig->adsConversionLabel ?>',
        'value': <?= (float) ($order['total'] ?? 0) ?>,
        'currency': 'BRL',
        'transaction_id': '<?= esc($order['order_number'] ?? $order['id'], 'js') ?>'
    });
    <?php endif; ?>

    console.log('GPS Imports: Purchase conversion sent for order <?= esc($order['order_number'] ?? $order['id'], 'js') ?>');
})();
</script>
<?php else: ?>
<!-- Conversion already logged for order <?= $orderId ?> -->
<?php endif; ?>
<?= $this->endSection() ?>
