<?= $this->extend('layouts/front') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <?= $this->include('front/customer/_sidebar') ?>
        </div>

        <!-- Content -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Pedido #<?= esc($order['order_number']) ?></h2>
                <a href="<?= base_url('minha-conta/pedidos') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Voltar
                </a>
            </div>

            <?php
            $statusColors = [
                'pending' => 'warning',
                'processing' => 'info',
                'paid' => 'success',
                'shipped' => 'primary',
                'delivered' => 'success',
                'cancelled' => 'danger',
                'refunded' => 'secondary',
            ];
            $statusNames = [
                'pending' => 'Pendente',
                'processing' => 'Em Preparacao',
                'paid' => 'Pago',
                'shipped' => 'Enviado',
                'delivered' => 'Entregue',
                'cancelled' => 'Cancelado',
                'refunded' => 'Reembolsado',
            ];
            $color = $statusColors[$order['status']] ?? 'secondary';
            $statusName = $statusNames[$order['status']] ?? $order['status'];
            ?>

            <!-- Order Status -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-1">Status do Pedido</h5>
                            <span class="badge bg-<?= $color ?> fs-6"><?= $statusName ?></span>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <small class="text-muted">Data do Pedido</small>
                            <p class="mb-0 fw-bold"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Order Items -->
                <div class="col-lg-8 mb-4">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Itens do Pedido</h5>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($order['items'])): ?>
                                <?php foreach ($order['items'] as $item): ?>
                                    <?php
                                    $itemImage = $item['image'] ?? '';
                                    if (empty($itemImage)) {
                                        $itemImageUrl = 'https://placehold.co/80x80/e9ecef/495057?text=Produto';
                                    } elseif (strpos($itemImage, 'http') === 0) {
                                        $itemImageUrl = $itemImage;
                                    } else {
                                        $itemImageUrl = base_url('uploads/products/thumbs/' . $itemImage);
                                    }
                                    ?>
                                    <div class="d-flex p-3 border-bottom">
                                        <img src="<?= esc($itemImageUrl) ?>"
                                             alt="<?= esc($item['name']) ?>"
                                             class="rounded me-3"
                                             style="width: 80px; height: 80px; object-fit: cover;"
                                             onerror="this.src='https://placehold.co/80x80/e9ecef/495057?text=Produto'">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?= esc($item['name']) ?></h6>
                                            <small class="text-muted">SKU: <?= esc($item['sku'] ?? '-') ?></small>
                                            <p class="mb-0">Qtd: <?= $item['quantity'] ?> x R$ <?= number_format($item['price'], 2, ',', '.') ?></p>
                                        </div>
                                        <div class="text-end">
                                            <strong>R$ <?= number_format(($item['subtotal'] ?? $item['price'] * $item['quantity']), 2, ',', '.') ?></strong>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <?php if (!empty($order['shipping_address'])): ?>
                        <div class="card mt-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Endereco de Entrega</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">
                                    <?= esc($order['shipping_address']['street']) ?>, <?= esc($order['shipping_address']['number']) ?>
                                    <?php if (!empty($order['shipping_address']['complement'])): ?>
                                        - <?= esc($order['shipping_address']['complement']) ?>
                                    <?php endif; ?>
                                    <br>
                                    <?= esc($order['shipping_address']['neighborhood']) ?><br>
                                    <?= esc($order['shipping_address']['city']) ?> - <?= esc($order['shipping_address']['state']) ?><br>
                                    CEP: <?= esc($order['shipping_address']['zipcode']) ?>
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Resumo</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span>R$ <?= number_format($order['subtotal'], 2, ',', '.') ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Frete (<?= esc($order['shipping_method'] ?? 'Padrao') ?>)</span>
                                <span>R$ <?= number_format($order['shipping_cost'], 2, ',', '.') ?></span>
                            </div>
                            <?php if ($order['discount'] > 0): ?>
                                <div class="d-flex justify-content-between mb-2 text-success">
                                    <span>Desconto</span>
                                    <span>- R$ <?= number_format($order['discount'], 2, ',', '.') ?></span>
                                </div>
                            <?php endif; ?>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>Total</strong>
                                <strong class="text-primary fs-5">R$ <?= number_format($order['total'], 2, ',', '.') ?></strong>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Info -->
                    <div class="card mt-3">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Pagamento</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            $paymentMethods = [
                                'pix' => 'PIX',
                                'credit_card' => 'Cartao de Credito',
                                'debit_card' => 'Cartao de Debito',
                                'boleto' => 'Boleto Bancario',
                            ];
                            ?>
                            <p class="mb-1">
                                <strong>Metodo:</strong> <?= $paymentMethods[$order['payment_method']] ?? $order['payment_method'] ?>
                            </p>
                            <?php
                            $paymentStatusColors = [
                                'pending' => 'warning',
                                'approved' => 'success',
                                'paid' => 'success',
                                'rejected' => 'danger',
                                'failed' => 'danger',
                                'refunded' => 'secondary',
                            ];
                            $paymentStatusNames = [
                                'pending' => 'Aguardando',
                                'approved' => 'Pago',
                                'paid' => 'Pago',
                                'rejected' => 'Rejeitado',
                                'failed' => 'Falhou',
                                'refunded' => 'Reembolsado',
                            ];
                            $pColor = $paymentStatusColors[$order['payment_status']] ?? 'secondary';
                            $pName = $paymentStatusNames[$order['payment_status']] ?? $order['payment_status'];
                            ?>
                            <p class="mb-0">
                                <strong>Status:</strong> <span class="badge bg-<?= $pColor ?>"><?= $pName ?></span>
                            </p>
                        </div>
                    </div>

                    <!-- Tracking -->
                    <?php if (!empty($order['tracking_code'])): ?>
                        <div class="card mt-3">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="bi bi-truck me-2"></i>Rastreamento</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-1"><strong>Codigo:</strong> <?= esc($order['tracking_code']) ?></p>
                                <a href="https://www.melhorrastreio.com.br/app/correios/<?= esc($order['tracking_code']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>Rastrear
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
